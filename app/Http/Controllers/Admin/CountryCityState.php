<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\State;


class CountryCityState extends Controller
{
    public function getState(Request $request)
    {   
       
        $states = State::where('country_id', $request->country)->get();
        return response()->json($states);
    }
}
