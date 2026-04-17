<?php

namespace Marshmallow\AdvancedImage;

use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Http\Requests\NovaRequest;

class AdvancedImage extends Image
{
    use TransformableImage;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'advanced-image';

    /**
     * Indicates whether the image should be fully rounded or not.
     *
     * @var bool
     */
    public $showOnIndex = true;

    protected $customCallback;
    public $rounded = true;

    /**
     * Create a new field.
     *
     * @param string        $name
     * @param string|null   $attribute
     * @param string|null   $disk
     * @param callable|null $storageCallback
     *
     * @return void
     */
    public function __construct($name, $attribute = null, $disk = 'public', $storageCallback = null)
    {
        parent::__construct($name, $attribute, $disk, $storageCallback);

        $this->thumbnail(function () {
            return $this->value ? Storage::disk($this->disk)->url($this->value) : null;
        })->preview(function () {
            return $this->value ? Storage::disk($this->disk)->url($this->value) : null;
        });
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param string                                  $requestAttribute
     * @param object                                  $model
     * @param string                                  $attribute
     *
     * @return void
     */
    protected function fillAttribute(NovaRequest $request, string $requestAttribute, object $model, string $attribute): mixed
    {
        if (empty($request->{$requestAttribute})) {
            return null;
        }

        $previousFileName = $model->{$attribute};

        $this->transformImage($request->{$this->attribute}, json_decode($request->{$this->attribute . '_data'}));

        if ($previousFileName) {
            Storage::disk($this->disk)->delete($previousFileName);
        }

        return parent::fillAttribute($request, $requestAttribute, $model, $attribute);
    }

    public function setCustomCallback($customCallback)
    {
        $this->customCallback = $customCallback;

        return $this;
    }

    protected function customCallback($request, $requestAttribute, $model, $attribute, $fileName)
    {
        $customCallback = $this->customCallback;
        if ($customCallback) {
            $customCallback($request, $requestAttribute, $model, $attribute, $fileName);
        }
    }

    public function customThumbnail($callable)
    {
        $this->thumbnail($callable);

        return $this;
    }

    public function customPreview($callable)
    {
        $this->preview($callable);

        return $this;
    }


    /**
     * Prepare the field element for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'croppable'   => $this->croppable,
            'aspectRatio' => $this->cropAspectRatio,
        ]);
    }
}
