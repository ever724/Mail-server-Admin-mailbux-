<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface InvoiceInterface
{
    public function getPaginatedFilteredInvoices(Request $request);

    public function newInvoice(Request $request);

    public function createInvoice(Request $request);

    public function getInvoiceById(Request $request, $invoice_id);

    public function updateInvoice(Request $request, $invoice_id);

    public function sendInvoiceEmail(Request $request, $invoice_id);

    public function markInvoiceStatus(Request $request, $invoice_id);

    public function deleteInvoice(Request $request, $invoice_id);
}
