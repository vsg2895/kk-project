<?php namespace Jakten\Services\Asset\Strategy;

use Jakten\Models\Asset;
use Jakten\Services\Asset\AssetType;

/**
 * School logo image
 **/
class ImageLogo extends ImageAbstract
{
    /**
     * Modify the image here.
     **/
    public function modify()
    {
        $this->fitInsideBox(320, 128);
    }

    /**
     * Modify the image here.
     **/
    public function generateVersions(Asset $asset)
    {
        $small = $asset->replicate(['type', 'path']);
        $small->parent_id = $asset->id;

        $this->fitInsideBox(200, 80);

        $ext = $this->image->extension;
        $this->image->save(str_replace('.' . $ext, '_small.' . $ext, $this->filePath), 100);

        $small->type = AssetType::IMAGE_LOGO_SMALL;
        $small->path = str_replace('.' . $ext, '_small.' . $ext, $this->path);
        $small->save();
    }

    /**
     * Get the asset type.
     **/
    public function getType()
    {
        return AssetType::IMAGE_LOGO;
    }

    /**
     * Get the asset label. Is used for upload folder name.
     **/
    public function getLabel()
    {
        return 'logo';
    }
} // END abstract class ImageLogo
