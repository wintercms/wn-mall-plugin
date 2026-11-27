# Wishlists

The `wishlists` component displays all wishlists of the currently
logged in customer.

## Properties

### `showShipping` (bool)

Show a shipping method selector along with the Wishlist. Shipping cost will be included in the totals.


## Styling

Take a look
at [the relevant SCSS files in our demo repository](https://github.com/wintercms/wn-mall-theme/blob/master/resources/scss/mall/wishlists.scss)
to get an idea on how to style this component.
<!-- TODO: Update link to the relevant SCSS files when the demo theme is finalized -->

## Events

This component emits a `mall.wishlist.productRemoved` JavaScript event when 
a product was removed from a wishlist. You can subscribe to this event
and receive the added product's information.

```js
$(function () {
    $.subscribe('mall.wishlist.productRemoved', function (data) {
        console.log('product removed from wishlist', data)
    });
});
``` 


## Example implementations

### Display the wishlists manager

```twig{6,12}
url = "/wishlists"
title = "Wishlists"
layout = "default"

<!-- [!code focus:1] -->
[wishlists]
==
<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- [!code focus:1] -->
            {% component 'wishlists' %}
        </div>
    </div>
</div>
```