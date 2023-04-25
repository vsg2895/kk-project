<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Model;
use Jakten\Models\Asset;
use Jakten\Repositories\Contracts\AssetRepositoryContract;

/**
 * Class AssetRepository
 * @package Jakten\Repositories
 */
class AssetRepository extends BaseRepository implements AssetRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return Asset::class;
    }
}
