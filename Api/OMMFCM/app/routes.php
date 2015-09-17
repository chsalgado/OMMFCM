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
//App::bind('app\services\ServicioOMMFCMInterface', 'app\services\ServicioOMMFCM');  

Route::get('/', function()
{
	return View::make('hello');
});

// Route group for API versioning
Route::group(array('prefix' => 'api/'), function()
{
    Route::resource('incidentes', 'IncidentesController');
});

