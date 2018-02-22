<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([/*'middleware' => 'auth.basic'*/], function () {
    Route::get('/ping', function(Request $request) {
            return response()
                ->json('pong')
                ->setStatusCode(200)
                ->header('Content-Type', 'application/json');
    });

    // ACTOR's APIs
    Route::get('/actors/{id}', 'ActorController@show')
        ->where(
            'id',
            '[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}'
        );
    Route::post('/actors', 'ActorController@store');
    Route::put(
        '/actors/{id}',
        function(Request $request, $id)
        {
            $app = app();
            $controller = $app->make('\App\Http\Controllers\ActorController');
            return $controller->callAction('update', [$request, $id]); 
        }
    )->where(
        'id',
        '[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}'
    );
    Route::delete(
        '/actors/{id}',
        'ActorController@remove'
    )->where(
        'id',
        '[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}'
    );
});
