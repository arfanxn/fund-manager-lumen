<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

        $router->get("test", function (Request $request) {
            return dd($request->header(), $request->user());
        });
        // 
    });
});
