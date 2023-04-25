<?php namespace Jakten\Services\Asset\Strategy;

use Jakten\Models\Asset;
use Jakten\Services\Asset\AssetType;

/**
 * Class ImagePost
 *
 * @package Jakten\Services\Asset\Strategy
 */
class ImagePost extends ImageAbstract
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
        return AssetType::IMAGE_POST;
    }

    /**
     * Get the asset label. Is used for upload folder name.
     **/
    public function getLabel()
    {
        return 'post';
    }
}
