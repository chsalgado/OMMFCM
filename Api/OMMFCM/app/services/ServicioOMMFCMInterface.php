<?php
	namespace services;

	interface ServicioOMMFCMInterface {  
	    public function getIncidentesPorReporte($reporte);
	    public function getIncidentesPaginados($pagina, $resultados);
	    public function getIncidentesPorEspecie($idEspecie, $pagina, $resultados); 
	    public function crearIncidente($incidente, $imagen64, $extensionImg, $publicPath);
	    public function eliminarIncidente($incidente);
	    public function modificarIncidente($incidente);
	    public function getEspecies($pagina, $resultados);
	    public function crearEspecie($especie);
	    public function eliminarEspecie($especie);
	    public function modificarEspecie($especie);
	    public function getEstados();
	    public function getMunicipiosPorEstado($idEstado);
	    public function getEstadosEspecies();
	    public function getEstadosEspecies2();
	}
