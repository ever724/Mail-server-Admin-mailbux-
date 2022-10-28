<?php

namespace App\Repositories;

use App\Interfaces\CreditNoteRefundInterface;
use App\Models\CreditNoteRefund;
use App\Services\QueryBuilder\AllowedFilter;
use App\Services\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CreditNoteRefundRepository implements CreditNoteRefundInterface
{
    /**
     * Display a listing of the resource.
     *
     * @param mixed $credit_note_id
     */
    public function getPaginatedFilteredCreditNoteRefundsByCreditNote(Request $request, $credit_note_id)
    {
        return QueryBuilder::for(CreditNoteRefund::where('credit_note_id', $credit_note_id))
            ->allowedFilters([
                AllowedFilter::exact('payment_method_id'),
            ])
            ->allowedIncludes([
                'credit_note',
                'payment_method',
            ])
            ->paginate()->appends(request()->query());
    }

    /**
     * Display a listing of the resource.
     *
     * @param mixed $credit_note_refund_id
     */
    public function getCreditNoteRefundById(Request $request, $credit_note_refund_id)
    {
        return CreditNoteRefund::with(['credit_note', 'payment_method'])->findOrFail($credit_note_refund_id);
    }

    /**
     * Store a newly created resource in database.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\CreditNote   $credit_note
     *
     * @return \App\Models\CreditNoteRefund
     */
    public function createCreditNoteRefund(Request $request, $credit_note)
    {
        // Check payment amount less than credit note amount
        if ($request->amount > $credit_note->remaining_balance) {
            throw ValidationException::withMessages([
                'amount' => __('messages.invalid_amount'),
            ]);
        }

        // Create a refund on database
        return CreditNoteRefund::create([
            'credit_note_id' => $credit_note->id,
            'payment_method_id' => $request->payment_method_id,
            'refund_date' => $request->refund_date,
            'amount' => $request->amount,
            'notes' => $request->notes,
        ]);
    }

    /**
     * Delete the specified resource.
     *
     * @param int $credit_note_refund_id
     *
     * @return bool
     */
    public function deleteCreditNoteRefund(Request $request, $credit_note_refund_id)
    {
        return CreditNoteRefund::destroy($credit_note_refund_id);
    }
}
