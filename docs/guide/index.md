---
title: Getting Started
editLink: true
---

# Mall Winter CMS Plugin

Welcome to the official documentation of the **Winter.Mall** plugin, the all-inclusive e-commerce solution
for the Laravel-based WinterCMS platform.

On the following pages you will find all the information you need to install the plugin on your 
website and integrate it in your used theme. You will also read about the individual models, 
controllers and how you can extend them to integrate your own functions.

> [!NOTE]
> If you think, that some details are missing or not sufficiently well explained, we invite you to 
> extend and add this to our documentation. For this case you will find an "**Edit this page on GitHub**" 
> link at the bottom of each page, which brings you to the corresponding markdown file in our Github 
> repository.  
> Thanks for your help!


## Requirements

We highly recommend using the latest WinterCMS v1.3+ and PHP v8 versions available, however, the 
following requirements represent the minimum needed to run Mall on your website.

- WinterCMS v1.2+ | v1.3+
- PHP v8.1+
- MySQL v5.7+ | v8.0+ **`or`** MariaDB v10.2+ **`or`** SQLite<span style="color: red;">**\***</span> v3.19+

> [!CAUTION]
> <span style="color: red;">**\***</span> Although it is possible to use Mall via SQLite, we 
> strongly recommend using one of the other database systems, especially on a large number of 
> products or customers.


### Dependencies

Mall relies on the following official WinterCMS Plugins, which must be installed before installing 
the Mall extension itself.

- [Winter.User](https://github.com/wintercms/wn-user-plugin) v2.3+
- [Winter.Location](https://github.com/wintercms/wn-location-plugin) v2.2+
- [Winter.Translate](https://github.com/wintercms/wn-translate-plugin) v2.3+

Mall also requires the following PHP packages, which should be installed automatically, when 
installing Mall via `composer` on WinterCMS v1.2+ installations.

- [barryvdh/laravel-dompdf](https://packagist.org/packages/barryvdh/laravel-dompdf) v2.1+ | v3.1+
- [hashids/hashids](https://packagist.org/packages/hashids/hashids) v4.1+
- [league/omnipay](https://packagist.org/packages/league/omnipay) v3.2+
- [omnipay/paypal](https://packagist.org/packages/omnipay/paypal) v3.0+
- [omnipay/stripe](https://packagist.org/packages/omnipay/stripe) v3.0+


### Optional dependencies

The following dependencies are only required if necessary, but must be installed manually if needed.

- [bummzack/omnipay-postfinance](https://packagist.org/packages/bummzack/omnipay-postfinance) - To use the PostFinance payment provider
- [offline/jsonq](https://packagist.org/packages/offline/jsonq) - To use the file based index
- [tmarois/filebase](https://packagist.org/packages/tmarois/filebase) - To use the file based index
- [vitalybaev/google-merchant-feed](https://packagist.org/packages/vitalybaev/google-merchant-feed) - To use the Google Merchant Feed integration
- [stripe/stripe-php](https://github.com/stripe/stripe-php) - To use the Stripe Hosted Checkout payment provider


### Development dependencies

The following dependencies are only required when developing or extending the Mall plugin itself, and 
must be installed manually as well, of course.

- [fakerphp/faker](https://packagist.org/packages/fakerphp/faker) v1.23+
- [mockery/mockery](https://packagist.org/packages/mockery/mockery) v1.6+
- [phpunit/phpunit](https://packagist.org/packages/omnipay/stripe) v8.5+ | v9.0+
- [squizlabs/php_codesniffer](https://packagist.org/packages/squizlabs/php_codesniffer) v3.0+


## Links

- [Demo Website](https://mall.offline.swiss)
- Mall Plugin - [GitHub](https://github.com/wintercms/wn-mall-plugin)
- Demo Theme - [GitHub](https://github.com/wintercms/wn-mall-theme)
