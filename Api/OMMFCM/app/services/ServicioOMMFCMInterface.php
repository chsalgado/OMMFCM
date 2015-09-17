<?php
	namespace services;

	interface ServicioOMMFCMInterface {  
	    public function getIncidentes(); // TODO eliminar esto cuando tegamos paginacion
	    public function crearIncidente($incidente);
	    public function eliminarIncidente($id);
	    public function modificarIncidente($incidente);
	}
