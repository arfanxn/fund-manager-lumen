<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Responses\ServerError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        return response([
            "transactions" => $request->user()
                ->allTransactions()->orderBy("created_at", "desc")
                ->simplePaginate(10),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attrs = $this->validate($request, [
            "amount" => "required|numeric",
            "type" => "required|in:INCOME,EXPENSE",
            "note" => "nullable|string|max:200",
            "date" => "required|date",
        ]);

        $transaction = Transaction::create([
            "user_id" => $request->user()->id,
            "amount" => $attrs["amount"],
            "type" =>  $attrs["type"],
            "note" => $attrs["note"],
            "date" => $attrs["date"],
        ]);

        return $transaction ?  response()->json([
            "message" => "a new transaction has been created.", "transaction" => $transaction
        ]) : response(["error_message" => ServerError::message()], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $attrs = $this->validate($request, [
            "id" => "required",
            "amount" => "required|numeric",
            "type" => "required|in:INCOME,EXPENSE",
            "note" => "nullable|string|max:200",
            "date" => "nullable|date",
        ]);

        $isUpdateSuccess = Transaction::where(function ($query) use ($attrs, $request) {
            return $query->where("id", $attrs["id"])
                ->where("user_id", $request->user()->id);
        })->update([
            "amount" => $attrs["amount"],
            "type" =>  $attrs["type"],
            "note" => $attrs["note"],
            "date" => $attrs["date"],
        ]);

        $transaction = Transaction::where("id", $attrs["id"])->first();

        return ($isUpdateSuccess && $transaction) ?  response()->json([
            "message" => "a transaction has been updated.", "transaction" => $transaction
        ]) : response(["error_message" => ServerError::message()], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $this->validate($request, [
            "transaction_id" => "required",
        ]);

        $isDeleteSuccess = Transaction::where("user_id", $request->user()->id)
            ->where("id", $id["transaction_id"])->delete();

        return $isDeleteSuccess ? response(200) : response(ServerError::message(), 500);
    }
}
