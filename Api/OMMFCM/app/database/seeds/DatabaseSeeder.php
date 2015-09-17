<?php

class DatabaseSeeder extends Seeder 
{

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		//Borrar información contenida en las tablas
		DB::table('especies')->delete();
		DB::table('incidentes')->delete();
		DB::table('municipios')->delete();
		DB::table('estados')->delete();
		DB::table('usuarios')->delete();

		//Poblar las tablas usuarios/especies/incidentes con datos dummy
		$usuario = Usuario::create(array(
			'username' => 'administrador',
			'password' => 'administrador',
			));

		$num_especies = 10;
		$num_incidentes = 100;
		for($i = 0; $i< $num_especies; $i++)
		{
			$nombreComun = 'nombre comun'.$i;
			$nombreCientifico = 'nombre cientifico'.$i;
			$especie = Especie::create(array(
			  'nombreComun' => $nombreComun,
			  'nombreCientifico' => $nombreCientifico,
			));
		}

		for($i = 0; $i< $num_incidentes; $i++)
		{
			$idEspecie = rand(1,9);
			$fecha = rand(2010,2015).'-0'.rand(1,9).'-'.rand(10,30).' '.rand(10,24).':00:00';
			$rutaFoto = 'fotos/foto_'.$i.'.jpg';
			$long = '100'.rand(10000000,14444444);
			$lat = rand(20,22).rand(10000000,99999999);
			$mpioOrigen = rand(1,2577);
			$mpioDestino = rand(1,2577);
			$km = rand(1,200);
			$incidente = Incidente::create(array(
			  'idEspecie' => $idEspecie,
			  'fecha' => $fecha,
			  'rutaFoto' => $rutaFoto,
			  'long'	=>	$long,
			  'lat'	=> $lat,
			  'mpioOrigen'	=> $mpioOrigen,
			  'mpioDestino' => $mpioDestino,
			  'km'	=>	$km

			));
		}
	}
}
