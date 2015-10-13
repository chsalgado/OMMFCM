<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaEstadoEspecie extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Crear tabla de estados de especies
		Schema::create('estadosEspecies', function(Blueprint $table)
    	{
	        $table->bigIncrements('idEstadoEspecie');
	        $table->string('estado');
	        $table->timestamps();
    	});

		// Agregar atributo de estado a las especies
		Schema::table('especies', function($table)
		{
		    $table->bigInteger('idEstadoEspecie');		    
		});

		// Agregar el tipo unsigned a la columna  idEstadoEspecie en la tabla especies para poder crear luego la llave foranea (no se puede crear una FK con tipos diferentes)		
		DB::statement('ALTER TABLE `especies` MODIFY `idEstadoEspecie` BIGINT UNSIGNED NULL;');

		// Agregar llave foranea. 
		DB::statement('ALTER TABLE especies ADD FOREIGN KEY (idEstadoEspecie) REFERENCES estadosEspecies(idEstadoEspecie)');		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Eliminar llave foranea 
		DB::statement('ALTER TABLE `especies` DROP FOREIGN KEY fk_idEstadoEspecie');		

		// Eliminar columna de estado en especies
		Schema::table('especies', function($table)
		{
		    $table->dropColumn('idEstadoEspecie');		    
		});

		// Eliminar tabla de estados de especies
		Schema::drop('estadosEspecies');
	}

}
