# MyAccount

The `MyAccount` component displays an overview of the user's account.

It pulls together the [OrdersList](./orders-list.md), [CustomerProfile](./customer-profile.md) and [AddressList](
./address-list.md) components. 

By changing the `page` property a different child component is displayed.

## Properties

### `page` (string)

The currently active page. This property should be populated via URL parameters. Possible values are `orders`, 
`profile` and `addresses`.

<!-- TODO: Add more properties if necessary
### `order` (string, optional)

The hashed ID of an order. This will automatically open the order details in the overview.
-->

## Example implementations

### Display the my account page

```twig{9-10,16}
title = "Account"
url = "/account/:page?/:order?"

[session]
security = "user"
redirect = "login"

<!-- [!code focus:2] -->
[myAccount]
page = "{{ :page }}"
==
<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- [!code focus:1] -->
            {% component 'myAccount' %}
        </div>
    </div>
</div>
```