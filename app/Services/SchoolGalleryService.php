<?php

namespace Jakten\Services;

use Illuminate\Support\Facades\Storage;
use Jakten\Models\School;
use Jakten\Models\SchoolImage;
use Jakten\Services\Asset\AssetService;
use Jakten\Services\Asset\Strategy\ImageGallery;
use Symfony\Component\HttpFoundation\Request;

class SchoolGalleryService
{
    /**
     * @var ModelService
     */
    private $modelService;

    /**
     * @var AssetService
     */
    private $assetService;

    /**
     * SchoolGalleryService constructor.
     * @param ModelService $modelService
     * @param AssetService $assetService
     */
    public function __construct(ModelService $modelService, AssetService $assetService)
    {
        $this->modelService = $modelService;
        $this->assetService = $assetService;
    }

    /**
     * @param School $school
     * @param Request $request
     * @return false|\Illuminate\Database\Eloquent\Model
     */
    public function storeSchoolImage(School $school, Request $request)
    {
        $input = $request->request->all();

        if ($request->hasFile('image')) {
            $savedImage = $this->assetService->storeImage(new ImageGallery($request->file('image')));
            $input['file_name'] = 'public/upload/' . $savedImage->path;
        }

        $schoolImage = new SchoolImage($input);

        return $school->images()->save($schoolImage);
    }

    /**
     * @param SchoolImage $schoolImage
     * @return bool|null
     * @throws \Exception
     */
    public function deleteSchoolImage(SchoolImage $schoolImage)
    {
        if (Storage::delete(str_replace('public/upload//storage/upload', 'public/upload', $schoolImage->file_name))) {
            return $schoolImage->delete();
        } else {
            return false;
        }
    }

}
