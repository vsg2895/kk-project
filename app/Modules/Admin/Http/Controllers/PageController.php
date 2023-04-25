<?php namespace Admin\Http\Controllers;

use Admin\Http\Requests\StorePageRequest;
use Admin\Http\Requests\UpdatePageRequest;
use Jakten\Models\Page;
use Jakten\Repositories\Contracts\PageRepositoryContract;
use Jakten\Repositories\Contracts\PageUriRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\PageService;
use Shared\Http\Controllers\Controller;

/**
 * Class PageController
 * @package Admin\Http\Controllers
 */
class PageController extends Controller
{
    /**
     * @var PageRepositoryContract
     */
    private $pages;

    /**
     * @var PageService
     */
    private $pageService;

    /**
     * PageController constructor.
     *
     * @param PageRepositoryContract $pages
     * @param PageService $pageService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(PageRepositoryContract $pages, PageService $pageService, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->pages = $pages;
        $this->pageService = $pageService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin::pages.index', [
            'pages' => $this->pages->query()->paginate(10),
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        return view('admin::pages.edit', [
            'page' => $this->pages->query()->findOrFail($id),
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin::pages.create', [
            'page' => new Page(),
        ]);
    }

    /**
     * @param StorePageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StorePageRequest $request)
    {
        $page = $this->pageService->storePage($request);

        $request->session()->flash('message', 'Sida skapad!');

        return redirect()->route('admin::pages.show', ['id' => $page->id]);
    }

    /**
     * @param $id
     * @param UpdatePageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, UpdatePageRequest $request)
    {
        $page = $this->pages->query()->findOrFail($id);
        $page = $this->pageService->updatePage($page, $request);

        $request->session()->flash('message', 'Sida uppdaterad!');

        return redirect()->route('admin::pages.show', ['id' => $page->id]);
    }
}
