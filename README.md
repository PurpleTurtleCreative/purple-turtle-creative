# Purple Turtle Creative

Custom WordPress theme built from [Automattic's Underscores](https://github.com/Automattic/_s) starter theme template, custom generated at https://underscores.me/

© [Michelle Blanchette](https://github.com/MichelleBlanchette). All Rights Reserved.

## Configuration

The following global constants are currently used within this theme. Please define them in your wp-config.php file.

```php
/* Google Analytics - Measurement Protocol */
define( 'PTC_GA4_API_SECRET', '' );
define( 'PTC_GA4_MEASUREMENT_ID', '' );
define( 'PTC_GA4_CLIENT_ID', '' );
/* Mailgun */
define( 'PTC_MAILGUN_API_KEY', '' );
define( 'PTC_MAILGUN_DOMAIN', '' );
/* Cloudflare - Turnstile CAPTCHA */
define( 'PTC_CF_TURNSTILE_SITE_KEY', '' );
define( 'PTC_CF_TURNSTILE_SECRET_KEY', '' );
```

Installation
---------------

### Requirements

`_s` requires the following dependencies:

- [Node.js](https://nodejs.org/)
- [Composer](https://getcomposer.org/)

### Setup

To start using all the tools that come with `_s`  you need to install the necessary Node.js and Composer dependencies :

```sh
$ composer install
$ npm install
```

### Available CLI commands

`_s` comes packed with CLI commands tailored for WordPress theme development :

- `composer lint:wpcs` : checks all PHP files against [PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/).
- `composer lint:php` : checks all PHP files for syntax errors.
- `npm run watch` : watches all SASS files and recompiles them to css when they change.
- `npm run compile:css` : compiles SASS files to css.
- `npm run lint:scss` : checks all SASS files against [CSS Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/).
- `npm run lint:js` : checks all JavaScript files against [JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/).
- `npm run bundle` : generates a .zip archive for distribution, excluding development and system files.
