<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\CreditNote\Store;
use App\Http\Requests\Application\CreditNote\Update;
use App\Interfaces\CreditNoteInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CreditNoteController extends Controller
{
    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param CreditNoteInterface $repository
     */
    public function __construct(CreditNoteInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display CreditNotes Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Gate::authorize('view credit notes');

        return view('application.credit_notes.index', [
            'credit_notes' => $this->repository->getPaginatedFilteredCreditNotes($request),
        ]);
    }

    /**
     * Display the Form for Creating New CreditNote.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Gate::authorize('create credit note');

        $credit_note = $this->repository->newCreditNote($request);

        return view('application.credit_notes.create', [
            'credit_note' => $credit_note,
            'customers' => $request->currentCompany->customers,
            'products' => $request->currentCompany->products,
            'tax_per_item' => (bool) $request->currentCompany->getSetting('tax_per_item'),
            'discount_per_item' => (bool) $request->currentCompany->getSetting('discount_per_item'),
        ]);
    }

    /**
     * Store the CreditNote in Database.
     *
     * @param \App\Http\Requests\Application\CreditNote\Store $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Store $request)
    {
        Gate::authorize('create credit note');

        // Store Credit Note
        $credit_note = $this->repository->createCreditNote($request);

        session()->flash('alert-success', __('messages.credit_note_added'));

        return redirect()->route('credit_notes.details', [
            'credit_note' => $credit_note->id,
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Display the CreditNote Details Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        Gate::authorize('view credit notes');

        $credit_note = $this->repository->getCreditNoteById($request, $request->credit_note);

        return view('application.credit_notes.details', [
            'credit_note' => $credit_note,
            'payments' => $credit_note->applied_payments()->orderBy('payment_number')->paginate(50),
            'refunds' => $credit_note->refunds()->paginate(50),
        ]);
    }

    /**
     * Send an email to customer about the Credit Note.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function send(Request $request)
    {
        Gate::authorize('update credit note');

        // Send email to customer
        if ($this->repository->sendCreditNoteEmail($request, $request->credit_note)) {
            session()->flash('alert-success', __('messages.an_email_sent_to_customer'));
        }

        return redirect()->back();
    }

    /**
     * Change Status of the Credit Note by Given Status.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function mark(Request $request)
    {
        Gate::authorize('update credit note');

        // Mark the Credit Note Status
        $this->repository->markCreditNoteStatus($request, $request->credit_note);

        session()->flash('alert-success', __('messages.credit_note_status_updated'));

        return redirect()->back();
    }

    /**
     * Display the Form for Editing Credit Note.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        Gate::authorize('update credit note');

        $credit_note = $this->repository->getCreditNoteById($request, $request->credit_note);

        return view('application.credit_notes.edit', [
            'credit_note' => $credit_note,
            'customers' => $request->currentCompany->customers,
            'products' => $request->currentCompany->products,
            'tax_per_item' => $credit_note->tax_per_item,
            'discount_per_item' => $credit_note->discount_per_item,
        ]);
    }

    /**
     * Update the Credit Note in Database.
     *
     * @param \App\Http\Requests\Application\CreditNote\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update credit note');

        // Update Credit Note
        $credit_note = $this->repository->updateCreditNote($request, $request->credit_note);

        session()->flash('alert-success', __('messages.credit_note_updated'));

        return redirect()->route('credit_notes.details', [
            'credit_note' => $credit_note->id,
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Delete the Credit Note.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(Request $request)
    {
        Gate::authorize('delete credit note');

        // Delete the Credit Note
        if ($this->repository->deleteCreditNote($request, $request->credit_note)) {
            session()->flash('alert-success', __('messages.credit_note_deleted'));

            return redirect()->route('credit_notes', [
                'company_uid' => $request->currentCompany->uid,
            ]);
        }

        return redirect()->back();
    }
}
