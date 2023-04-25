<?php namespace Admin\Http\Controllers;

use Jakten\Repositories\Contracts\ContactRequestRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Shared\Http\Controllers\Controller;

/**
 * Class ContactRequestController
 * @package Admin\Http\Controllers
 */
class ContactRequestController extends Controller
{
    /**
     * @var ContactRequestRepositoryContract
     */
    private $requests;

    /**
     * ContactRequestController constructor.
     *
     * @param ContactRequestRepositoryContract $requests
     * @param KKJTelegramBotService $botService
     */
    public function __construct(ContactRequestRepositoryContract $requests, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->requests = $requests;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin::contact_requests.index', [
            'requests' => $this->requests->query()->orderBy('created_at', 'desc')->paginate(10),
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        return view('admin::contact_requests.show', [
            'request' => $this->requests->query()->findOrFail($id),
        ]);
    }
}
