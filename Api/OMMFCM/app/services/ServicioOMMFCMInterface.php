<?php
	namespace services;

	interface ServicioOMMFCMInterface {  
	    public function getIncidentes();
	    public function getIncidentesPaginados($pagina, $resultados);
	    public function getIncidentesPorEspecie($idEspecie, $pagina, $resultados); 
	    public function crearIncidente($incidente, $imagen64, $extensionImg);
	    public function eliminarIncidente($id);
	    public function modificarIncidente($incidente);
	    public function getEspecies($pagina, $resultados);
	    public function crearEspecie($especie);
	    public function eliminarEspecie($id);
	    public function modificarEspecie($especie);
	    public function getEstados();
	    public function getMunicipiosPorEstado($idEstado);
	    public function getEstadosEspecies();
	    public function getEstadosEspecies2();
	}
