<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\CreditNote\RefundStore;
use App\Interfaces\CreditNoteInterface;
use App\Interfaces\CreditNoteRefundInterface;
use Illuminate\Http\Request;

class CreditNoteRefundController extends Controller
{
    // Repository
    private $credit_note_repository;

    private $credit_note_refund_repository;

    /**
     * Controller constructor.
     *
     * @param CreditNoteInterface       $credit_note_repository
     * @param CreditNoteRefundInterface $credit_note_refund_repository
     */
    public function __construct(CreditNoteInterface $credit_note_repository, CreditNoteRefundInterface $credit_note_refund_repository)
    {
        $this->credit_note_repository = $credit_note_repository;
        $this->credit_note_refund_repository = $credit_note_refund_repository;
    }

    /**
     * Display the create credit note refund page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('application.credit_notes.create_refund', [
            'credit_note' => $this->credit_note_repository->getCreditNoteById($request, $request->credit_note),
        ]);
    }

    /**
     * Store the create credit note refund.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(RefundStore $request)
    {
        $credit_note = $this->credit_note_repository->getCreditNoteById($request, $request->credit_note);

        // Store refund in database
        $this->credit_note_refund_repository->createCreditNoteRefund($request, $credit_note);

        session()->flash('alert-success', __('messages.refund_issued'));

        return redirect()->route('credit_notes.details', [
            'credit_note' => $credit_note->id,
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Delete the create credit note refund.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(Request $request)
    {
        $credit_note = $this->credit_note_repository->getCreditNoteById($request, $request->credit_note);

        // Delete refund
        if ($this->credit_note_refund_repository->deleteCreditNoteRefund($request, $request->refund)) {
            session()->flash('alert-success', __('messages.refund_deleted'));

            return redirect()->route('credit_notes.details', [
                'credit_note' => $credit_note->id,
                'company_uid' => $request->currentCompany->uid,
            ]);
        }

        return redirect()->back();
    }
}
