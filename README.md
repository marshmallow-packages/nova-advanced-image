# Nova Advanced Image Field

This package provides an advanced image field for Nova resources allowing you to upload, crop and resize any image. It uses [Cropper.js](https://fengyuanchen.github.io/cropperjs) with [vue-cropperjs](https://github.com/Agontuk/vue-cropperjs) in the frontend and [Intervention Image](http://image.intervention.io) in the backend.

![screenshot of the advanced image field](https://raw.githubusercontent.com/marshmallow-packages/nova-advanced-image/master/screenshot.png)

## Requirements
This package requires **one of** the following libraries:
- GD Library >=2.0 (used by default)
- Imagick PHP extension >=6.5.7

See [Intervention requirements](http://image.intervention.io/getting_started/installation) for more details.

## Installation

Install the package into a Laravel application with Nova using Composer:

```bash
composer require marshmallow/nova-advanced-image
```

If you want to use Imagick as the default image processing library, follow the [Intervention documentation for Laravel](http://image.intervention.io/getting_started/installation#laravel). This will provide you with a new configuration file where you can specify the driver you want.

## Usage
`AdvancedImage` extends from `File` so you can use any methods that `File` implements. See the documentation [here](https://nova.laravel.com/docs/2.0/resources/fields.html#file-field).

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
            AdvancedImage::make('photo')->croppable(16/9),

            // Resize the image to a max width
            AdvancedImage::make('photo')->resize(1920),

            // Resize the image to a max height
            AdvancedImage::make('photo')->resize(null, 1080),

            // Show a cropbox and resize the image
            AdvancedImage::make('photo')->croppable()->resize(400, 300),

            // Override the image processing driver for this field only
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

### Security

If you discover any security related issues, please email stef@marshmallow.dev instead of using the issue tracker.

## Credits

- [Clément Tessier](https://github.com/ctessier/nova-advanced-image-field)
- [Clément Tessier contributors](https://github.com/ctessier/nova-advanced-image-field/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
