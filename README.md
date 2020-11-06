# wp-smartbanner
A customisable WordPress smart app banner for iOS and Android.

## Composer
Use composer to require all dependencies:
`composer update` or `composer install`

## Building the assets
Use npm to build all assets:
`npm run build`

## PHP Code Sniffer
In the root of the plugin:
`vendor/bin/phpcs --ignore=*/vendor/*,*/wpcs/* --extensions=php --standard=WordPress .`

#### Autofix issues
To autofix issues: 
`vendor/bin/phpcbf --standard=WordPRess file/path`