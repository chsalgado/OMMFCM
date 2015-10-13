<?php
	class Especie extends Eloquent{

		protected $fillable = array('nombreComun', 'nombreCientifico');
		protected $primaryKey = 'idEspecie';

		public function incidentes()
		{
    		return $this -> hasMany('Incidente');
    	}

		public function estadoEspecie()
		{
			return $this -> belongsTo('estadoEspecie');
		}
	}