<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AgregarTablaEstadoEspecie2 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Crear tabla de estados de especies
		Schema::create('estadosEspecies2', function(Blueprint $table)
    	{
			$table->tinyInteger('idEstadoEspecie2')->unsigned()->autoIncrement();
	        $table->string('estado');
	        $table->timestamps();
    	});

		// Agregar atributo de estado a las especies
		Schema::table('especies', function($table)
		{
		    $table->tinyInteger('idEstadoEspecie2');		    
		});

		// Agregar el tipo unsigned a la columna  idEstadoEspecie en la tabla especies para poder crear luego la llave foranea (no se puede crear una FK con tipos diferentes)		
		DB::statement('ALTER TABLE `especies` MODIFY `idEstadoEspecie2` TINYINT UNSIGNED NULL;');

		// Agregar llave foranea. 
		DB::statement('ALTER TABLE especies ADD FOREIGN KEY (idEstadoEspecie2) REFERENCES estadosEspecies2(idEstadoEspecie2)');		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Eliminar llave foranea 
		DB::statement('ALTER TABLE `especies` DROP FOREIGN KEY fk_idEstadoEspecie2');		

		// Eliminar columna de estado en especies
		Schema::table('especies', function($table)
		{
		    $table->dropColumn('idEstadoEspecie2');		    
		});

		// Eliminar tabla de estados de especies
		Schema::drop('estadosEspecies2');	
	}

}
