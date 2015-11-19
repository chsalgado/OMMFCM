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

// Grupo de rutas para la API, necesita estar authenticado
Route::group(array('prefix' => 'api/', 'before' => 'auth.basic'), function()
{
    Route::resource('incidentes', 'IncidentesController');
    Route::resource('especies', 'EspeciesController');
    Route::resource('estadosEspecies', 'EstadosEspeciesController');
    Route::resource('estadosEspecies2', 'EstadosEspecies2Controller');
});

// Grupo de rutas para apps y sitio público
Route::group(array('prefix' => 'api/'), function()
{
	Route::post('incidentes', ['uses' => 'IncidentesController@store']);
    Route::resource('estados', 'EstadosController');
    Route::resource('municipios', 'MunicipiosController');
});
