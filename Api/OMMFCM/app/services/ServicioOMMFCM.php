<?php
	namespace services;
	use Incidente;
	use Image;
	use File;
	use \Eventviva\ImageResize;
	use Especie;
	use Estado;
	use EstadoEspecie;
	
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

	    	if(file_exists($rutaImagen))
	    	{
		    	$resultado = File::delete($rutaImagen);    		

		    	if(!$resultado)
		    	{
		    		return 500;
		    	}
	    	}

	    	if(file_exists($rutaThumbnail))
	    	{
		    	$resultado = File::delete($rutaThumbnail);    			    		

		    	if(!$resultado)
		    	{
		    		return 500;
		    	}
	    	}

	    	$resultado = $incidente -> delete();

	    	if($resultado)
	    	{
				return 204;	    		
	    	}

	    	return 500;	    
	    }

	    public function modificarIncidente($incidente)
	    {
	    	$incidenteExistente = Incidente::find($incidente -> idIncidente);

			if(!$incidente -> idEspecie || !$incidente -> km || !$incidente -> ruta)
			{
				return 400;	
			}

			$incidenteExistente -> idEspecie = $incidente -> idEspecie;
			$incidenteExistente -> ruta = $incidente -> ruta;
			$incidenteExistente -> km = $incidente -> km;
			
			$resultado = $incidenteExistente -> save();

			if($resultado)
			{
				return 200;
			}
			return 500;
	    }

	    public function getEspecies($pagina, $resultados)
	    {
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

	    public function getEstados()
		{
	   		$estados = Estado::all();

	   		return $estados;
	   	}

	    public function getMunicipiosPorEstado($idEstado)
	   	{
	   		$municipios = Estado::find($idEstado) -> municipios;

	   		return $municipios;
	   	}

	   	public function getEstadosEspecies()
	   	{
	   		$estadosEspecies = EstadoEspecie::all();

	   		return $estadosEspecies;
	   	}
	}