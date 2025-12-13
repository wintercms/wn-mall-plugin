<?php

namespace Winter\Mall\Classes\Events;

use Backend\Facades\Backend;
use Cms\Classes\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Winter\Storm\Mail\Mailable;
use Winter\Mall\Classes\Jobs\SendOrderConfirmationToCustomer;
use Winter\Mall\Classes\PaymentState\FailedState;
use Winter\Mall\Classes\PaymentState\PaidState;
use Winter\Mall\Classes\PaymentState\RefundedState;
use Winter\Mall\Models\GeneralSettings;
use Winter\Mall\Models\Notification;
use Winter\Mall\Models\Order;
use Winter\Translate\Classes\Translator;
use Winter\User\Models\Settings as UserSettings;

class MailingEventHandler
{
    public $enabledNotifications = [];

    public function __construct()
    {
        $this->enabledNotifications = Notification::getEnabled();
    }

    /**
     * Subscribe conditionally to all relevant mall events.
     *
     * @param $events
     */
    public function subscribe($events)
    {
        $eventMap = [
            'winter.mall::customer.created'    => [
                'event'   => 'mall.customer.afterSignup',
                'handler' => 'MailingEventHandler@customerCreated',
            ],
            'winter.mall::order.state.changed' => [
                'event'   => 'mall.order.state.changed',
                'handler' => 'MailingEventHandler@orderStateChanged',
            ],
            'winter.mall::order.shipped'       => [
                'event'   => 'mall.order.shipped',
                'handler' => 'MailingEventHandler@orderShipped',
            ],
        ];

        foreach ($eventMap as $notification => $data) {
            if ($this->enabledNotifications->has($notification)) {
                $events->listen($data['event'], $data['handler']);
            }
        }

        $events->listen('mall.order.payment_state.changed', 'MailingEventHandler@orderPaymentStateChanged');
        $events->listen('mall.checkout.succeeded', 'MailingEventHandler@checkoutSucceeded');
        $events->listen('mall.checkout.failed', 'MailingEventHandler@checkoutFailed');
    }

    /**
     * A customer has signed up.
     *
     * @param $user
     *
     * @throws \Cms\Classes\CmsException
     */
    public function customerCreated($handler, $user)
    {
        // Don't mail guest accounts.
        if ($user->customer->is_guest) {
            return;
        }

        $needsConfirmation = UserSettings::get('activate_mode') === UserSettings::ACTIVATE_USER;
        $confirmCode       = implode('!', [$user->id, $user->getActivationCode()]);
        $confirmUrl        = $this->getAccountUrl('confirmation') . '?code=' . $confirmCode;

        $data = [
            'user'         => $user,
            'confirm'      => $needsConfirmation,
            'confirm_url'  => $confirmUrl,
            'confirm_code' => $confirmCode,
        ];

        Mail::queue($this->template('winter.mall::customer.created'), $data, function ($message) use ($user) {
            if (class_exists(Translator::class) && method_exists($message, 'locale')) {
                $message->locale(Translator::instance()->getLocale());
            }
            $message->to($user->email, $user->customer->name);
        });
    }

    /**
     * A checkout was successful.
     *
     * @param $result
     *
     * @throws \Cms\Classes\CmsException
     */
    public function checkoutSucceeded($result)
    {
        // Notify the customer
        if ($this->enabledNotifications->has('winter.mall::checkout.succeeded')) {
            $input = [
                'id'          => $result->order->id,
                'template'    => $this->template('winter.mall::checkout.succeeded'),
                'account_url' => $this->getAccountUrl(),
                'order_url'   => $this->getBackendOrderUrl($result->order),
            ];
            // Push the PDF generation and mail send call to the queue.
            Queue::push(SendOrderConfirmationToCustomer::class, $input);
        }

        // Notify the admin
        if (
            $this->enabledNotifications->has('winter.mall::admin.checkout_succeeded')
            && $adminMail = GeneralSettings::get('admin_email')
        ) {
            $data = [
                'order'       => $result->order->fresh(['products', 'customer']),
                'account_url' => $this->getAccountUrl(),
                'order_url'   => $this->getBackendOrderUrl($result->order),
            ];
            Mail::queue(
                $this->template('winter.mall::admin.checkout_succeeded'), $data,
                function ($message) use ($adminMail) {
                    $message->to($adminMail);
                });
        }
    }

