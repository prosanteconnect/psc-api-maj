<?php

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

use Laravel\Lumen\Routing\Router;

/** @var Router $router */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->get('ps',  ['uses' => 'PsController@index']);
    $router->post('ps',  ['uses' => 'PsController@store']);
    $router->put('ps', ['uses' => 'PsController@storeOrUpdate']);
    $router->put('ps/replace', ['uses' => 'PsController@storeOrReplace']);
    $router->get('ps/{psId}', ['uses' => 'PsController@show']);
    $router->put('ps/{psId}', ['uses' => 'PsController@update']);
    $router->delete('ps/{psId}', ['uses' => 'PsController@destroy']);
    $router->delete('ps/force/{psId}', ['uses' => 'PsController@forceDestroy']);

    $router->post('psref', ['uses' => 'PsRefController@store']);
    $router->get('psref/{psRefId}', ['uses' => 'PsRefController@show']);
    $router->put('psref/{psRefId}', ['uses' => 'PsRefController@update']);
    $router->delete('psref/{psRefId}', ['uses' => 'PsRefController@destroy']);

    $router->get('ps/{psId}/professions', ['uses' => 'ProfessionController@index']);
    $router->post('ps/{psId}/professions', ['uses' => 'ProfessionController@store']);
    $router->get('ps/{psId}/professions/{exProId}', ['uses' => 'ProfessionController@show']);
    $router->put('ps/{psId}/professions/{exProId}', ['uses' => 'ProfessionController@update']);
    $router->delete('ps/{psId}/professions/{exProId}', ['uses' => 'ProfessionController@destroy']);

    $router->get('ps/{psId}/professions/{exProId}/expertises', ['uses' => 'ExpertiseController@index']);
    $router->post('ps/{psId}/professions/{exProId}/expertises', ['uses' => 'ExpertiseController@store']);
    $router->get('ps/{psId}/professions/{exProId}/expertises/{expertiseId}', ['uses' => 'ExpertiseController@show']);
    $router->put('ps/{psId}/professions/{exProId}/expertises/{expertiseId}', ['uses' => 'ExpertiseController@update']);
    $router->delete("ps/{psId}/professions/{exProId}/expertises/{expertiseId}", ['uses' => 'ExpertiseController@destroy']);

    $router->get('ps/{psId}/professions/{exProId}/situations', ['uses' => 'WorkSituationController@index']);
    $router->post('ps/{psId}/professions/{exProId}/situations', ['uses' => 'WorkSituationController@store']);
    $router->get('ps/{psId}/professions/{exProId}/situations/{situId}', ['uses' => 'WorkSituationController@show']);
    $router->put('ps/{psId}/professions/{exProId}/situations/{situId}', ['uses' => 'WorkSituationController@update']);
    $router->delete('ps/{psId}/professions/{exProId}/situations/{situId}', ['uses' => 'WorkSituationController@destroy']);

    $router->get('structures',  ['uses' => 'StructureController@index']);
    $router->post('structures',  ['uses' => 'StructureController@store']);
    $router->put('structures',  ['uses' => 'StructureController@storeOrUpdate']);
    $router->get('structures/{structureId}', ['uses' => 'StructureController@show']);
    $router->put('structures/{structureId}', ['uses' => 'StructureController@update']);
    $router->delete('structures/{structureId}', ['uses' => 'StructureController@destroy']);
});
