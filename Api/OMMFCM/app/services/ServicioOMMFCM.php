<?php
	namespace services;
	use Incidente;
	use Request;
	use Image;
	use File;

	class ServicioOMMFCM implements ServicioOMMFCMInterface{
		
		public function getIncidentes()
		{
	   		$pagina = Request::get('pagina');
	   		$resultados = Request::get('resultados');
			Incidente::resolveConnection()->getPaginator()->setCurrentPage($pagina);
	   		$incidentes = Incidente::paginate($resultados);

	   		return $incidentes;
	   	}

	    public function crearIncidente($incidente, $imagen64, $extensionImg)
	    {
			$imagen = base64_decode($imagen64);
			$ruta_imagen 	= public_path() . "/imagenes/incidentes/" . "incidente_" . time() . $extensionImg;
			File::put($ruta_imagen, $imagen);

			$nuevoIncidente = new Incidente;
			$nuevoIncidente = $incidente;
			$nuevoIncidente -> rutaFoto = $ruta_imagen;
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