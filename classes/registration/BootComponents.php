<?php

namespace Winter\Mall\Classes\Registration;

use Winter\Mall\Components\AddressForm;
use Winter\Mall\Components\AddressList;
use Winter\Mall\Components\AddressSelector;
use Winter\Mall\Components\Cart;
use Winter\Mall\Components\Checkout;
use Winter\Mall\Components\CurrencyPicker;
use Winter\Mall\Components\CustomerProfile;
use Winter\Mall\Components\DiscountApplier;
use Winter\Mall\Components\EnhancedEcommerceAnalytics;
use Winter\Mall\Components\MallDependencies;
use Winter\Mall\Components\MyAccount;
use Winter\Mall\Components\OrdersList;
use Winter\Mall\Components\PaymentMethodSelector;
use Winter\Mall\Components\Product as ProductComponent;
use Winter\Mall\Components\ProductReviews;
use Winter\Mall\Components\Products as ProductsComponent;
use Winter\Mall\Components\ProductsFilter;
use Winter\Mall\Components\QuickCheckout;
use Winter\Mall\Components\ShippingMethodSelector;
use Winter\Mall\Components\SignUp;
use Winter\Mall\Components\WishlistButton;
use Winter\Mall\Components\Wishlists;
use Winter\Mall\FormWidgets\Price;
use Winter\Mall\FormWidgets\PropertyFields;

trait BootComponents
{
    public function registerComponents()
    {
        return [
            Cart::class                       => 'cart',
            SignUp::class                     => 'signUp',
            ShippingMethodSelector::class     => 'shippingMethodSelector',
            AddressSelector::class            => 'addressSelector',
            AddressForm::class                => 'addressForm',
            PaymentMethodSelector::class      => 'paymentMethodSelector',
            Checkout::class                   => 'checkout',
            QuickCheckout::class              => 'quickCheckout',
            ProductsComponent::class          => 'products',
            ProductsFilter::class             => 'productsFilter',
            ProductComponent::class           => 'product',
            DiscountApplier::class            => 'discountApplier',
            MyAccount::class                  => 'myAccount',
            OrdersList::class                 => 'ordersList',
            CustomerProfile::class            => 'customerProfile',
            AddressList::class                => 'addressList',
            CurrencyPicker::class             => 'currencyPicker',
            MallDependencies::class           => 'mallDependencies',
            EnhancedEcommerceAnalytics::class => 'enhancedEcommerceAnalytics',
            Wishlists::class                  => 'wishlists',
            WishlistButton::class             => 'wishlistButton',
            ProductReviews::class             => 'productReviews',
        ];
    }

    public function registerFormWidgets()
    {
        return [
            PropertyFields::class => 'mall.propertyfields',
            Price::class          => 'mall.price',
        ];
    }
}
