<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

// Route group for API versioning
Route::group(array('prefix' => 'api/'), function()
{
    Route::resource('incidentes', 'IncidentesController');
    Route::resource('especies', 'EspeciesController');
    Route::resource('estados', 'EstadosController');
    Route::resource('municipios', 'MunicipiosController');
    Route::resource('estadosEspecies', 'EstadosEspeciesController');
    Route::resource('estadosEspecies2', 'EstadosEspecies2Controller');

	Route::get('incidentes', ['before' => 'auth.basic', 'uses' => 'IncidentesController@index']);
	Route::put('incidentes/{incidentes}', ['before' => 'auth.basic', 'uses' => 'IncidentesController@update']);
	Route::delete('incidentes/{incidentes}', ['before' => 'auth.basic', 'uses' => 'IncidentesController@destroy']);

	Route::get('especies', ['before' => 'auth.basic', 'uses' => 'EspeciesController@index']);
	Route::post('especies', ['before' => 'auth.basic', 'uses' => 'EspeciesController@store']);
	Route::put('especies/{especies}', ['before' => 'auth.basic', 'uses' => 'EspeciesController@update']);
	Route::delete('especies/{especies}', ['before' => 'auth.basic', 'uses' => 'EspeciesController@destroy']);

	Route::get('estadosEspecies', ['before' => 'auth.basic', 'uses' => 'EstadosEspeciesController@index']);
	Route::get('estadosEspecies2', ['before' => 'auth.basic', 'uses' => 'EstadosEspecies2Controller@index']);
});