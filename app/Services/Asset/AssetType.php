<?php namespace Jakten\Services\Asset;

/**
 * Class AssetType
 * @package Jakten\Services\Asset
 */
class AssetType
{
    /**
     * @constant IMAGE_LOGO
     */
    const IMAGE_LOGO = 1;

    /**
     * @constant IMAGE_LOGO_SMALL
     */
    const IMAGE_LOGO_SMALL = 2;

    /**
     * @const int IMAGE_GALLERY_ITEM
     */
    const IMAGE_GALLERY_ITEM = 3;

    /**
     * @const int IMAGE_GALLERY_ITEM_THUMBNAIL
     */
    const IMAGE_GALLERY_ITEM_THUMBNAIL = 4;

    /**
     * @const int IMAGE_PREVIEW
     */
    const IMAGE_PREVIEW = 5;

    /**
     * @const int IMAGE_POST
     */
    const IMAGE_POST = 6;

    /**
     * @const int IMAGE_PARTNER
     */
    const IMAGE_PARTNER = 7;

    /**
     * Label identifier to image type conversion for
     * retrieving a version of an asset.
     *
     * @return integer
     **/
    public static function labelToTypeId($label, $asset)
    {
        if (!$asset->parent) {
            return $asset->type;
        }

        switch ($asset->parent->type) {
            // LOGO VERSIONS
            case self::IMAGE_LOGO:
                switch ($label) {
                    case 'small':
                        return self::IMAGE_LOGO_SMALL;
                        break;
                }
                break;
            case self::IMAGE_GALLERY_ITEM:
                // THUMBNAIL VERSION
                switch ($label) {
                    case 'thumbnail':
                        return self::IMAGE_GALLERY_ITEM_THUMBNAIL;
                        break 2;
                }
                break;
        }

        return false;
    }
}
