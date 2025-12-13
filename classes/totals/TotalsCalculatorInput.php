<?php

namespace Winter\Mall\Classes\Totals;


use Illuminate\Support\Collection;
use Winter\Mall\Models\Cart;
use Winter\Mall\Models\Discount;
use Winter\Mall\Models\PaymentMethod;
use Winter\Mall\Models\Product;
use Winter\Mall\Models\ShippingMethod;
use Winter\Mall\Models\Wishlist;

class TotalsCalculatorInput
{
    /**
     * @var Collection<Product>
     */
    public $products;
    /**
     * @var ShippingMethod
     */
    public $shipping_method;
    /**
     * @var Collection<Discount>
     */
    public $discounts;
    /**
     * @var PaymentMethod
     */
    public $payment_method;
    /**
     * @var int
     */
    public $shipping_country_id;

    /**
     * Create an instance from a Cart model.
     *
     * @param Cart $cart
     *
     * @return TotalsCalculatorInput
     */
    public static function fromCart(Cart $cart)
    {
        $cart->loadMissing(
            'products',
            'products.data.taxes',
            'shipping_method',
            'shipping_method.taxes.countries',
            'shipping_method.rates',
            'discounts'
        );

        $input                      = new self();
        $input->products            = $cart->products;
        $input->shipping_method     = $cart->shipping_method;
        $input->payment_method      = $cart->payment_method;
        $input->discounts           = $cart->discounts;
        $input->shipping_country_id = optional(optional($cart)->shipping_address)->country_id;

        return $input;
    }

    public static function fromWishlist(Wishlist $wishlist)
    {
        $wishlist->loadMissing('items.data.taxes');

        $input                      = new self();
        $input->products            = $wishlist->items;
        $input->discounts           = new Collection();
        $input->shipping_method     = $wishlist->shipping_method;
        $input->shipping_country_id = $wishlist->getCartCountryId();

        return $input;
    }
}