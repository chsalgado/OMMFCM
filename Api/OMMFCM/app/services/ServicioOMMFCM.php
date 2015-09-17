<?php
	namespace services;
	use Incidente;
	use Request;

	class ServicioOMMFCM implements ServicioOMMFCMInterface{
		// TODO eliminar esto cuando tegamos paginacion
		public function getIncidentes()
		{
	   		$pagina = Request::get('pagina');
	   		$resultados = Request::get('resultados');
			Incidente::resolveConnection()->getPaginator()->setCurrentPage($pagina);
	   		$incidentes = Incidente::paginate($resultados);

	   		return $incidentes;
	   	}

	    public function crearIncidente($incidente)
	    {
			$nuevoIncidente = new Incidente;
			$nuevoIncidente = $incidente;
			$resultado = $nuevoIncidente -> save(); 

			return $resultado;
	    }

	    public function eliminarIncidente($id)
	    {
	    	$incidente = Incidente::find($id);
	    	$resultado = $incidente -> delete();

			return $resultado;
	    }

	    public function modificarIncidente($incidente)
	    {
	    	$incidenteExistente = Incidente::find($incidente -> idIncidente);

			if ($incidente -> idEspecie)
			{
				$incidenteExistente -> idEspecie = $incidente -> idEspecie;
			}

			$resultado = $incidenteExistente -> save();

			return $resultado;
	    }
	}