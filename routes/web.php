<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use Carbon\Carbon;
use Illuminate\Http\Request;

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
    });



    $router->get("test", function (Request $request) {
        // return dd($request->header(), $request->user());
        return Carbon::today()->subDays(rand(0, 365))->toDateString();
    });
});
