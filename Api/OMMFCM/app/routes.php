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
});