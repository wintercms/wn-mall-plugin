<?php

namespace Winter\Mall\Classes\Registration;

trait BootMails
{

    public function registerMailTemplates()
    {
        return [
            'winter.mall::mail.customer.created',
            'winter.mall::mail.order.state_changed',
            'winter.mall::mail.order.shipped',
            'winter.mall::mail.checkout.succeeded',
            'winter.mall::mail.checkout.failed',
            'winter.mall::mail.payment.failed',
            'winter.mall::mail.payment.paid',
            'winter.mall::mail.payment.refunded',
            'winter.mall::mail.admin.checkout_succeeded',
            'winter.mall::mail.admin.checkout_failed',
            'winter.mall::mail.admin.payment_paid',
        ];
    }

    public function registerMailPartials()
    {
        return [
            'mall.order.table'         => 'winter.mall::mail._partials.order.table',
            'mall.order.tracking'      => 'winter.mall::mail._partials.order.tracking',
            'mall.order.addresses'     => 'winter.mall::mail._partials.order.addresses',
            'mall.order.payment_state' => 'winter.mall::mail._partials.order.payment_state',
            'mall.customer.address'    => 'winter.mall::mail._partials.customer.address',
        ];
    }
}
