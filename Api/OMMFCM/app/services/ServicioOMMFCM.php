<? php

	namespace services;

	public class ServicioOMMFCM implements ServicioOMMFCMInterface{
		// TODO eliminar esto cuando tegamos paginacion
		public function getIncidentes()
		{
			$incidentes = Incidente::all();
			
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
	    	$incidenteExistente = Incidente::find($incidente -> id);

			if ($incidente -> idEspecie)
			{
				$incidenteExistente -> idEspecie = $incidente -> idEspecie;
			}

			$resultado = $incidenteExistente -> save();

			return $resultado;
	    }
	}