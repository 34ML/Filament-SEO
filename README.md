# SEO field for filament admin panel

![SEOFieldHeader](https://raw.githubusercontent.com/34ML/Filament-SEO/main/resources/Images/Filament-Seo.jpg)

* This package is a convenient helper for using the [laravel-seo](https://github.com/34ML/laravel-seo) package with Filament Admin and Forms , please check it for more information about how to set up the SEO logic in your project.

* It provides a simple component that returns a Filament field group for **_***any language***_** you want to modify the title, description, keywords, follow type fields of the SEO model.

* It automatically takes care of getting and saving all the data to the seo relationship, and you can thus use it anywhere, without additional configuration!

![FieldExample](https://raw.githubusercontent.com/34ML/Filament-SEO/main/resources/Images/FieldExample.png)


## Installation

You can install the package via composer:

```bash
composer require 34ml/filament-seo
```

You need to publish the config file where you can specify the languages you want to use:

```bash
php artisan vendor:publish --tag="filament-seo-config"
```
The config file will look like this:
```php
<?php

return [
    'locales' => [ //Add your locales here
        'en',
        'ar',
        'fr',
    ],
];
```
You need also to publish the migration file to create the seo table from the [laravel-seo](https://github.com/34ML/laravel-seo) package:

```bash
php artisan vendor:publish --tag="seo-migrations"
php artisan migrate
```

## Usage

* Sample usage in filament forms:
```php
use _34ml\SEO\SEOField;

public static function form(Form $form): Form
{
    return $form->schema([
        ...SEOField::make(),
       // Your other fields
    ]);
}
```

* You can add callbacks to add any additional fields you want to the SEO field group:

```php
use _34ml\SEO\SEOField;

public static function form(Form $form): Form
{
    return $form->schema([
        ...SEOField::make(
            callbacks: function() {
                return $this->collapsible(),
            }
        ),
       // Your other fields
    ]);
}
```

## Credits

- [Ahmed Essam](https://github.com/aessam13)
- [Mostafa Hassan](https://github.com/MostafaHassan1)
- [Reham Mourad](https://github.com/RehamMourad)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
