<?php
	class Especie extends Eloquent{

		protected $fillable = array('nombreComun', 'nombreCientifico');
		protected $primaryKey = 'idEspecie';

		public function incidentes()
		{
    		return $this -> hasMany('Incidente', 'idEspecie', 'idEspecie');
    	}

		public function estadoEspecie()
		{
			return $this -> belongsTo('estadoEspecie');
		}
	}