<?php

namespace Marshmallow\NovaAdvancedImageField;

use Laravel\Nova\Contracts\Cover;
use Marshmallow\AdvancedImage\AdvancedImage;

class AdvancedAvatar extends AdvancedImage implements Cover
{
    /**
     * Determine if the field should be displayed as rounded.
     *
     * @return bool
     */
    public function isRounded(): bool
    {
        return true;
    }

    /**
     * Determine if the field should be displayed as squared.
     *
     * @return bool
     */
    public function isSquared(): bool
    {
        return false;
    }

    /**
     * Resolve the thumbnail URL for the field.
     *
     * @return string|null
     */
    public function resolveThumbnailUrl()
    {
        return $this->resolveAttribute($this->thumbnailUrlCallback);
    }
}
