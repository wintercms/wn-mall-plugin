# AddressList

The `AddressList` component displays a list of a user's addresses.

It generates edit links for the [AddressForm](./address-form.md) component
and gives the user the ability to delete an Address.
 
## Properties

There are no properties available for this component.

## Example implementations

::: tip 
This component is part of the [MyAccount component](./my-account.md).  
**You do not have to implement it separately.**
::: 

### Display the address list

```twig{5,11}
title = "Account"
url = "/account/addresses"

<!-- [!code focus:1] -->
[addressList]
==
<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- [!code focus:1] -->
            {% component 'addressList' %}
        </div>
    </div>
</div>
```