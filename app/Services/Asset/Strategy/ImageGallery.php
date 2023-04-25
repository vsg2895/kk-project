<?php

namespace Jakten\Services\Asset\Strategy;


use Jakten\Models\Asset;
use Jakten\Services\Asset\AssetType;

class ImageGallery extends ImageAbstract
{
    
    /**
     * Modify the image here.
     **/
    public function modify()
    {
        $this->fitInsideBox(1920, 1080);
    }
    
    /**
     * Generate alternative versions of the image.
     **/
    public function generateVersions(Asset $asset)
    {
        $thumbnail = $asset->replicate(['type', 'path']);
        $thumbnail->parent_id = $asset->id;
    
        $this->fitInsideBox(640, 480);
    
        $ext = $this->image->extension;
        $this->image->save(str_replace('.' . $ext, '_thumbnail.' . $ext, $this->filePath), 100);
    
        $thumbnail->type = AssetType::IMAGE_GALLERY_ITEM_THUMBNAIL;
        $thumbnail->path = str_replace('.' . $ext, '_thumbnail.' . $ext, $this->path);
        $thumbnail->save();
    }
    
    /**
     * Get the asset type.
     **/
    public function getType()
    {
        return AssetType::IMAGE_GALLERY_ITEM;
    }
    
    /**
     * Get the asset label. Is used for upload folder name.
     **/
    public function getLabel()
    {
        return 'gallery';
    }
}