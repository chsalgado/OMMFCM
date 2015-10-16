<?php
	class Incidente extends Eloquent{

		protected $fillable = array('fecha', 'rutaFoto', 'rutaThumbnail', 'long', 'lat', 'mpioOrigen', 'mpioDestino', 'km', 'idEspecie', 'ruta');
		protected $primaryKey = 'idIncidente';

		public function especie(){
			return $this -> belongsTo('Especie');
		}

		public function mpioOrigen()
		{
			return $this -> hasOne('Municipio', 'id_municipio', 'mpioOrigen');
		}

		public function mpioDestino()
		{
			return $this -> hasOne('Municipio', 'id_municipio', 'mpioDestino');
		}
	}