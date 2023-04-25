<?php namespace Jakten\Services\Asset;

use Jakten\Facades\Auth;
use Jakten\Models\Asset;
use Jakten\Repositories\Contracts\AssetRepositoryContract;
use Jakten\Services\Asset\Strategy\ImageAbstract;
use Jakten\Services\ModelService;

/**
 * Class AssetService
 * @package Jakten\Services\Asset
 */
class AssetService
{
    /**
     * @var AssetRepositoryContract
     */
    private $assets;

    /**
     * @var ModelService
     */
    private $modelService;

    /**
     * AssetService constructor.
     *
     * @param AssetRepositoryContract $assets
     * @param ModelService $modelService
     */
    public function __construct(AssetRepositoryContract $assets, ModelService $modelService)
    {
        $this->assets = $assets;
        $this->modelService = $modelService;
    }

    /**
     * Stores an image in the public upload folder and applies
     * modifications according to the strategy chosen.
     *
     * @param ImageAbstract $strategy
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function storeImage(ImageAbstract $strategy)
    {
        $path = $strategy->path;
        $strategy->image->save($strategy->filePath, 100);

        /** @var Asset $asset */
        $asset = $this->modelService->createModel(Asset::class, [
            'path' => $path,
            'mime' => $strategy->getMime(),
            'type' => $strategy->getType(),
            'author_id' => Auth::user()->id,
        ]);

        $asset->save();
        $strategy->generateVersions($asset);

        return $asset;
    }
}
