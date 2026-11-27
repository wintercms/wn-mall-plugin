---
title: Configuration
editLink: true
---

# Configuration

Once the plugin is [installed](/guide/introduction/installation.html#manual-installation), and the 
first [data is seeded](/guide/introduction/installation.html#demo-data), you should 
configure your new Shop as described in the following steps. 

## Plugin: Winter.Location

Visit your WinterCMS backend and go to `Settings` -> `Location` -> `Country & States`.  
Make sure, that only the countries you are shipping to are enabled. Disable every other country in this list.

## Plugin: Winter.Mall Configuration

We highly recommend declaring and checking the following settings and settings pages.

1. `Mall: General` -> `Configuration` (email address, CMS pages)
2. `Mall: General` -> `Currencies` -> [Read more here](/guide/usage/currencies.html)
3. `Mall: General` -> `Taxes` -> [Read more here](/guide/usage/taxes.html)
4. `Mall: Payments` -> `Payment methods` -> [Read more here](/guide/usage/payment-methods.html)
5. `Mall: Orders` -> `Shipping methods` -> [Read more here](/guide/usage/shipping-methods.html)

## Theme: Layout

Your WinterCMS theme must provide some dependencies, required by the JavaScript environment of the 
**Winter.Mall** plugin. The following changes must be implemented in the layout, which is used on the 
individual store pages (as described in the section below).

If you're new to Winter's component system, you should definitively take a look at the 
[official documentation page](https://wintercms.com/docs/develop/docs/cms/components).
You can also take a look at [the default layout](https://github.com/wintercms/wn-mall-theme/blob/main/layouts/default.htm#L7) of our demonstration theme for some guidance.

### Components

The **Winter.Mall** plugin relies on 2 components that ensure the basic functionality of the environment:

1. The `[mallDependencies]` component includes the most-basic required front-end assets. You need to 
add the component within the `ini` configuration section of your theme and include it with the 
familiar TWIG-Syntax inside the `<head>` area of your layout.
2. The `[session]` component, provided by the *Winter.Users* plugin, ensures that the user remains 
logged in while browsing your store. It only must be included within the `ini` section.


```twig{4,5,12}
description = "Default Layout"

<!-- [!code focus:2] -->
[mallDependencies]
[session]
==
<!DOCTYPE html>
<html>
<head>
    <!-- ... -->
    <!-- [!code focus:1] -->
    {% component 'mallDependencies' %}
</head>
<body>
    <!-- ... -->
</body>
</html>
```

### Scripts

Make sure to include the `scripts` tag in your layout, which works as a placeholder for all injected 
JavaScript files and code blocks. 

The **Winter.Mall** plugin also requires Winter's [AJAX framework](https://wintercms.com/docs/develop/docs/ajax/introduction)
for the interaction with the PHP-side of the components. Thus, make sure to include the framework 
either by using one of the [Combiner aliases](https://wintercms.com/docs/develop/docs/services/asset-compilation#combiner-aliases) 
or the `{% framework extras %}` tag, as shown below.

```twig{9,10}
<!DOCTYPE html>
<html>
<head>
    <!-- ... -->
</head>
<body>
    <!-- ... -->
    <!-- [!code focus:2] -->
    {% framework extras %}
    {% scripts %}
</body>
</html>
```

### Winter.Pages Static Menu

If you are using the `Winter.Pages` plugin, you can add the `All mall shop categories` entry to 
your navigation. This will render a tree of all available categories in your theme. 


## Theme: Pages

The **Winter.Mall** plugin expects some predefined pages from your theme to handle the different views. 
These pages are usually recognized automatically, but can be selected in the backend settings page,
under `Mall: General` -> `Configuration`.

The following pages represent the minimum requirement to ensure a complete implementation of the 
**Winter.Mall** plugins. Of course, you're free to change and rename them as you please, just make sure 
to keep the structure of the `url`parameter.

- [`product.htm`](#product-htm) - Single Product
- [`category.htm`](#category-htm) - Category
- [`cart.htm`](#cart-htm) - Current Cart
- [`checkout.htm`](#checkout-htm) - (Quick) Checkout
- [`myaccount.htm`](#myaccount-htm) - Customer Account
- [`address.htm`](#address-htm) - Configure Customer Address
- [`login.htm`](#login-htm) - User Login Page


### product.htm

The product page displays a single product using the [`Product` component](/guide/components/product).

```twig
url = "/product/:slug/:variant?"
title = "Product"
layout = "default"

<!-- [!code focus:3] -->
[product]
product = ":slug"
variant = ":slug"
==
<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- [!code focus:1] -->
            {% component 'product' %}
        </div>
    </div>
</div>
```

### category.htm

The category page displays all products in a category using the [`Products` component](/guide/components/products). 
The products can be additionally filtered using the [`ProductsFilter` component](/guide/components/products-filter).

```twig
url = "/category/:slug*"
title = "Category"
layout = "default"

<!-- [!code focus:6] -->
[products]
category = ":slug"
setPageTitle = 1
includeVariants = 1
includeChildren = 1
perPage = 9

<!-- [!code focus:3] -->
[productsFilter]
category = ":slug"
showPriceFilter = 1
==
<div class="container">
    <div class="row">
        <div class="col-12 col-md-4">
            <!-- [!code focus:1] -->
            {% component 'productsFilter' %}
        </div>
        <div class="col-12 col-md-8">
            <!-- [!code focus:2] -->
            <h2>{{ products.category.name }}</h2>
            {% component 'products' %}
        </div>
    </div>
</div>
```

### cart.htm

This page displays the cart of the current user or guest using the [`Cart` component](/guide/components/cart). 

```twig
url = "/cart"
title = "Cart"
layout = "default"

<!-- [!code focus:2] -->
[cart]
showTaxes = 0
==
<!-- [!code focus:7] -->
{% component 'cart' %}

{% if cart.products.count > 0 %}
    <a href="{{ 'checkout' | page }}" class="btn">
        Begin checkout
    </a>
{% endif %}
``` 

### checkout.htm

The checkout page hosts the complete checkout process, using the [`Checkout` component](/guide/components/checkout). 
The **Winter.Mall** plugin also provides an alternative version with the [`QuickCheckout` component](/guide/components/quick-checkout),
which combines all steps directly on one single page.

```twig
url = "/checkout/:step?"
title = "Checkout"
layout = "default"

<!-- [!code focus:4] -->
[signUp]

[checkout]
step = "{{ :step }}"
==
<!-- [!code focus:5] -->
{% if not user %}
    {% component 'signUp' %}
{% else %}
    {% component 'checkout' %}
{% endif %}
``` 

### myaccount.htm

The my-account page displays an general account overview of the current logged-in user, using the 
[`MyAccount` component](/guide/components/my-account). We also provide additional attributes to the 
`session` component, provided by the _Winter.User_ plugin.

```twig
url = "/account/:page?"
title = "Account"
layout = "default"

<!-- [!code focus:6] -->
[session]
security = "user"
redirect = "login"

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

### address.htm

The address page displays an form for a specific address of the current logged-in user, using the 
[`AddressForm` component](/guide/components/address-form). 

```twig
url = "/address/:address?/:redirect?/:set?"
title = "Address"
layout = "default"

<!-- [!code focus:8] -->
[session]
security = "user"
redirect = "home"

[addressForm]
address = "{{ :address }}"
redirect = "{{ :redirect }}"
set = "{{ :set }}"
==
<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- [!code focus:1] -->
            {% component 'addressForm' %}
        </div>
    </div>
</div>
``` 

### login.htm

The login form displays a sign-up form for guests using the [`signUp` component](/guide/components/sign-up).

```twig
url = "/login"
title = "Login"
layout = "default"

<!-- [!code focus:2] -->
[signUp]
redirect = "/account"
==
<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- [!code focus:1] -->
            {% component 'signUp' %}
        </div>
    </div>
</div>
``` 

## Finish Configuration

Everything settled up and working? Really? Execute `php artisan mall:check` again and see if the 
plugin shares your opinion.

If yes, check out our [`Going Live advice`](/guide/usage/going-live) and enjoy selling!
