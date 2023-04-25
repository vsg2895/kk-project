<?php namespace Jakten\Observers;

use Illuminate\Support\Facades\Storage;
use Jakten\Models\Post;

/**
 * Class PostObserver
 *
 * @package Jakten\Observers
 */
class PostObserver
{
    /**
     * Handle the Post "deleted" event.
     *
     * @param Post $post
     */
    public function deleted(Post $post)
    {
        Storage::delete($post->preview_img_filename);
    }
}
