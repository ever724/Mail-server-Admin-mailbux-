<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Application\CreditNote\Store;
use App\Http\Requests\Application\CreditNote\Update;
use App\Http\Resources\CreditNoteResource;
use App\Interfaces\CreditNoteInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CreditNoteController extends BaseController
{
    // Resource
    public $resource = CreditNoteResource::class;

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
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Gate::authorize('view credit notes');

        $credit_notes = $this->repository->getPaginatedFilteredCreditNotes($request);

        return $this->sendCollectionResponse($credit_notes, true, 200);
    }

    /**
     * Store a newly created resource in database.
     *
     * @param Store $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Store $request)
    {
        Gate::authorize('create credit note');

        // Store Credit Note
        $credit_note = $this->repository->createCreditNote($request);

        return $this->sendResponse($credit_note, true, 201, [
            'message' => __('messages.credit_note_added'),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        Gate::authorize('view credit notes');

        $credit_note = $this->repository->getCreditNoteById($request, $request->credit_note);

        return $this->sendResponse($credit_note, true, 200);
    }

    /**
     * Send email to customer.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        Gate::authorize('update credit note');

        // Send email to customer
        if ($this->repository->sendCreditNoteEmail($request, $request->credit_note)) {
            return $this->sendResponse(null, true, 200, [
                'message' => __('messages.an_email_sent_to_customer'),
            ]);
        }

        return $this->sendResponse(null, false, 500, [
            'message' => session()->get('alert-danger'),
        ]);
    }

    /**
     * Update the status of the specified resource in database.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function mark(Request $request)
    {
        Gate::authorize('update credit note');

        // Mark the Credit Note Status
        $credit_note = $this->repository->markCreditNoteStatus($request, $request->credit_note);

        return $this->sendResponse($credit_note, true, 200, [
            'message' => __('messages.credit_note_status_updated'),
        ]);
    }

    /**
     * Update the specified resource in database.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Update $request)
    {
        Gate::authorize('update credit note');

        // Update Credit Note
        $credit_note = $this->repository->updateCreditNote($request, $request->credit_note);

        return $this->sendResponse($credit_note, true, 200, [
            'message' => __('messages.credit_note_updated'),
        ]);
    }

    /**
     * Delete the specified resource from database.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        Gate::authorize('delete credit note');

        // Delete the Credit Note
        if ($this->repository->deleteCreditNote($request, $request->credit_note)) {
            return $this->sendResponse(null, true, 200, [
                'message' => __('messages.credit_note_deleted'),
            ]);
        }

        return $this->sendResponse(null, false, 500, [
            'message' => session()->get('alert-danger'),
        ]);
    }

    /**
     * @return string
     */
    protected function resource(): string
    {
        return $this->resource;
    }
}
