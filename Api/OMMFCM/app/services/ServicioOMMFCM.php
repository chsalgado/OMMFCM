<?php
	namespace services;
	use Incidente;
	use Request;
	use Image;
	use File;
	use \Eventviva\ImageResize;
	use Especie;
	
	class ServicioOMMFCM implements ServicioOMMFCMInterface{
		
		public function getIncidentes($pagina, $resultados)
		{
	   		Incidente::resolveConnection()->getPaginator()->setCurrentPage($pagina);
	   		$incidentes = Incidente::paginate($resultados);

	   		return $incidentes;
	   	}

	   	public function getIncidentesPorEspecie($idEspecie, $pagina, $resultados)
		{
	   		Incidente::resolveConnection()->getPaginator()->setCurrentPage($pagina);
	   		$incidentes = Incidente::where('idEspecie', '=', $idEspecie)->paginate($resultados);

	   		return $incidentes;
	   	}

	    public function crearIncidente($incidente, $imagen64, $extensionImg)
	    {
	    	if(is_null($imagen64) || is_null($extensionImg))
	    	{
	    		return 400;
	    	}

	    	if(is_null($incidente -> idEspecie))
	    	{
	    		$incidente -> idEspecie = 0;
	    	}

	    	if(is_null($incidente -> mpioOrigen))
	    	{
	    		$incidente -> mpioOrigen = 0;
	    	}

	    	if(is_null($incidente -> mpioDestino))
	    	{
	    		$incidente -> mpioDestino = 0;
	    	}

	    	$thumbnailAncho = 200;
	    	$thumbnailAlto = 200;
			$imagen = base64_decode($imagen64);
			$imagenThumbnail = ImageResize::createFromString(base64_decode($imagen64));
			$imagenThumbnail -> resize($thumbnailAncho, $thumbnailAlto);
			$ruta = public_path() . "/imagenes/incidentes/";
			$nombreImagen 	= "incidente_" . time();
			$rutaThumbnail = $ruta . $nombreImagen . "_thumbnail" . $extensionImg;
			$nombreImagen  = $nombreImagen . $extensionImg;

			$resultado = File::put($ruta . $nombreImagen, $imagen);

			if(!$resultado)
			{
				return 500;
			}

			$resultado = $imagenThumbnail -> save($rutaThumbnail);

			if(!$resultado)
			{
				return 500;
			}
 
			$nuevoIncidente = new Incidente;
			$nuevoIncidente = $incidente;
			$nuevoIncidente -> rutaFoto = $ruta . $nombreImagen;
			$nuevoIncidente -> rutaThumbnail = $rutaThumbnail;

			$resultado = $nuevoIncidente -> save(); 

	    	if($resultado)
	    	{
				return 200;	    		
	    	}

	    	return 500;	    
	    }

	    public function eliminarIncidente($id)
	    {
	    	$incidente = Incidente::find($id);
	    	$rutaImagen = $incidente -> rutaFoto;
	    	$rutaThumbnail = $incidente -> rutaThumbnail;
	    	
	    	$resultado = File::delete($rutaImagen, $rutaThumbnail);

	    	if(!$resultado)
	    	{
	    		return 500;
	    	}

	    	$resultado = $incidente -> delete();

	    	if($resultado)
	    	{
				return 200;	    		
	    	}

	    	return 500;	    
	    }

	    public function modificarIncidente($incidente)
	    {
	    	$incidenteExistente = Incidente::find($incidente -> idIncidente);

			if(!$incidente -> idEspecie)
			{
				return 400;	
			}

			$incidenteExistente -> idEspecie = $incidente -> idEspecie;
			$resultado = $incidenteExistente -> save();

			if($resultado)
			{
				return 200;
			}
			return 500;
	    }

	    public function getEspecies()
	    {
	   		$pagina = Request::get('pagina');
	   		$resultados = Request::get('resultados');
	   		$especies = Especie::all();

	   		// Validar que los dos parámetros de paginación fueron enviados, de lo contrario mandar todas las especies
	   		if(!is_null($pagina) && !is_null($resultados))
	   		{
				Especie::resolveConnection()->getPaginator()->setCurrentPage($pagina);
		   		$especies = Especie::paginate($resultados);	   			
	   		}

	   		return $especies;	    	
	    }

	    public function crearEspecie($especie)
	    {
			$nuevaEspecie = new Especie;
			$nuevaEspecie = $especie;
			$resultado = $nuevaEspecie -> save(); 

	    	if($resultado)
	    	{
				return 200;	    		
	    	}

	    	return 500;
	    }

	    public function modificarEspecie($especie)
	    {
	    	$especieExistente = Especie::find($especie -> idEspecie);
	    	
	    	if(is_null($especieExistente))
	    	{
	    		return 404;
	    	}

	    	$especieExistente -> nombreComun = $especie -> nombreComun;
	    	$especieExistente -> nombreCientifico = $especie -> nombreCientifico;

	    	$resultado = $especieExistente -> save();

	    	if($resultado)
	    	{
				return 200;	    		
	    	}

	    	return 500;
	    }

	    public function eliminarEspecie($id)
	    {
	    	$especie = Especie::find($id);
	    	$resultado = $especie -> delete();

	    	if($resultado)
	    	{
				return 200;	    		
	    	}

	    	return 500;
	    }
	}