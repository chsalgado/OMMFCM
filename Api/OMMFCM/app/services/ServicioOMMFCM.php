<?php
	namespace services;
	use Incidente;
	use Request;
	use Image;
	use File;
	use \Eventviva\ImageResize;
	
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
	    	if(is_null($imagen64))
	    	{
	    		return false; // TODO definir la respuesta del servidor ante campos vacíos y agregar validación
	    	}

	    	$thumbnailWidth = 200;
	    	$thumbnailHeight = 200;
			$imagen = base64_decode($imagen64);
			$imageThumbnail = ImageResize::createFromString(base64_decode($imagen64));
			$imageThumbnail -> resize($thumbnailWidth, $thumbnailHeight);
			$ruta = public_path() . "/imagenes/incidentes/";
			$nombreImagen 	= "incidente_" . time();
			$rutaThumbnail = $ruta . $nombreImagen . "_thumbnail" . $extensionImg;
			$nombreImagen  = $nombreImagen . $extensionImg;
			$resultado = File::put($ruta . $nombreImagen, $imagen);

			if(!$resultado)
			{
				return $resultado;
			}

			$resultado = $imageThumbnail -> save($rutaThumbnail);

			if(!$resultado)
			{
				return $resultado;
			}
 
			$nuevoIncidente = new Incidente;
			$nuevoIncidente = $incidente;
			$nuevoIncidente -> rutaFoto = $ruta . $nombreImagen;
			$nuevoIncidente -> rutaThumbnail = $rutaThumbnail;
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