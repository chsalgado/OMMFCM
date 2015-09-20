<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeys extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Para añadir las llaves foráneas es necesario que las columnas a asociar tengan el mismo tipo de datos
		DB::statement('ALTER TABLE `incidentes` MODIFY `idEspecie` BIGINT UNSIGNED NOT NULL;');
		DB::statement('ALTER TABLE `incidentes` MODIFY `mpioOrigen` INT(11) UNSIGNED NOT NULL;');
		DB::statement('ALTER TABLE `incidentes` MODIFY `mpioDestino` INT(11) UNSIGNED NOT NULL;');
		DB::statement('ALTER TABLE `municipios` MODIFY `id_municipio` INT(11) UNSIGNED NOT NULL;');
	 
		DB::statement('ALTER TABLE incidentes ADD FOREIGN KEY (idEspecie) REFERENCES especies(idEspecie)');
		DB::statement('ALTER TABLE incidentes ADD FOREIGN KEY (mpioOrigen) REFERENCES municipios(id_municipio)');
		DB::statement('ALTER TABLE incidentes ADD FOREIGN KEY (mpioDestino) REFERENCES municipios(id_municipio)');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE `incidentes` DROP FOREIGN KEY fk_idEspecie');
		DB::statement('ALTER TABLE `incidentes` DROP FOREIGN KEY fk_mpioOrigen');
		DB::statement('ALTER TABLE `incidentes` DROP FOREIGN KEY fk_mpioDestino');
	}

}
