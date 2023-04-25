<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Model;
use Jakten\Models\Page;
use Jakten\Repositories\Contracts\PageRepositoryContract;

/**
 * Class PageRepository
 * @package Jakten\Repositories
 */
class PageRepository extends BaseRepository implements PageRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return Page::class;
    }

    /*
     * @param $slug
     *
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    // public function bySlug($slug)
    // {
    //     $id = substr($slug, strrpos($slug, '-') + 1);

    //     $this->query()->where('id', $id);

    //     return $this;
    // }
}
