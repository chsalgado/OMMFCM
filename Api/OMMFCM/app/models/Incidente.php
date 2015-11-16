<?php
	class Incidente extends Eloquent{

		protected $fillable = array('fecha', 'rutaFoto', 'rutaThumbnail', 'long', 'lat', 'km', 'idEspecie', 'ruta');
		protected $primaryKey = 'idIncidente';

		public static $reglasCrearIncidente = array(
            'fecha' 		=> 'Required|Date',
            'long'     		=> 'Required|Numeric',
            'lat'  			=> 'Required|Numeric',
            'rutaFoto'		=> 'Required|URL',
            'rutaThumbnail'	=> 'Required|URL',
            'idEspecie'		=> 'Required|Numeric'
        );

        public static $reglasModificarIncidente = array(
            'idEspecie'	=> 'Required|Numeric',
            'km'		=> 'Numeric',
            'ruta'		=> 'alpha_dash'	
        );

        public static $reglasBorrarIncidente = array(
        	'idIncidente'	=> 'Required|Integer'
        );

		public function especie(){
			return $this -> belongsTo('Especie');
		}
	}