<?php namespace Admin\Http\Controllers;

use Jakten\Repositories\Contracts\InvoiceRepositoryContract;
use Jakten\Repositories\Contracts\OrderRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Shared\Http\Controllers\Controller;

/**
 * Class InvoiceController
 * @package Admin\Http\Controllers
 */
class InvoiceController extends Controller
{
    /**
     * @var OrderRepositoryContract
     */
    private $orders;

    /**
     * @var InvoiceRepositoryContract
     */
    private $invoices;

    /**
     * OrderController constructor.
     *
     * @param OrderRepositoryContract $orders
     * @param InvoiceRepositoryContract $invoices
     */
    public function __construct(OrderRepositoryContract $orders, InvoiceRepositoryContract $invoices, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->orders = $orders;
        $this->invoices = $invoices;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin::invoices.index');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $invoice = $this->invoices->query()->findOrFail($id);

        return view('admin::invoices.show', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * @param null $orderId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($orderId = null)
    {
        $order = null;
        if ($orderId) {
            $order = $this->orders->query()->findOrFail($orderId);
        }

        return view('admin::invoices.create', [
            'order' => $order,
        ]);
    }
}
