<?php namespace Jakten\Services;

use Illuminate\Foundation\Http\FormRequest;
use Jakten\Models\ContactRequest;
use Jakten\Models\Page;
use Jakten\Models\PageUri;
use Jakten\Repositories\Contracts\PageUriRepositoryContract;

/**
 * Class PageService
 * @package Jakten\Services
 */
class PageService
{
    /**
     * @var ModelService
     */
    private $modelService;

    /**
     * @var PageUriRepositoryContract
     */
    private $pageUris;

    /**
     * PageService constructor.
     *
     * @param ModelService $modelService
     * @param PageUriRepositoryContract $pageUris
     */
    public function __construct(ModelService $modelService, PageUriRepositoryContract $pageUris)
    {
        $this->modelService = $modelService;
        $this->pageUris = $pageUris;
    }

    /**
     * @param FormRequest $request
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function storePage(FormRequest $request)
    {
        $page = $this->modelService->createModel(Page::class, $request->all());
        $page->save();

        $uri = $request->input('uri');
        $this->handleUris($page, $uri);

        return $page;
    }

    /**
     * @param Page $page
     * @param FormRequest $request
     * @return \Illuminate\Database\Eloquent\Model|Page
     * @throws \Exception
     */
    public function updatePage(Page $page, FormRequest $request)
    {
        $page = $this->modelService->updateModel($page, $request->all());
        $page->save();

        $uri = $request->input('uri');
        $this->handleUris($page, $uri);

        return $page;
    }

    /**
     * @param Page $page
     * @param $uri
     * @throws \Exception
     */
    private function handleUris(Page $page, $uri)
    {
        $this->pageUris->reset()->query()
            ->where('page_id', $page->id)
            ->where('status', PageUri::ACTIVE)
            ->update([
                'status' => PageUri::REDIRECT,
            ]);

        $uri = $this->pageUris->reset()->byUri($uri)->query()->firstOr(function () use ($page, $uri) {
            $uri = $this->modelService->createModel(PageUri::class, ['page_id' => $page->id, 'uri' => $uri]);

            return $uri;
        });

        $uri->status = PageUri::ACTIVE;
        $uri->save();
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
