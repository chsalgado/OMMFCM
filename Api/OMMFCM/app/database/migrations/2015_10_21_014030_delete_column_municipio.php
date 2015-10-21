<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteColumnMunicipio extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE `incidentes` DROP FOREIGN KEY incidentes_ibfk_2');
		DB::statement('ALTER TABLE `incidentes` DROP FOREIGN KEY incidentes_ibfk_3');
		Schema::table('incidentes', function($table)
		{
		    $table->dropColumn('mpioOrigen'); 
		    $table->dropColumn('mpioDestino');    
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('incidentes', function($table)
		{
		    $table->integer('mpioOrigen'); 
		    $table->integer('mpioDestino');    
		});
	}

}
