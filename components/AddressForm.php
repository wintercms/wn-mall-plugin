<?php namespace Winter\Mall\Components;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Winter\Storm\Support\Facades\Flash;
use Winter\Mall\Models\Address;
use Winter\Mall\Models\Cart;
use Winter\Mall\Models\GeneralSettings;
use Winter\Location\Models\Country;
use Winter\User\Facades\Auth;

/**
 * The AddressForm component displays a form to edit an address.
 */
class AddressForm extends MallComponent
{
    /**
     * The address model.
     *
     * @var Address
     */
    public $address;
    /**
     * A list of all available countries.
     *
     * @var \Illuminate\Support\Collection
     */
    public $countries;
    /**
     * If this address is used as "billing" or "shipping" address.
     *
     * @var string
     */
    public $setAddressAs;
    /**
     * The user's cart.
     *
     * @var Cart
     */
    public $cart;
    /**
     * Use state field.
     *
     * @var boolean
     */
    public $useState = true;

    /**
     * Component details.
     *
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'winter.mall::lang.components.addressForm.details.name',
            'description' => 'winter.mall::lang.components.addressForm.details.description',
        ];
    }

    /**
     * Properties of this component.
     *
     * @return array
     */
    public function defineProperties()
    {
        return [
            'address'  => [
                'type'  => 'dropdown',
                'title' => 'winter.mall::lang.components.addressForm.properties.address.title',
            ],
            'redirect' => [
                'type'  => 'dropdown',
                'title' => 'winter.mall::lang.components.addressForm.properties.redirect.title',
            ],
            'set'      => [
                'type'  => 'dropdown',
                'title' => 'winter.mall::lang.components.addressForm.properties.set.title',
            ],
        ];
    }

    /**
     * Options array for the address options dropdown.
     *
     * @return array
     */
    public function getAddressOptions()
    {
        return Address::get()->pluck('name', 'id')->toArray();
    }

    /**
     * Options array for the redirect options dropdown.
     *
     * @return array
     */
    public function getRedirectOptions()
    {
        return [
            'checkout' => trans('winter.mall::lang.components.addressForm.redirects.checkout'),
            'account'  => trans('winter.mall::lang.components.addressForm.redirects.account'),
        ];
    }

    /**
     * Options array for the "set as" dropdown.
     *
     * @return array
     */
    public function getSetOptions()
    {
        return [
            null       => trans('winter.mall::lang.common.not_in_use'),
            'billing'  => trans('winter.mall::lang.components.addressForm.set.billing'),
            'shipping' => trans('winter.mall::lang.components.addressForm.set.shipping'),
        ];
    }

    /**
     * This method sets all variables needed for this component to work.
     *
     * @return bool
     */
    public function setData()
    {
        $user = Auth::getUser();
        if ( ! $user) {
            return false;
        }

        $this->setVar('setAddressAs', $this->property('set'));
        $this->setVar('cart', Cart::byUser(Auth::getUser()));
        $this->setVar('countries', Country::getNameList());
        $this->setVar('useState', GeneralSettings::get('use_state', true));

        $hashId = $this->property('address');
        if ($hashId === 'new') {
            return true;
        }

        $id = $this->decode($hashId);
        try {
            $this->setVar('address', Address::byCustomer($user->customer)->findOrFail($id));
        } catch (ModelNotFoundException $e) {
            return false;
        }

        return true;
    }

    /**
     * The component is executed.
     *
     * @return string|void
     */
    public function onRun()
    {
        if ( ! $this->setData()) {
            return $this->controller->run('404');
        }
    }

    /**
     * The user submitted the edit form.
     *
     * @return string|void
     */
    public function onSubmit()
    {
        $this->setData();
        $user = Auth::getUser();
        if ( ! $user) {
            return $this->controller->run('404');
        }

        $data  = post();
        $isNew = $this->property('address') === 'new';

        if ($isNew) {
            $this->address              = new Address();
            $this->address->customer_id = $user->customer->id;
        }

        $this->address->fill($data);
        $this->address->name = $data['address_name'];
        $this->address->save();

        if (in_array($this->setAddressAs, ['billing', 'shipping', 'both'])) {
            if ($this->setAddressAs === 'both') {
                $this->cart->billing_address_id  = $this->address->id;
                $this->cart->shipping_address_id = $this->address->id;
            } else {
                $this->cart->{$this->setAddressAs . '_address_id'} = $this->address->id;
            }
            $this->cart->save();
        }

        if ($user->customer->default_shipping_address_id === null) {
            $user->customer->default_shipping_address_id = $this->address->id;
        }
        if ($user->customer->default_billing_address_id === null) {
            $user->customer->default_billing_address_id = $this->address->id;
        }
        $user->customer->save();

        Flash::success(trans('winter.mall::lang.common.saved_changes'));

        if ($url = $this->getRedirectUrl()) {
            return redirect()->to(url($url));
        }

        return null;
    }

    /**
     * Get the redirect url.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        switch ($this->property('redirect')) {
            case 'payment':
                return $this->controller->pageUrl(GeneralSettings::get('checkout_page'), ['page' => 'payment']);
            case 'quickCheckout':
                return $this->controller->pageUrl(GeneralSettings::get('checkout_page'), ['step' => 'overview']);
            case 'account':
                return $this->controller->pageUrl(GeneralSettings::get('account_page'), ['page' => 'addresses']);
            default:
                return $this->controller->pageUrl(GeneralSettings::get('checkout_page'), ['step' => 'confirm']);
        }
    }
}
