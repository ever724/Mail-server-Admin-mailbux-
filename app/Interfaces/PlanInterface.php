<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface PlanInterface
{
    public function getPlans(Request $request);
}
