<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Model;
use Jakten\Models\Comment;
use Jakten\Repositories\Contracts\CommentRepositoryContract;

/**
 * Class CommentRepository
 * @package Jakten\Repositories
 */
class CommentRepository extends BaseRepository implements CommentRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return Comment::class;
    }

    /**
     * @param int $postId
     * @return CommentRepositoryContract
     */
    public function wherePostId(int $postId)
    {
        $this->query()->where('post_id', $postId);

        return $this;
    }
}