    /**
     * A checkout has failed.
     *
     * @param $result
     *
     * @throws \Cms\Classes\CmsException
     */
    public function checkoutFailed($result)
    {
        $data = [
            'order'       => $result->order->fresh(['products', 'customer']),
            'account_url' => $this->getAccountUrl(),
            'order_url'   => $this->getBackendOrderUrl($result->order),
        ];

        // Notify the customer
        if ($this->enabledNotifications->has('winter.mall::checkout.failed')) {
            Mail::queue($this->template('winter.mall::checkout.failed'), $data, function ($message) use ($result, $data) {
                $this->handleLocale($message, $data['order']);
                $message->to($result->order->customer->user->email, $result->order->customer->name);
            });
        }

        // Notify the admin
        if (
            $this->enabledNotifications->has('winter.mall::admin.checkout_failed')
            && $adminMail = GeneralSettings::get('admin_email')
        ) {
            Mail::queue($this->template('winter.mall::admin.checkout_failed'), $data,
                function ($message) use ($adminMail) {
                    $message->to($adminMail);
                });
        }
    }

    /**
     * The state of an order has changed.
     *
     * @param $order
     */
    public function orderStateChanged($order)
    {
        if ( ! $order->stateNotification) {
            return;
        }

        $data = [
            'order' => $order->load(['order_state']),
            'account_url' => $this->getAccountUrl(),
        ];

        Mail::queue($this->template('winter.mall::order.state.changed'), $data, function ($message) use ($order) {
            $this->handleLocale($message, $order);
            $message->to($order->customer->user->email, $order->customer->name);
        });
    }


    /**
     * The order has been shipped.
     *
     * @param $order
     *
     * @throws \Cms\Classes\CmsException
     */
    public function orderShipped($order)
    {
        if ( ! $order->shippingNotification) {
            return;
        }

        $data = [
            'order'       => $order->load(['order_state']),
            'account_url' => $this->getAccountUrl(),
        ];

        Mail::queue($this->template('winter.mall::order.shipped'), $data, function ($message) use ($order) {
            $this->handleLocale($message, $order);
            $message->to($order->customer->user->email, $order->customer->name);
        });
    }

    /**
     * The payment state of an order has changed.
     * Depending on the new order state a different mail template will be used.
     *
     * @param $order
     *
     * @throws \Cms\Classes\CmsException
     */
    public function orderPaymentStateChanged($order)
    {
        $attr = 'payment_state';
        if ( ! $order->isDirty($attr) || $order->getOriginal($attr) === $order->getAttribute($attr)) {
            return;
        }

        switch ($order->getAttribute($attr)) {
            case FailedState::class:
                $view = 'failed';
                break;
            case PaidState::class:
                $view = 'paid';
                break;
            case RefundedState::class:
                $view = 'refunded';
                break;
            default:
                // The customer is not informed about any other state.
                return;
        }

        // This notification is disabled.
        if ( ! $this->enabledNotifications->has('winter.mall::payment.' . $view)) {
            return;
        }

        $data = [
            'order'       => $order->load(['order_state', 'payment_logs']),
            'account_url' => $this->getAccountUrl(),
        ];

        Mail::queue(
            $this->template('winter.mall::payment.' . $view),
            $data,
            function ($message) use ($order) {
                $this->handleLocale($message, $order);
                $message->to($order->customer->user->email, $order->customer->name);
            }
        );

        // Notify the admin about succeeded payments.
        $failedBecamePaid = $order->getOriginal($attr) === FailedState::class && $order->getAttribute($attr) === PaidState::class;

        if ($failedBecamePaid && $adminMail = GeneralSettings::get('admin_email')) {
            Mail::queue('winter.mall::mail.admin.payment_paid', $data,
                function ($message) use ($adminMail) {
                    $message->to($adminMail);
                });
        }
    }

    /**
     * Return the user defined mail template for a given event code.
     *
     * @return string
     */
    protected function template($code)
    {
        return $this->enabledNotifications->get($code);
    }

    /**
     * Return the direct URL to a customer's account page.
     *
     * @param string $page
     *
     * @return string
     * @throws \Cms\Classes\CmsException
     */
    protected function getAccountUrl($page = 'orders'): string
    {
        $controller = Controller::getController() ?: new Controller;
        return $controller->pageUrl(
            GeneralSettings::get('account_page'), ['page' => $page]
        );
    }

    /**
     * Returns the direct URL to the order details.
     *
     * @param $order
     *
     * @return string
     */
    protected function getBackendOrderUrl($order): string
    {
        return Backend::url('winter/mall/orders/show/' . $order->id);
    }

    /**
     * Set the locale on the message, based on the order.
     *
     * @param Mailable $message
     * @param Order $order
     */
    protected function handleLocale(Mailable $message, Order $order)
    {
        if ($order->lang && method_exists($message, 'locale')) {
            $message->locale($order->lang);
        }
    }
}
