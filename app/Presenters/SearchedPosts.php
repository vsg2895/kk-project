<?php namespace Jakten\Presenters;

use Illuminate\Support\Collection;
use Jakten\Models\Post;
use Jakten\Models\PostMultimedia;

/**
 * Class SearchedPosts
 * @package Jakten\Presenters
 */
class SearchedPosts
{
    /**
     * @param Post $post
     * @return array
     */
    public function formatModel(Post $post)
    {
        $data = $post->toArray();

        $data['previewImg'] = $post->previewImgFilenameUrl;

        $data['multimedia'] = $post->multimedia->map(function (PostMultimedia $multimedia) {
            return [
                'url' => $multimedia->multimediaUrl,
                'type' => $multimedia->type,
                'alt_text' => $multimedia->alt_text,
            ];
        });

        $data = array_except($data, 'preview_img_filename');

        return $data;
    }

    /**
     * @param Collection $posts
     * @return Collection
     */
    public function format(Collection $posts)
    {
        return $posts->map(function (Post $post) {
            return $this->formatModel($post);
        });
    }
}
