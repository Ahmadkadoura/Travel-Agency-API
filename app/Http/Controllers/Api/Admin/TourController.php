<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorTourRequest;
use App\Http\Resources\TourResource;
use App\Models\Travel;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function store(Travel $travel,StorTourRequest $request)
    {
        $tour=$travel->tour()->create($request->validated());
        return new TourResource($tour);
    }

   
}