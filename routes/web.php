<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get("api/example", "ExampleController");

$router->group(["prefix" => "api"], function () use ($router) {
    $router->post("login", "AuthController@login");
    $router->post("register", "AuthController@register");

    $router->group(["middleware" => "auth"], function () use ($router) {
        $router->get("logout", "AuthController@logout");
        $router->get("isAuthenticated", "AuthController@isAuth");

        $router->group(["prefix" => "fund"], function () use ($router) {
            $router->get("show", "FundController@show");
            $router->put("update", "FundController@update");
        });

        $router->group(["prefix" => "transaction"], function () use ($router) {
            $router->get("index", "TransactionController@index");
            $router->post("store", "TransactionController@store");
            $router->put("update", "TransactionController@update");
            $router->delete("destroy", "TransactionController@destroy");
        });

        $router->get("test/etag", function (Request $request) {
            $transactions = $request->user()
                ->allTransactions()->orderBy("created_at", "desc")
                ->simplePaginate(20);

            $hashedTX = \Illuminate\Support\Facades\Hash::make(json_encode($transactions["data"]));

            if (\Illuminate\Support\Facades\Hash::check(json_encode($transactions["data"]), $request->header("If-None-Match"))) {
                return response("Not Modified", 304)->header("ETag", $request->header("If-None-Match"));
            }

            return response([
                "transactions" => $transactions,
            ])->header("ETag", $hashedTX);
        });
    });



    $router->get("test", function (Request $request) {
        // return dd($request->header(), $request->user());
        // return Carbon::today()->subDays(rand(0, 365))->toDateString();

        // $now = Carbon::now()->toDateTimeString();
        // $nowHashed = \Illuminate\Support\Facades\Hash::make($now);
        // $isHashMatch = \Illuminate\Support\Facades\Hash::check($now, $nowHashed);
        // return response(["isHashMatch" => $isHashMatch]);

        $transactions1 = \App\Models\Transaction::where("user_id", 1)
            /* ->offset(999999)*/->limit(20)->get();
        $transactions2 = \App\Models\Transaction::where("user_id", 1)
            ->offset(0)->limit(20)->get();
        $transactionsHashed1   = \Illuminate\Support\Facades\Hash::make($transactions1);
        $transactionsHashed2   = \Illuminate\Support\Facades\Hash::make($transactions2);
        $isHashMatch1 =   \Illuminate\Support\Facades\Hash::check($transactions1, $transactionsHashed1);
        $isHashMatch2 =  \Illuminate\Support\Facades\Hash::check($transactions2, $transactionsHashed1);
        return response()->json([
            "transactionsHashed1" => $transactionsHashed1,  "transactionsHashed2" => $transactionsHashed2,
            "isHashMatch1" => $isHashMatch1, "isHashMatch2" => $isHashMatch2,
            // "transactions1" => $transactions1
        ]);
    });
});
