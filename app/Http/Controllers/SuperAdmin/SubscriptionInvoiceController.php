<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\SubscriptionInvoice;
use Barryvdh\DomPDF\PDF;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class SubscriptionInvoiceController extends Controller
{
    /**
     * @var PDF
     */
    private $pdfService;

    public function __construct(PDF $pdf)
    {
        $this->pdfService = $pdf;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $filters = $request->input('filter', []);

        $invoiceQuery = SubscriptionInvoice::query();

        if (isset($filters['client_name'])) {
            $filterClientName = function ($query) use ($filters) {
                $query->whereHas('client', function ($query) use ($filters) {
                    $query->where('name', 'LIKE', '%' . $filters['client_name'] . '%');
                });
            };
        }

        if (isset($filters['this_month'])) {
            $invoiceQuery->whereBetween('paid_at', [now()->firstOfMonth(), now()->lastOfMonth()]);
        }

        if (isset($filters['not_trials'])) {
            $excludeTrial = filter_var($filters['not_trials'], FILTER_VALIDATE_BOOLEAN);

            if ($excludeTrial) {
                $invoiceQuery->where('status', '!=', SubscriptionInvoice::STATUS_TRIAL);
            }
        }

        $invoiceQuery->whereHas('subscription', $filterClientName ?? null);

        return view(
            'super_admin.invoices.index',
            ['invoices' => $invoiceQuery->paginate()]
        );
    }

    /**
     * @param Request             $request
     * @param Client              $client
     * @param SubscriptionInvoice $invoice
     *
     * @return Application|Factory|View
     */
    public function html(Request $request, Client $client, SubscriptionInvoice $invoice)
    {
        $data = $invoice->subscription->meta_data;

        $metaData = function (string $key) use ($data) {
            return Arr::get($data, $key);
        };

        return view('super_admin.invoices.customer-invoice', [
            'invoice' => $invoice,
            'metaData' => $metaData,
            'isPdf' => false,
        ]);
    }

    /**
     * @param Client              $client
     * @param SubscriptionInvoice $invoice
     *
     * @return Response
     */
    public function pdf(Client $client, SubscriptionInvoice $invoice)
    {
        $data = $invoice->subscription->meta_data;

        $metaData = function (string $key) use ($data) {
            return Arr::get($data, $key);
        };

        return $this->pdfService
            ->loadView('super_admin.invoices.customer-invoice', [
                'invoice' => $invoice,
                'metaData' => $metaData,
                'isPdf' => true,
            ])
            ->stream();
    }

    /**
     * @param Client              $client
     * @param SubscriptionInvoice $invoice
     *
     * @return JsonResponse|Response
     */
    public function downloadPdf(Client $client, SubscriptionInvoice $invoice)
    {
        $data = $invoice->subscription->meta_data;

        $metaData = function (string $key) use ($data) {
            return Arr::get($data, $key);
        };

        return $this->pdfService
            ->loadView('super_admin.invoices.customer-invoice', [
                'invoice' => $invoice,
                'metaData' => $metaData,
                'isPdf' => true,
            ])
            ->download(sprintf('mailbux-invoice-%s.pdf', $invoice->order_number));
    }
}
