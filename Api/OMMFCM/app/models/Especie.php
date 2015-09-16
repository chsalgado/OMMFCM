<? php
	class Especie extends Eloquent{

		protected fillable = array('nombreComun', 'nombreCientifico');

		public function incidentes(){
    		return $this -> hasMany('Incidente');
    	}
	}