# EasyPost Api Wrapper

![Tests](https://github.com/cybrix-solutions/easypost/workflows/Tests/badge.svg?style=flat-square)

`easypost` is a paid package that offers shipment functionality for your Laravel application utilizing the [EasyPost](https://www.easypost.com/) Api.
Our package wraps the `easypost/easypost-php` package for ease of interacting with the EasyPost Api.

## Installation

### Getting a license

You must buy a license on [the EasyPost product page](#) at cybrixsolutions.com

Single application licenses may be installed in a single Laravel app. If you purchased the unlimited application license,
there are no restrictions. A license comes with one year of upgrades. If your license expires, you are still allowed to use
the EasyPost package, but you won't receive updates anymore.

### Requiring the package

After you've purchased a license, add the `satis.cybrixsolutions.com` repository in your `composer.json`.

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://satis.cybrixsolutions.com"
        }
    ]
}
```

Next, you need to create a file called `auth.json` and place it either next to the `composer.json` file in your project,
or in the Composer home directory. You can determine the Composer home directory on \*nix machines by using this command:

```bash
composer config --list --global | grep home
```

This is the content you should put in `auth.json`:

```json
{
    "http-basic": {
        "satis.cybrixsolutions.com": {
            "username": "<YOUR-CYBRIXSOLUTIONS.COM-EMAIL-ADDRESS-HERE>",
            "password": "<YOUR-EASYPOST-LICENSE-KEY-HERE>"
        }
    }
}
```

To be sure you can reach `satis.cybrixsolutions.com`, clean your autoloaders before using this command:

```bash
composer dump-autoload
```

To validate if Composer can read your `auth.json` file, you can run this command:

```bash
composer config --list --global | grep satis.cybrixsolutions.com
```

With the configuration in place, you'll be able to install the EasyPost package into your project using this command:

```bash
composer require "cybrix-solutions/easypost:^0.1"
```

## Documentation

You'll find the documentation for this package on [our documentation site](#). (Coming soon)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

Please review [my security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Testing

You can run the tests with:

```bash
composer test
```

## Credits

-   [Randall Wilk](https://github.com/rawilk)
-   [All Contributors](../../contributors)
-   [EasyPost](https://github.com/easypost/easypost-php)

## Disclaimer

This package is not affiliated with, maintained, authorized, endorsed or sponsored by EasyPost. It is simply a wrapper around their API.
