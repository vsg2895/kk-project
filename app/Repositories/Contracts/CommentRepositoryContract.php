<?php namespace Jakten\Repositories\Contracts;

/**
 * Interface CommentRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface CommentRepositoryContract extends BaseRepositoryContract
{
    /**
     * @param int $postId
     * @return \Illuminate\Database\Eloquent\Builder|CommentRepositoryContract
     */
    public function wherePostId(int $postId);
}
