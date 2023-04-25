<?php namespace Api\Http\Controllers;

use Api\Http\Requests\StoreInvoiceRequest;
use Illuminate\Http\Request;
use Jakten\Repositories\Contracts\InvoiceRepositoryContract;
use Jakten\Services\InvoiceService;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class InvoiceController
 * @package Api\Http\Controllers
 */
class InvoiceController extends ApiController
{
    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var InvoiceRepositoryContract
     */
    private $invoices;

    /**
     * InvoiceController constructor.
     *
     * @param InvoiceService $invoiceService
     * @param InvoiceRepositoryContract $invoices
     * @param KKJTelegramBotService $botService
     */
    public function __construct(InvoiceService $invoiceService, InvoiceRepositoryContract $invoices, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->invoiceService = $invoiceService;
        $this->invoices = $invoices;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request)
    {
        $invoices = $this->invoices->query()->get();

        return $this->success($invoices);
    }

    /**
     * @param StoreInvoiceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreInvoiceRequest $request)
    {
        $invoice = $this->invoiceService->storeInvoice($request);

        return $this->success($invoice);
    }

    /**
     * @param $invoiceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function sent($invoiceId)
    {
        $invoice = $this->invoices->query()->findOrFail($invoiceId);
        $invoice = $this->invoiceService->sent($invoice);

        return $invoice->isDirty(['sent_at']) ?
            $this->success($invoice) :
            $this->error($invoice);
    }

    /**
     * @param $invoiceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function paid($invoiceId)
    {
        $invoice = $this->invoices->query()->findOrFail($invoiceId);
        $invoice = $this->invoiceService->paid($invoice);

        return $invoice->isDirty(['paid_at']) ?
            $this->success($invoice) :
            $this->error($invoice);
    }
}
