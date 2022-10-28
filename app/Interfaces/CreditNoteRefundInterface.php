<?php

namespace App\Interfaces;

use App\Models\CreditNote;
use Illuminate\Http\Request;

interface CreditNoteRefundInterface
{
    public function getPaginatedFilteredCreditNoteRefundsByCreditNote(Request $request, $credit_note_id);

    public function getCreditNoteRefundById(Request $request, $credit_note_refund_id);

    public function createCreditNoteRefund(Request $request, CreditNote $credit_note);

    public function deleteCreditNoteRefund(Request $request, $credit_note_refund_id);
}
