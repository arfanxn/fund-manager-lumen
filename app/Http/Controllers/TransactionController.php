<?php

namespace App\Http\Controllers;

use App\Models\Fund;
use App\Models\Transaction;
use App\Responses\ServerError;
use Illuminate\Database\QueryException;
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

        try {
            DB::beginTransaction();

            $transaction = Transaction::create([
                "user_id" => $request->user()->id,
                "amount" => $attrs["amount"],
                "type" =>  $attrs["type"],
                "note" => $attrs["note"],
                "date" => $attrs["date"],
            ]);

            $fundQueryBuilder =  Fund::where("user_id", $request->user()->id);

            $attrs["type"] == Transaction::INCOME ? $fundQueryBuilder->increment("balance", $attrs["amount"])
                : $fundQueryBuilder->decrement("balance", $attrs["amount"]);

            DB::commit();

            return $transaction  ?
                response()->json([
                    "message" => "a new transaction has been created.",
                    "transaction" => $transaction
                ]) :
                response(["error_message" => ServerError::message()], 500);
        } catch (QueryException $e) {
            DB::rollBack();
            return response(["error_message" => $e->getMessage()], 500);
        }
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
        // return dd($request->all());

        $attrs = $this->validate($request, [
            "id" => "required",
            "amount" => "required|numeric",
            // "type" => "required|in:INCOME,EXPENSE", // type is not editable/updateable
            "note" => "nullable|string|max:200",
            "date" => "nullable|date",
        ]);

        try {
            DB::beginTransaction();

            $fundQueryBuilder = Fund::where("user_id", $request->user()->id);

            $transactionQueryBuilder =  Transaction::where(
                function ($query) use ($attrs, $request) {
                    return $query->where("id", $attrs["id"])
                        ->where("user_id", $request->user()->id);
                }
            );

            $prevTransactionData = $transactionQueryBuilder->first();
            if (!$prevTransactionData) return
                response(["error_message" => ServerError::message()], 500);

            if ($prevTransactionData->type == Transaction::INCOME) {
                if (($prevTransactionData->amount) < $attrs["amount"]) {
                    $decrementBy =  intval(($prevTransactionData->amount) - $attrs["amount"]);
                    $fundQueryBuilder->decrement("balance", $decrementBy);
                } else {
                    $decrementBy =  ($prevTransactionData->amount) - $attrs["amount"];
                    $fundQueryBuilder->decrement("balance", $decrementBy);
                }
            } else {
                if (($prevTransactionData->amount) < $attrs["amount"]) {
                    $incrementBy =  intval(($prevTransactionData->amount) - $attrs["amount"]);
                    $fundQueryBuilder->increment("balance", $incrementBy);
                } else {
                    $incrementBy =  ($prevTransactionData->amount) - $attrs["amount"];
                    $fundQueryBuilder->increment("balance", $incrementBy);
                }
            }

            $isUpdateTransactionSuccess = $transactionQueryBuilder->update([
                "amount" => $attrs["amount"],
                "note" => $attrs["note"],
                "date" => $attrs["date"],
            ]);

            $updatedTransactionData = $transactionQueryBuilder->first();

            DB::commit();

            return ($isUpdateTransactionSuccess && $updatedTransactionData) ?
                response()->json([
                    "message" => "a transaction has been updated.",
                    "transaction" => $updatedTransactionData
                ]) : response(["error_message" => ServerError::message()], 500);
        } catch (QueryException $e) {
            DB::rollBack();
            return response(["error_message" => $e->getMessage()], 500);
        }
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

        try {
            DB::beginTransaction();

            $transactionQueryBuilder = Transaction::where(function ($query) use ($request, $id) {
                return $query->where("user_id", $request->user()->id)
                    ->where("id", $id["transaction_id"]);
            });

            $fundQueryBuilder = $request->user()->fund();

            $transactionData = $transactionQueryBuilder->first();

            $transactionData->type == Transaction::INCOME ? $fundQueryBuilder
                ->decrement("balance", $transactionData->amount) :
                $fundQueryBuilder->increment("balance", $transactionData->amount);

            $isDeleteSuccess = $transactionQueryBuilder->delete();

            DB::commit();
            return $isDeleteSuccess ? response("success deleting.", 200)
                : response(ServerError::message(), 500);
        } catch (QueryException $e) {
            DB::rollBack();
            return response(["error_message" => $e->getMessage()], 500);
        }
    }
}
