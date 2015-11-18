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

		// Para crear una cuenta, cambiar estos valores
		$usuario = User::create(array(
			'username' => 'watchIMT',
			'password' => Hash::make('SkGdYKtN4mycKGrq'),
			));
	}
}
