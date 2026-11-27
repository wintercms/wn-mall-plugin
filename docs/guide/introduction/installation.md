---
title: Installation
editLink: true
---

# Installation
<!-- 
**Winter.Mall** can be found on the official [Winter CMS Marketplace](https://wintercms.com/plugin/winter-mall). 



## Using Marketplace

The easiest way to install **Winter.Mall** is via the official Marketplace. There you can add the plugin to 
your project and install it by executing the following artisan command in the root directory of your 
website:

```sh
php artisan project:sync
```

During this installation, all dependencies are downloaded and installed automatically.
-->

## Manual Installation
<!-- 
You can install **Winter.Mall** also manually like any other WinterCMS plugin. However, make sure that you 
install the dependencies up-front. Visit the root directory of your WinterCMS website and execute 
the following commands

```sh
php artisan plugin:install Winter.User
php artisan plugin:install Winter.Location
php artisan plugin:install Winter.Translate
php artisan plugin:install Winter.Mall
```

You can also use composer to install the plugins, however, keep in mind that you've to install 
and migrate the dependent packages first before installing and migrating Mall itself.
-->
You can use composer to install the plugins, however, keep in mind that you've to install 
and migrate the dependent packages first before installing and migrating **Winter.Mall** itself.

Installing dependencies:

```sh
composer require winter/user-plugin
composer require winter/location-plugin
composer require winter/translate-plugin

php artisan migrate
```

Installing **Winter.Mall**:
```sh
composer require winter/wn-mall-plugin

php artisan migrate
```
<!-- 
Both ways will automatically install all composer packages required by the **Winter.Mall** plugin, at least 
on WinterCMS v3 installations. 
-->


## Optional Dependencies

Certain features of the **Winter.Mall** plugin requires you to install additional composer packages to 
make everything work. 

| Feature                      | Package |
| ---------------------------- | ------- |
| File-Based Index             | [`offline/jsonq`](https://packagist.org/packages/offline/jsonq)<br />[`tmarois/filebase`](https://packagist.org/packages/tmarois/filebase) |
| Google Merchant Feed         | [`vitalybaev/google-merchant-feed`](https://packagist.org/packages/vitalybaev/google-merchant-feed) |
| PostFinance Payment Provider | [`bummzack/omnipay-postfinance`](https://packagist.org/packages/bummzack/omnipay-postfinance) |


Simple use the following command in the root directory of your WinterCMS website to install the 
desired package.

```sh
composer require <$package_name>
```
> [!CAUTION]
> **We do not provide other payments providers packages ourselves.**  
> You need to install them manually if you want to use them.  
If your project requires additional payment providers gateways, ask the WinterCMS community for guidance, some of them are available as independent winter plugins (free or paid).

## Check your Installation

After you successfully installed all plugins and packages, you can run the `mall:check` artisan 
command to validate your installation. 

**Don't panic!** After the initial installation there will be some items that are marked as FAIL. 
This is because we didn't configure the plugin yet.

```sh
php artisan mall:check
```

::: tip
You can run this command at any time in the future to make sure everything is set up correctly.
:::


## Demo data

To get a feeling for how **Winter.Mall** works, you can run the following command to pre-populate your installation with demo data.

```sh
php artisan mall:seed-demo
```
::: warning
This will erase all shop data and reset all settings! Do not run this command if you have already configured your 
installation. 
:::

You can also simple reset the whole **Winter.Mall** installation by running the following command.

```sh
php artisan plugin:refresh Winter.Mall
```

## Setup Demo-Theme

<!-- 
WIP
We also provide a simple demonstration theme to make getting started with our **Winter.Mall** plugin as 
easy as possible.  
You can find a demo implementation of a shop
theme on GitHub: [https://github.com/wintercms/wn-mall-theme](https://github.com/wintercms/wn-mall-theme)

::: warning
If you use the demo theme you should still apply the steps mentioned in the "Configuration" section.
:::

* <input type="checkbox"> Simply clone the theme to `<your installation>/themes/mall` and select it as your active theme.

* <input type="checkbox"> The demo theme requires `OFFLINE.SiteSearch` and `Winter.Pages` to be installed. Make sure 
these plugins are available as well.
-->

If you want to start with a blank slate just follow the instructions on this and the [Theme Setup](/guide/introduction/configuration#theme-layout) page to get everything up and running.


