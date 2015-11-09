<?php
	class Especie extends Eloquent{

		protected $fillable = array('nombreComun', 'nombreCientifico', 'idEstadoEspecie', 'idEstadoEspecie2');
		protected $primaryKey = 'idEspecie';

		public static $reglasCrearEspecie = array(
            'nombreComun' 			=> 'Required|alpha_spaces',
            'nombreCientifico'     	=> 'Required|alpha_spaces',
            'idEstadoEspecie'  		=> 'Required|Integer',
            'idEstadoEspecie2'		=> 'Required|Integer'
        );

        public static $reglasModificarEspecie = array(
        	'idEspecie'				=> 'Required|Integer',
            'nombreComun' 			=> 'Required|alpha_spaces',
            'nombreCientifico'     	=> 'Required|alpha_spaces',
            'idEstadoEspecie'  		=> 'Required|Integer',
            'idEstadoEspecie2'		=> 'Required|Integer'
        );

        public static $reglasBorrarEspecie = array(
        	'idEspecie'				=> 'Required|Integer'
        );

		public function incidentes()
		{
    		return $this -> hasMany('Incidente', 'idEspecie', 'idEspecie');
    	}

		public function estadoEspecie()
		{
			return $this -> belongsTo('estadoEspecie');
		}

		public function estadoEspecie2()
		{
			return $this -> belongsTo('estadoEspecie2');
		}
	}