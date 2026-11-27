# AddressSelector

The `AddressSelector` component displays an address that belongs to the user's cart. 
It gives the user the ability to edit it or select a different address.

This component is part of the [Checkout component](./checkout.md) and is used 
to display the currently active billing and shipping Address on the checkout page. 

## Properties

### `type` (string)

Display the `billing` or `shipping` address of the user's cart. 

### `redirect` (string)

The URL to redirect the user to after they have selected or edited an address.

## Example implementations

::: tip 
This component is part of the [Checkout component](./checkout.md). You do not have to implement it separately.
::: 

### Display the address selector

```ini
[addressSelector shippingAddress]
type = "shipping"
redirect = "/checkout"

[addressSelector billingAddress]
type = "billing"
redirect = "/checkout"
==

{% component 'shippingAddress' %}
{% component 'billingAddress' %}
```