# SEO field for filament admin panel

![SEOFieldHeader](https://raw.githubusercontent.com/34ML/Filament-SEO/main/resources/Images/Filament-Seo.jpg)

This package is a convenient helper for using the [laravel-seo](https://github.com/34ML/laravel-seo) package with Filament Admin and Forms.

`Please check the [laravel-seo](https://github.com/34ML/laravel-seo) package for more information about how to set up the SEO logic in your project.`

It provides a simple component that returns a Filament field group for ENGLISH and ARABIC to modify the title, description, keywords, follow type and image fields of the SEO model. It automatically takes care of getting and saving all the data to the seo() relationship, and you can thus use it anywhere, without additional configuration!

## Installation

You can install the package via composer:

```bash
composer require 34ml/filament-seo
```

## Usage

```php
use _34ml\SEO\SEO;

public static function form(Form $form): Form
{
    return $form->schema([
        SEO::make(),
       // .. Your other fields
    ]);
}
```

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Ahmed Essam](https://github.com/aessam13)
- [Reham Mourad](https://github.com/RehamMourad)
- [Mostafa Hassan](https://github.com/MostafaHassan1)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
