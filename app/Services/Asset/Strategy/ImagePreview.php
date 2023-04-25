<?php namespace Jakten\Services\Asset\Strategy;

use Jakten\Models\Asset;
use Jakten\Services\Asset\AssetType;

/**
 * Class ImagePreview
 *
 * @package Jakten\Services\Asset\Strategy
 */
class ImagePreview extends ImageAbstract
{
    public function modify()
    {
        //
    }

    public function generateVersions(Asset $asset)
    {
        //
    }

    /**
     * Get the asset type.
     **/
    public function getType()
    {
        return AssetType::IMAGE_PREVIEW;
    }

    /**
     * Get the asset label. Is used for upload folder name.
     **/
    public function getLabel()
    {
        return 'preview';
    }
}
