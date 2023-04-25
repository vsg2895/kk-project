<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Model;
use Jakten\Models\Post;
use Jakten\Repositories\Contracts\PostRepositoryContract;

/**
 * Class PostRepository
 * @package Jakten\Repositories
 */
class PostRepository extends BaseRepository implements PostRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return Post::class;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|PostRepositoryContract
     */
    public function isVisible()
    {
        $this->query()->where('status', 1);

        return $this;
    }

    public function getPostTypes(): array
    {
        return [
            'post' => 'post',
            'landing' => 'landing',
        ];
    }
}
