<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Application\CreditNote\RefundStore;
use App\Http\Resources\CreditNoteRefundResource;
use App\Interfaces\CreditNoteInterface;
use App\Interfaces\CreditNoteRefundInterface;
use Illuminate\Http\Request;

class CreditNoteRefundController extends BaseController
{
    // Resource
    public $resource = CreditNoteRefundResource::class;

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
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $credit_note = $this->credit_note_repository->getCreditNoteById($request, $request->credit_note);

        $refunds = $this->credit_note_refund_repository->getPaginatedFilteredCreditNoteRefundsByCreditNote($request, $credit_note->id);

        return $this->sendCollectionResponse($refunds, true, 200);
    }

    /**
     * Store a newly created resource in database.
     *
     * @param RefundStore $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RefundStore $request)
    {
        $credit_note = $this->credit_note_repository->getCreditNoteById($request, $request->credit_note);

        // Store refund in database
        $refund = $this->credit_note_refund_repository->createCreditNoteRefund($request, $credit_note);

        return $this->sendResponse($refund, true, 201, [
            'message' => __('messages.refund_issued'),
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
        $credit_note = $this->credit_note_repository->getCreditNoteById($request, $request->credit_note);

        $refund = $this->credit_note_refund_repository->getCreditNoteRefundById($request, $request->refund);

        return $this->sendResponse($refund, true, 200);
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
        $credit_note = $this->credit_note_repository->getCreditNoteById($request, $request->credit_note);

        // Delete refund
        if ($this->credit_note_refund_repository->deleteCreditNoteRefund($request, $request->refund)) {
            return $this->sendResponse(null, true, 200, [
                'message' => __('messages.refund_deleted'),
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
