<?php
	class Especie extends Eloquent{

		protected $fillable = array('nombreComun', 'nombreCientifico', 'idEstadoEspecie', 'idEstadoEspecie2');
		protected $primaryKey = 'idEspecie';

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