<?php namespace Organization\Http\Controllers;

use Shared\Http\Controllers\Controller;
use Jakten\Models\{
    School, SchoolImage
};
use Jakten\Services\{KKJTelegramBotService, SchoolGalleryService};
use Admin\Http\Requests\{
    StoreSchoolImageRequest
};
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SchoolController
 * @package Organization\Http\Controllers
 */
class SchoolGalleryController extends Controller
{
    private $galleryService;

    public function __construct(SchoolGalleryService $galleryService, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->galleryService = $galleryService;
    }

    /**
     * @param School $school
     * @param StoreSchoolImageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(School $school, StoreSchoolImageRequest $request)
    {
        if ($this->galleryService->storeSchoolImage($school, $request)) {
            $request->session()->flash('message', 'Ladda upp framgång!');
        } else {
            $request->session()->flash('message', 'Uppladdning misslyckas!');
        }

        return redirect()->back();
    }

    /**
     * @param SchoolImage $schoolImage
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete(SchoolImage $schoolImage, Request $request)
    {
        if ($this->galleryService->deleteSchoolImage($schoolImage)) {
            $request->session()->flash('message', 'Ta bort framgång!');
        } else {
            $request->session()->flash('message', 'Radera misslyckas!');
        }
        return redirect()->back();
    }
}
