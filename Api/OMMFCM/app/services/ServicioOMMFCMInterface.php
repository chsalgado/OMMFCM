<?php
	namespace services;

	interface ServicioOMMFCMInterface {  
	    public function getIncidentes(); 
	    public function crearIncidente($incidente, $imagen64, $extensionImg);
	    public function eliminarIncidente($id);
	    public function modificarIncidente($incidente);
	    public function getEspecies();
	    public function crearEspecie($especie);
	    public function eliminarEspecie($id);
	    public function modificarEspecie($especie);
	}
