<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     //
    // }

    public function __invoke(Request  $request)
    {
        return response()->json([
            "from" => __CLASS__ . " and method is => " . __METHOD__,
            "request" => $request->all()
        ]);
    }
}
