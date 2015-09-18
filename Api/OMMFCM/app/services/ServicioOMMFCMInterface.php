<?php
	namespace services;

	interface ServicioOMMFCMInterface {  
	    public function getIncidentes(); 
	    public function crearIncidente($incidente, $imagen64);
	    public function eliminarIncidente($id);
	    public function modificarIncidente($incidente);
	}
