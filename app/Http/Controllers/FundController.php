<?php

namespace App\Http\Controllers;

use App\Models\Fund;
use App\Models\Transaction;
use App\Responses\ServerError;
use Illuminate\Http\Request;

class FundController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Fund  $fund
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return response()->json(["fund" => $request->user()->fund]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Fund  $fund
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $attrs = $this->validate($request, [
            "balance" => "required|numeric"
        ]);

        $isUpdateSuccess = Fund::where("user_id", $request->user()->id)
            ->update(["balance" => $attrs["balance"]]);

        $updatedFund = $request->user()->fund;

        return ($isUpdateSuccess && $updatedFund) ?
            response()->json(["updated_fund" => $updatedFund, "message" => "success updating"]) :
            response(["error_message" => ServerError::message()], 500);
    }
}
