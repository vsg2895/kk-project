<?php namespace Jakten\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Jakten\Models\{Invoice, InvoiceRow};

/**
 * Class InvoiceService
 * @package Jakten\Services
 */
class InvoiceService
{
    /**
     * @var ModelService
     */
    private $modelService;

    /**
     * UserService constructor.
     *
     * @param ModelService $modelService
     * @param OrderService $orderService
     */
    public function __construct(ModelService $modelService)
    {
        $this->modelService = $modelService;
    }

    /**
     * @param FormRequest $request
     *
     * @return Invoice|Model
     */
    public function storeInvoice(FormRequest $request)
    {
        $invoice = $this->modelService->createModel(Invoice::class, ['school_id' => $request->input('school_id')]);
        $invoice->save();

        $rows = [];
        collect($request->input('rows'))->each(function ($row) use (&$rows) {
            $rows[] = $this->modelService->createModel(InvoiceRow::class, [
                'amount' => $row['amount'],
                'quantity' => $row['quantity'],
                'name' => $row['name'],
            ]);
        });

        $invoice->rows()->saveMany($rows);

        return $invoice;
    }

    /**
     * @param Invoice $invoice
     *
     * @return Invoice
     */
    public function sent(Invoice $invoice)
    {
        $invoice->sent_at = Carbon::now();
        $invoice->save();

        return $invoice;
    }

    /**
     * @param Invoice $invoice
     *
     * @return Invoice
     */
    public function paid(Invoice $invoice)
    {
        $invoice->paid_at = Carbon::now();
        $invoice->save();

        return $invoice;
    }
}
