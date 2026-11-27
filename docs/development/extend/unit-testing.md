# Unit Testing

## 1. Install packages

The following dependencies must be installed directly in the root folder of your WinterCMS 
installation, and __not__ inside the `plugins/winter/mall` plugins root directory.

1. [`fakerphp/faker`](https://packagist.org/packages/fakerphp/faker)
2. [`mockery/mockery`](https://packagist.org/packages/mockery/mockery)

```sh
composer require --dev "fakerphp/faker:^1.23" "mockery/mockery:^1.6"
```

## 2. Run tests

From the Winter CMS root, run:

```sh
php artisan winter:test -p Winter.Mall
```
or if you want to render test method names as human-readable sentences:
```sh
php artisan winter:test -p Winter.Mall -- --testdox
```

By default, Winter's plugin test case creates a fresh SQLite in-memory database for each test,
runs the migrations, and loads the Mall test data. The PHP SQLite extension is required.
A `config/testing/database.php` file overrides this default, so it must point to a dedicated
test database.

The existing tests are organized under `tests/unit` by the classes, models, or traits they
cover. `tests/feature` and `tests/fixtures` are reserved for future feature tests and fixture
files.

### Running Tests with Composer

From the Winter CMS root, you can also use its Composer test script with the Mall configuration:

```sh
composer test -- --configuration=plugins/winter/mall/phpunit.xml --bootstrap=modules/system/tests/bootstrap/app.php
```
### Filtering Tests

You can also use the PHPUnits `--filter` option to test only specific classes or methods. Visit 
[the official documentation](https://docs.phpunit.de/en/9.6/textui.html) for more information.

The examples use the class name without its namespace to avoid escaping backslashes.
The method name must include the `test_` prefix.

### Examples

Examples with artisan:

```sh
# To run a single class
php artisan winter:test -p Winter.Mall -- --filter 'CartProductTest::'

# To run a single method
php artisan winter:test -p Winter.Mall -- --filter 'CartProductTest::test_custom_field_value_conversion$'
```

Examples with composer:

```sh
# To run a single class
composer test -- --configuration=plugins/winter/mall/phpunit.xml --bootstrap=modules/system/tests/bootstrap/app.php --filter 'CartProductTest::'

# To run a single method
composer test -- --configuration=plugins/winter/mall/phpunit.xml --bootstrap=modules/system/tests/bootstrap/app.php --filter 'CartProductTest::test_custom_field_value_conversion$'
```