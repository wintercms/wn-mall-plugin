# CustomerProfile

The `CustomerProfile` component displays a form to edit a user's profile.

## Properties

There are no properties available for this component.

## Example implementations

::: tip 
This component is part of the [MyAccount component](./my-account.md).  
**You do not have to implement it separately.**
::: 

### Display the customer profile edit form

```twig{5,11}
title = "Account"
url = "/account/edit"

<!-- [!code focus:1] -->
[customerProfile]
==
<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- [!code focus:1] -->
            {% component 'customerProfile' %}
        </div>
    </div>
</div>
```