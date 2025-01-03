<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\travelResource;
use App\Models\Travel;
use Illuminate\Http\Request;

class TravelController extends Controller
{
    public function index()
    {

        $travels=Travel::where('is_public',true)->paginate();
        return travelResource::collection($travels);

    }
}
