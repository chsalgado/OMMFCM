<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNullAttributes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE `incidentes` MODIFY `idEspecie` BIGINT NULL;');
		DB::statement('ALTER TABLE `incidentes` MODIFY `fecha` TIMESTAMP NULL;');
		DB::statement('ALTER TABLE `incidentes` MODIFY `long` DECIMAL(11,8) NULL;');
		DB::statement('ALTER TABLE `incidentes` MODIFY `lat` DECIMAL(10,8) NULL;');
		DB::statement('ALTER TABLE `incidentes` MODIFY `mpioOrigen` INT(11) NULL;');
		DB::statement('ALTER TABLE `incidentes` MODIFY `mpioDestino` INT(11) NULL;');
		DB::statement('ALTER TABLE `incidentes` MODIFY `km` DECIMAL(8,2) NULL;');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE `incidentes` MODIFY `idEspecie` BIGINT NOT NULL;');
		DB::statement('ALTER TABLE `incidentes` MODIFY `fecha` NOT NULL;');
		DB::statement('ALTER TABLE `incidentes` MODIFY `long` NOT NULL;');
		DB::statement('ALTER TABLE `incidentes` MODIFY `lat` NOT NULL;');
		DB::statement('ALTER TABLE `incidentes` MODIFY `mpioOrigen` INT(11) NOT NULL;');
		DB::statement('ALTER TABLE `incidentes` MODIFY `mpioDestino` INT(11) NOT NULL;');
		DB::statement('ALTER TABLE `incidentes` MODIFY `km` NOT NULL;');
	}

}
