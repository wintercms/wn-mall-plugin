# WishlistButton

The `wishlistButton` component displays a `Add to wishlist` button.

The button includes a simple popup where the user can choose a specific
wishlist or create a new one.

## Properties

It's best to use the properties directly from the currently viewed product or variant, see code example below.

## `product` (int)
The ID of the product to be added to the wishlist.

## `variant` (int|null)
The ID of the variant to be added to the wishlist. Can be `null`.

## Styling

Take a look
at [the relevant SCSS files in our demo repository](https://github.com/wintercms/wn-mall-theme/blob/master/resources/scss/mall/wishlists.scss)
to get an idea on how to style this component.
<!-- TODO: Update link to the relevant SCSS files when the demo theme is finalized -->

## Events

This component emits a `mall.wishlist.productAdded` JavaScript event when 
a product was added to a wishlist. You can subscribe to this event
and receive the added product's information.

```js
$(function () {
    $.subscribe('mall.wishlist.productAdded', function (data) {
        console.log('product added to wishlist', data)
    });
});
``` 

## Example implementations

### Display the wishlist button

If you place this button inside the [Product component](./product.md)
you can reference the `item` variable to access the currenlty viewed product/variant.

Checkout [the source of the demo website](https://github.com/wintercms/wn-mall-theme/blob/master/partials/product/belowcartbutton.htm) 
to see an example implementation.
<!-- TODO: Update link to the relevant SCSS files when the demo theme is finalized -->

```twig
[wishlistButton]
==
{%  component 'wishlistButton'
    product=item.product_id
    variant=item.variant_id
%}
```