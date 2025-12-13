<?php namespace Winter\Mall\Components;

use Auth;
use Winter\Storm\Exception\ValidationException;
use Winter\Storm\Support\Facades\Flash;
use Winter\Mall\Models\Cart;

/**
 * The DiscountApplier component allow the user to enter a discount code.
 */
class DiscountApplier extends MallComponent
{
    /**
     * Component details.
     *
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'winter.mall::lang.components.discountApplier.details.name',
            'description' => 'winter.mall::lang.components.discountApplier.details.description',
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
            'discountCodeLimit' => [
                'type'    => 'string',
                'title'   => 'winter.mall::lang.components.cart.properties.discountCodeLimit.title',
                'description' => 'winter.mall::lang.components.cart.properties.discountCodeLimit.description',
                'default' => 0,
            ],
        ];
    }

    /**
     * A discount code has been entered.
     *
     * Applies the discount code directly to the Cart model.
     *
     * @throws ValidationException
     */
    public function onApplyDiscount()
    {
        $code = strtoupper(post('code'));
        $cart = Cart::byUser(Auth::getUser());

        try {
            $cart->applyDiscountByCode($code, (int)$this->property('discountCodeLimit'));
        } catch (\Throwable $e) {
            throw new ValidationException([
                'code' => $e->getMessage(),
            ]);
        }

        Flash::success(trans('winter.mall::lang.components.discountApplier.discount_applied'));
    }
}
