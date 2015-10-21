<?php
	class Incidente extends Eloquent{

		protected $fillable = array('fecha', 'rutaFoto', 'rutaThumbnail', 'long', 'lat', 'km', 'idEspecie', 'ruta');
		protected $primaryKey = 'idIncidente';

		public function especie(){
			return $this -> belongsTo('Especie');
		}
	}