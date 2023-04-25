<?php namespace Jakten\Services\Asset\Strategy;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Jakten\Models\Asset;

/**
 * Abstract image file strategy wrapper.
 **/
abstract class ImageAbstract
{
    public $image;
    public $originalFile;
    public $path;
    public $filePath;
    public $mime;
    public $type;

    /**
     * ImageAbstract constructor.
     * @param UploadedFile $image
     */
    public function __construct(UploadedFile $image)
    {
        $this->originalFile = $image;

        // store original file
        $this->path = $image->store($this->getLabel(), 'upload');
        $this->filePath = storage_path('app/public/upload/' . $this->path);

        // create intervention instance to modify image
        $this->image = Image::make($this->filePath);
        $this->mime = $image->getMimeType();

        // trigger strategy modifications
        $this->modify();
    }

    /**
     * Modify the image here.
     **/
    abstract public function modify();

    /**
     * Generate alternative versions of the image.
     **/
    abstract public function generateVersions(Asset $asset);

    /**
     * Get the asset type.
     **/
    abstract public function getType();

    /**
     * Get the asset label. Is used for upload folder name.
     **/
    abstract public function getLabel();

    /**
     * Get the file mime type.
     **/
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * Get the default background color.
     **/
    public function getBackgroundColor()
    {
        if ($this->image->mime == "image/png" || $this->image->extension == 'png') {
            return [255,255,255,0];
        }

        return '#ffffff';
    }

    /**
     * Expands the image canvas to enforce specific dimensions.
     **/
    protected function adjustToAspectRatio($width, $height, $color = false) {
        $color = $color ? $color : $this->getBackgroundColor();
        $ratio = $width / $height;

        // current image dimensions
        $w = $this->image->width();
        $h = $this->image->height();
        $r = $w / $h;

        if ($ratio == $r) {
            // image already has correct aspect ratio
            return;
        }

        $wr = $width / $w;
        $hr = $height / $h;

        if ($height / $wr > $h) {
            // heighten canvas to fit the chosen aspect ratio
            $this->image->resizeCanvas($w, intval(($height / $wr)), 'center', false, $color);
        }else if (($width / $hr) > $w) {
            // widen canvas to fit the chosen aspect ratio
            $this->image->resizeCanvas(intval(($width / $hr)), $h, 'center', false, $color);
        }
    }

    /**
     * Makes sure that an image fits inside predefined dimensions,
     * without affecting the aspect ratio.
     **/
    protected function fitInsideBox($width, $height) {
        $w = $this->image->width();
        $h = $this->image->height();

        $upsize = function ($constraint) {
            $constraint->upsize();
        };

        if ($w < $width && $h < $height) {
            return;
        }else if ($w > $h) {
            // landscape picture
            $this->image->widen($width, $upsize);
        }else {
            // portrait or square picture
            $this->image->heighten($height, $upsize);
        }
    }
} // END abstract class ImageAbstract
