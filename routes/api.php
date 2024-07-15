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

Route::group([], function() {
    Route::post('getCuadrants', [
        'uses' => 'api\\apiCertificatesController@getCuadrants'
    ]);

    Route::post('getCertificates', [
        'uses' => 'api\\apiCertificatesController@getCertificates'
    ]);
});

Route::post('login', 'api\\AuthController@login');

Route::post('getUserToGlobalUser', [
    'uses' => 'api\\apiGlobalUsersController@getUserToGlobalUser'
]);

Route::post('getListUsers', [
    'uses' => 'api\\apiGlobalUsersController@getListUsersToGlobalUsers'
]);

Route::post('syncUser', [
    'uses' => 'api\\apiGlobalUsersController@syncUser'
]);

Route::get('syncJobsAndDepartments', [
    'uses' => 'api\\apiGlobalUsersController@syncJobsAndDepartments'
]);

Route::get('setupDeptsAndHeaders', [
    'uses' => 'api\\apiGlobalUsersController@setupDeptsAndHeaders'
]);

Route::get('getUserById/{id}', [
    'uses' => 'api\\apiGlobalUsersController@getUserById'
]);

Route::post('updateUser', [
    'uses' => 'api\\apiGlobalUsersController@updateUser'
]);
Route::group(['middleware' => 'auth:api'], function() {
});
