<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearEntidades extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('usuarios', function(Blueprint $table)
    	{
	        $table->increments('id');
	        $table->string('username')->unique();
	        $table->string('password');
	        $table->timestamps();
    	});

    	  	Schema::create('estados', function(Blueprint $table)
    	{
	        $table->integer('id_estado') -> unique();
	        $table->string('estado');
    	});

    	Schema::create('municipios', function(Blueprint $table)
    	{
	        $table->integer('id_municipio') -> unique();
	        $table->string('nombre_municipio');
	        $table->integer('estado');
    	});

    	Schema::create('especies', function(Blueprint $table)
    	{
	        $table->bigIncrements('idEspecie');
	        $table->string('nombreComun');
	        $table->string('nombreCientifico');
	        $table->timestamps();
    	});
	
    	Schema::create('incidentes', function(Blueprint $table)
    	{
	        $table->bigIncrements('idIncidente');
	        $table->bigInteger('idEspecie') -> references('id') -> on('especies');
	        $table->timestamp('fecha');
	        $table->string('rutaFoto');
	        $table->decimal('long', 11, 8);
	        $table->decimal('lat', 10, 8);
	        $table->integer('mpioOrigen') -> references('id_municipio') -> on('municipios');
	        $table->integer('mpioDestino') -> references('id_municipio') -> on('municipios');;
	        $table->decimal('km');
	        $table->timestamps();
    	});
	}
    	

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('usuarios');
		Schema::drop('especies');
		Schema::drop('incidentes');
		Schema::drop('estados');
		Schema::drop('municipios');
	}

}
