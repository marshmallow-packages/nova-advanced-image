![alt text](https://marshmallow.dev/cdn/media/logo-red-237x46.png "marshmallow.")

# Nova Advanced Image Field

[![Latest Version on Packagist](https://img.shields.io/packagist/v/marshmallow/nova-advanced-image.svg?style=flat-square)](https://packagist.org/packages/marshmallow/nova-advanced-image)
[![Tests](https://img.shields.io/github/actions/workflow/status/marshmallow-packages/nova-advanced-image/php-syntax-checker.yml?branch=main&label=tests&style=flat-square)](https://github.com/marshmallow-packages/nova-advanced-image/actions/workflows/php-syntax-checker.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/marshmallow/nova-advanced-image.svg?style=flat-square)](https://packagist.org/packages/marshmallow/nova-advanced-image)

An advanced image field for Nova with crop and resize.

This package provides an advanced image field for Nova resources allowing you to upload, crop and resize any image. It uses [Cropper.js](https://fengyuanchen.github.io/cropperjs) with [vue-cropperjs](https://github.com/Agontuk/vue-cropperjs) in the frontend and [Intervention Image](http://image.intervention.io) in the backend.

![screenshot of the advanced image field](https://raw.githubusercontent.com/marshmallow-packages/nova-advanced-image/main/screenshot.png)

## Requirements

- PHP `^8.0`
- [Laravel Nova](https://nova.laravel.com) `^4.0` or `^5.0`
- [Intervention Image](http://image.intervention.io) `^3.6`

Intervention Image requires **one of** the following libraries for image processing:

- GD Library >=2.0 (used by default)
- Imagick PHP extension >=6.5.7

See the [Intervention requirements](http://image.intervention.io/getting_started/installation) for more details. The `autoOrientate()` helper additionally requires the PHP `exif` extension to be enabled.

## Installation

Install the package into a Laravel application with Nova using Composer:

```bash
composer require marshmallow/nova-advanced-image
```

The field service provider is auto-discovered, so there is nothing further to register.

If you want to use Imagick as the default image processing library, follow the [Intervention documentation for Laravel](http://image.intervention.io/getting_started/installation#laravel). This will provide you with a new configuration file where you can specify the driver you want. You can also override the driver per field with `->driver('imagick')`.

## Usage

`AdvancedImage` extends Nova's `Image` field, so you can use any method that `Image`/`File` implements. See the [Nova file field documentation](https://nova.laravel.com/docs/resources/fields#file-field) for the inherited options.

```php
<?php

namespace App\Nova;

// ...
use Marshmallow\AdvancedImage\AdvancedImage;

class Post extends Resource
{
    // ...

    public function fields(Request $request)
    {
        return [
            // ...

            // Simple image upload
            AdvancedImage::make('photo'),

            // Show a cropbox with a free ratio
            AdvancedImage::make('photo')->croppable(),

            // Show a cropbox with a fixed ratio
            AdvancedImage::make('photo')->croppable(16 / 9),

            // Resize the image to a max width
            AdvancedImage::make('photo')->resize(1920),

            // Resize the image to a max height
            AdvancedImage::make('photo')->resize(null, 1080),

            // Show a cropbox and resize the image
            AdvancedImage::make('photo')->croppable()->resize(400, 300),

            // Auto-rotate based on the image's Exif orientation (requires the exif extension)
            AdvancedImage::make('photo')->autoOrientate(),

            // Override the image processing driver for this field only ('gd' or 'imagick')
            AdvancedImage::make('photo')->driver('imagick')->croppable(),

            // Store to AWS S3
            AdvancedImage::make('photo')->disk('s3'),

            // Specify a custom subdirectory
            AdvancedImage::make('photo')->disk('s3')->path('image'),
        ];
    }
}
```

The `resize` option uses [Intervention Image `resize()`](http://image.intervention.io/api/resize) with the upsize and aspect ratio constraints.

### Available methods

| Method | Description |
| --- | --- |
| `croppable($param = true)` | Enable the crop box. Pass a numeric value (e.g. `16 / 9`) to lock a fixed aspect ratio. |
| `resize($width = null, $height = null)` | Resize the stored image to a maximum width and/or height. |
| `driver(string $driver)` | Override the Intervention driver for this field. Accepts `gd` or `imagick`. |
| `autoOrientate()` | Rotate the image to the orientation stored in its Exif data. Requires the PHP `exif` extension. |
| `customThumbnail($callable)` | Customise the thumbnail URL resolver (proxies Nova's `thumbnail()`). |
| `customPreview($callable)` | Customise the preview URL resolver (proxies Nova's `preview()`). |
| `setCustomCallback($customCallback)` | Run a custom callback after the image has been stored. |

### Avatar variant

For avatar-style fields, use `AdvancedAvatar`. It extends `AdvancedImage`, implements Nova's `Cover` contract and renders a rounded thumbnail.

```php
use Marshmallow\AdvancedImage\AdvancedAvatar;

AdvancedAvatar::make('avatar')->croppable(1),
```

## Security

If you discover any security related issues, please email stef@marshmallow.dev instead of using the issue tracker.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent changes.

## Credits

- [Marshmallow](https://github.com/marshmallow-packages)
- [Clément Tessier](https://github.com/ctessier/nova-advanced-image-field) (original author)
- [All Contributors](https://github.com/marshmallow-packages/nova-advanced-image/graphs/contributors)

## License

The MIT License (MIT). Please see the [License File](LICENSE.md) for more information.
