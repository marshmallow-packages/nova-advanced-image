<?php

namespace Marshmallow\AdvancedImage;

use Laravel\Nova\Fields\File;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Http\Requests\NovaRequest;
use Marshmallow\AdvancedImage\TransformableImage;

class AdvancedImage extends File
{
    use TransformableImage;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'advanced-image';

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = true;

    protected $customCallback;

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
    protected function fillAttribute(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if (empty($request->{$requestAttribute})) {
            return;
        }

        $previousFileName = $model->{$attribute};

        $this->transformImage($request->{$this->attribute}, json_decode($request->{$this->attribute.'_data'}));

        parent::fillAttribute($request, $requestAttribute, $model, $attribute);

        Storage::disk($this->disk)->delete($previousFileName);
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
     * Get additional meta information to merge with the element payload.
     *
     * @return array
     */
    public function meta()
    {
        return array_merge([
            'croppable'   => $this->croppable,
            'aspectRatio' => $this->cropAspectRatio,
        ], parent::meta());
    }
}
