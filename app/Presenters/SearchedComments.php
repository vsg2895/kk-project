<?php namespace Jakten\Presenters;

use Illuminate\Support\Collection;
use Jakten\Models\Comment;

/**
 * Class SearchedComments
 *
 * @package Jakten\Presenters
 */
class SearchedComments
{
    /**
     * @param Collection $comments
     * @return Collection
     */
    public function format(Collection $comments)
    {
        return $comments->map(function (Comment $comment) {
            $data = $comment->toArray();

            $data['user'] = [
                'id' => $data['user']['id'],
                'name' => $data['user']['name'],
            ];

            $data = array_except($data, 'user_id');

            return $data;
        });
    }
}
