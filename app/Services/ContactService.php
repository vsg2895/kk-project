<?php namespace Jakten\Services;

use Illuminate\Foundation\Http\FormRequest;
use Jakten\Models\ContactRequest;
use Jakten\Repositories\Contracts\PageUriRepositoryContract;

/**
 * Class ContactService
 * @package Jakten\Services
 */
class ContactService
{
    /**
     * @var ModelService
     */
    private $modelService;

    /**
     * PageService constructor.
     *
     * @param ModelService $modelService
     * @param PageUriRepositoryContract $pageUris
     */
    public function __construct(ModelService $modelService)
    {
        $this->modelService = $modelService;
    }

    /**
     * @param FormRequest $request
     *
     * @return ContactRequest
     */
    public function contactStore(FormRequest $request)
    {
        $contactRequest = $this->modelService->createModel(ContactRequest::class, $request->all());
        $contactRequest->save();

        return $contactRequest;
    }
}
