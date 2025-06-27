<?php

namespace Marshmallow\AdvancedImage;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Drivers\Gd\Driver as GDDriver;
use Intervention\Image\ImageManager;

trait TransformableImage
{
    /**
     * The driver library to use for transforming the image.
     *
     * This value will override the driver configured for Intervention
     * in the `config/image.php` file of the Laravel project.
     *
     * @var string|null
     */
    private $driver = null;

    /**
     * Indicates if the image is croppable.
     *
     * @var bool
     */
    private $croppable = false;

    /**
     * The fixed aspect ratio of the crop box.
     *
     * @var float
     */
    private $cropAspectRatio;

    /**
     * The width for the resizing of the image.
     *
     * @var int
     */
    private $width;

    /**
     * The height for the resizing of the image.
     *
     * @var int
     */
    private $height;

    /**
     * Indicates if the image is orientable.
     *
     * @var bool
     */
    private $autoOrientate = false;

    /**
     * The Intervention Image instance.
     *
     * @var \Intervention\Image\Image
     */
    private $image;

    /**
     * Override the default driver to be used by Intervention for the image manipulation.
     *
     * @param string $driver
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function driver(string $driver)
    {
        if (!in_array($driver, ['gd', 'imagick'])) {
            throw new \Exception("The driver \"$driver\" is not a valid Intervention driver.");
        }

        $this->driver = $driver;

        return $this;
    }

    /**
     * Specify if the underlying image should be croppable.
     * If a numeric value is given as a first parameter, it will be used to define a fixed aspect
     * ratio for the crop box.
     *
     * @param mixed $param
     *
     * @return $this
     */
    public function croppable($param = true)
    {
        if (is_numeric($param)) {
            $this->cropAspectRatio = $param;
            $param = true;
        }

        $this->croppable = $param;

        return $this;
    }

    /**
     * Specify the size (width and height) the image should be resized to.
     *
     * @param int|null $width
     * @param int|null $height
     *
     * @return $this
     */
    public function resize($width = null, $height = null)
    {
        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    /**
     * Specify if the underlying image should be orientated.
     * Rotate the image to the orientation specified in Exif data, if any. Especially useful for smartphones.
     * This method requires the exif extension to be enabled in your php settings.
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function autoOrientate()
    {
        if (!extension_loaded('exif')) {
            throw new \Exception('The PHP exif extension must be enabled to use the autoOrientate method.');
        }

        $this->autoOrientate = true;

        return $this;
    }

    /**
     * Transform the uploaded file.
     *
     * @param \Illuminate\Http\UploadedFile $uploadedFile
     * @param object|null $cropperData
     *
     * @return void
     */
    public function transformImage(UploadedFile $uploadedFile, ?object $cropperData): void
    {
        if (!$this->croppable && !$this->width && !$this->height) {
            return;
        }

        $manager = new ImageManager(
            $this->driver === 'gd' ? GDDriver::class : Driver::class,
            autoOrientation: $this->autoOrientate,
        );

        // open an image file
        $this->image = $manager->read($uploadedFile->getPathName());

        if ($this->croppable && $cropperData) {
            $this->cropImage($cropperData);
        }

        if ($this->width || $this->height) {
            $this->resizeImage();
        }

        $clientExtension = $uploadedFile->clientExtension();
        if (!filled($clientExtension)) {
            $clientExtension = null;
        }

        $this->image->save(null, null, $clientExtension);

        unset($this->image);
    }

    /**
     * Crop the image.
     *
     * @param object $cropperData
     *
     * @return void
     */
    private function cropImage(object $cropperData): void
    {
        $this->image->crop($cropperData->width, $cropperData->height, $cropperData->x, $cropperData->y);
    }

    /**
     * Resize the image.
     *
     * @return void
     */
    private function resizeImage(): void
    {
        $this->image->scale($this->width, $this->height);
    }
}
