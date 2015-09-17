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

Route::get('/nums', function()
{
	$numeros = array();
	$aux = 0;
	for($i = 1; $i<=10000; $i++){
		echo $i.' ';
	}
	//return View::make('hello');
});

// Route group for API versioning
Route::group(array('prefix' => 'api/'), function()
{
    Route::resource('incidentes', 'IncidentesController');
});

