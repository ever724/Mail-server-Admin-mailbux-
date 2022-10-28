<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface CreditNoteInterface
{
    public function getPaginatedFilteredCreditNotes(Request $request);

    public function newCreditNote(Request $request);

    public function createCreditNote(Request $request);

    public function getCreditNoteById(Request $request, $credit_note_id);

    public function updateCreditNote(Request $request, $credit_note_id);

    public function sendCreditNoteEmail(Request $request, $credit_note_id);

    public function markCreditNoteStatus(Request $request, $credit_note_id);

    public function deleteCreditNote(Request $request, $credit_note_id);
}
