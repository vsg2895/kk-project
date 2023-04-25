<?php namespace Jakten\Observers;

use Illuminate\Support\Facades\Storage;
use Jakten\Models\PostMultimedia;

/**
 * Class PostMultimediaObserver
 *
 * @package Jakten\Observers
 */
class PostMultimediaObserver
{
    /**
     * Handle the PostMultimedia "deleted" event.
     *
     * @param PostMultimedia $multimedia
     */
    public function deleted(PostMultimedia $multimedia)
    {
        if ($multimedia->isImage()) {
            Storage::delete($multimedia->path);
        }
    }
}
