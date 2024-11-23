<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourRequest;
use App\Http\Resources\TourResource;
use App\Models\Travel;

class TourController extends Controller
{
    public function index(Travel $travel,TourRequest $request)
    {
        $tour=$travel->tour()
            ->when( $request->priceFrom, function($query) use ($request){
                $query->where('price','>=',$request->priceFrom*100);
            })
            ->when( $request->priceTo, function($query) use ($request){
                $query->where('price','<=',$request->priceTo*100);
            })
            ->when( $request->dateFrom, function($query) use ($request){
                $query->where('starting_date','>=',$request->dateFrom);
            })
            ->when( $request->dateTo, function($query) use ($request){
                $query->where('starting_date','<=',$request->dateTo);
            })
            ->when( $request->sortBy && $request->sortOrder, function($query) use ($request){
                $query->orderBy($request->sortBy && $request->sortOrder);
            })
            ->orderBy('starting_date')
            ->paginate();
        return TourResource::collection($tour);
    }
}
