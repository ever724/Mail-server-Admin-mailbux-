<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface EstimateInterface
{
    public function getPaginatedFilteredEstimates(Request $request);

    public function getEstimateById(Request $request, $estimate_id);

    public function newEstimate(Request $request);

    public function createEstimate(Request $request);

    public function updateEstimate(Request $request, $estimate_id);

    public function sendEstimateEmail(Request $request, $estimate_id);

    public function markEstimateStatus(Request $request, $estimate_id);

    public function convertEstimateToInvoice(Request $request, $estimate_id);

    public function deleteEstimate(Request $request, $estimate_id);
}
