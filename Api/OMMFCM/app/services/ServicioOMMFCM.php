<?php
	namespace services;
	use Incidente;
	use Image;
	use File;
	use \Eventviva\ImageResize;
	use Especie;
	use Estado;
	use EstadoEspecie;
	use EstadoEspecie2;
	use DB;
	use Validator;

	class ServicioOMMFCM implements ServicioOMMFCMInterface{
		
		public function getIncidentesPorReporte($reporte)
		{
			$columnas;
			if($reporte == 1)
			{
				// columnas especiales para el excel
				$columnas = array('fecha', 'ruta', 'km', 'lat', 'long', 'nombreCientifico', 'nombreComun', 'estadosEspecies.estado', 'estadosEspecies2.estado AS estado2');
			}
			
			if($reporte == 2)
			{
				// columnas especiales para el mapa
				$columnas = array('fecha', 'lat', 'long', 'especies.idEspecie','nombreCientifico', 'nombreComun');
			}

			// obtener incidentes
			$incidentes = DB::table('incidentes')
						->join('especies', 'especies.idEspecie', '=', 'incidentes.idEspecie')
						->join('estadosEspecies', 'estadosEspecies.idEstadoEspecie', '=', 'especies.idEstadoEspecie')
						->join('estadosEspecies2', 'estadosEspecies2.idEstadoEspecie2', '=', 'especies.idEstadoEspecie2')
						->orderBy('fecha', 'asc')
						->get($columnas);				

			return $incidentes;
		}

		public function getIncidentesPaginados($pagina, $resultados)
		{
	   		Incidente::resolveConnection()->getPaginator()->setCurrentPage($pagina);
	   		$incidentes = Incidente::orderBy('fecha', 'desc')->paginate($resultados);

	   		return $incidentes;
	   	}

	   	public function getIncidentesPorEspecie($idEspecie, $pagina, $resultados)
		{
	   		Incidente::resolveConnection()->getPaginator()->setCurrentPage($pagina);
	   		$incidentes = Incidente::where('idEspecie', '=', $idEspecie)->orderBy('fecha', 'asc')->paginate($resultados);

	   		return $incidentes;
	   	}

	    public function crearIncidente($incidente, $imagen64, $extensionImg, $publicPath)
	    {
	    	if(is_null($imagen64) || is_null($extensionImg))
	    	{
	    		return 400;
	    	}

	    	if(strcasecmp($extensionImg, ".png") != 0 &&
	    	   strcasecmp($extensionImg, ".jpg") != 0 &&
	    	   strcasecmp($extensionImg, ".jpeg") != 0 &&
	    	   strcasecmp($extensionImg, ".gif") != 0)
	    	{
	    		return 403;
	    	}

	    	$thumbnailAncho = 200;
	    	$thumbnailAlto = 200;
			$ruta = $publicPath . "/imagenes/incidentes/";
			$nombreImagen 	= "incidente_" . time();
			$rutaThumbnail = $ruta . $nombreImagen . "_thumbnail" . $extensionImg;
			$nombreImagen  = $nombreImagen . $extensionImg;
			$rutaImagen = $ruta . $nombreImagen;

			// Le asignamos la idEspecie 0 para definir que no se le ha asignado ninguna especie
	    	$incidente -> idEspecie = 0;	    	
			$incidente -> rutaFoto = $rutaImagen;
			$incidente -> rutaThumbnail = $rutaThumbnail;
			$incidente -> ruta = null;
			$incidente -> km = null;

			$validator = Validator::make($incidente -> toArray(), Incidente::$reglasCrearIncidente);

			if ($validator->fails()) 
			{
				return 400;
			}

			$imagen = base64_decode($imagen64);

			try
			{
				imagecreatefromstring($imagen);
			}
			catch(\Exception $e)
			{
				return 403;
			}

			try 
			{
				File::put($rutaImagen, $imagen);
				$imagenThumbnail = new ImageResize($rutaImagen, $imagen);
				$imagenThumbnail -> resize($thumbnailAncho, $thumbnailAlto);
				$imagenThumbnail -> save($rutaThumbnail);
			}
			catch (\Exception $e) 
			{
				return 500;
			}

			$resultado = $incidente -> save(); 

	    	if($resultado)
	    	{
				return 201;	    		
	    	}

	    	return 500;	    
	    }

	    public function eliminarIncidente($incidente)
	    {
	    	$validator = Validator::make($incidente -> toArray(), Incidente::$reglasBorrarIncidente);

			if ($validator->fails()) 
			{
				return 400;
			}

	    	$incidenteExistente = $incidente ->find($incidente -> idIncidente);

	    	if(is_null($incidenteExistente))
	    	{
	    		return 404;
	    	}

	    	$rutaImagen = $incidenteExistente -> rutaFoto;
	    	$rutaThumbnail = $incidenteExistente -> rutaThumbnail;

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

	    	$resultado = $incidenteExistente -> delete();

	    	if($resultado)
	    	{
				return 204;	    		
	    	}

	    	return 500;	    
	    }

	    public function modificarIncidente($incidente)
	    {
	    	$validator = Validator::make($incidente -> toArray(), Incidente::$reglasModificarIncidente);

			if ($validator->fails()) 
			{
				return 400;
			}

	    	// El find ahora lo hace una instancia de incidente para poder hacer el mock
	    	$incidenteExistente = $incidente ->find($incidente -> idIncidente);

			if(is_null($incidenteExistente))
	    	{
	    		return 404;
	    	}

			$incidenteExistente -> idEspecie = $incidente -> idEspecie;
			$incidenteExistente -> km = $incidente -> km;
			$incidenteExistente -> ruta = $incidente -> ruta;
			
			$resultado = $incidenteExistente -> save();

			if($resultado)
			{
				return 200;
			}
			return 500;
	    }

	    public function getEspecies($pagina, $resultados)
	    {
	    	// Regresar todas las especies, excepto la que tiene id=0 (es la especie default)
	   		$especies = Especie::orderBy('nombreComun', 'asc')->get();

	   		// Validar que los dos parámetros de paginación fueron enviados, de lo contrario mandar todas las especies
	   		if(!is_null($pagina) && !is_null($resultados))
	   		{
				Especie::resolveConnection()->getPaginator()->setCurrentPage($pagina);
		   		$especies = Especie::where('idEspecie', '>', 0)->orderBy('nombreComun', 'asc')->paginate($resultados);	   			
	   		}

	   		return $especies;	    	
	    }

	    public function crearEspecie($especie)
	    {
			$validator = Validator::make($especie -> toArray(), Especie::$reglasCrearEspecie);

			if ($validator->fails()) 
			{
				return 400;
			}

			$resultado = $especie -> save(); 

	    	if($resultado)
	    	{
				return 201;	    		
	    	}

	    	return 500;
	    }

	    public function modificarEspecie($especie)
	    {
	    	$validator = Validator::make($especie -> toArray(), Especie::$reglasModificarEspecie);

			if ($validator->fails()) 
			{
				return 400;
			}

	    	// El find ahora lo hace una instancia de especie para poder hacer el mock
	    	$especieExistente = $especie ->find($especie -> idEspecie);
	    	
	    	if(is_null($especieExistente))
	    	{
	    		return 404;
	    	}

	    	$especieExistente -> nombreComun = $especie -> nombreComun;
	    	$especieExistente -> nombreCientifico = $especie -> nombreCientifico;
	    	$especieExistente -> idEstadoEspecie = $especie -> idEstadoEspecie;
	    	$especieExistente -> idEstadoEspecie2 = $especie -> idEstadoEspecie2;

	    	$resultado = $especieExistente -> save();

	    	if($resultado)
	    	{
				return 200;	    		
	    	}

	    	return 500;
	    }

	    public function eliminarEspecie($especie)
	    {
	    	$validator = Validator::make($especie -> toArray(), Especie::$reglasBorrarEspecie);

			if ($validator->fails()) 
			{
				return 400;
			}

	    	$especieExistente = $especie ->find($especie -> idEspecie);

	    	if(is_null($especieExistente))
	    	{
	    		return 404;
	    	}

	    	$incidentes = $especieExistente -> incidentes;

	    	if(count($incidentes))
	    	{
	    		return 412;
	    	}

	    	$resultado = $especieExistente -> delete();

	    	if($resultado)
	    	{
				return 204;	    		
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
	   		$estadosEspecies = EstadoEspecie::where('idEstadoEspecie', '>', '0')->get();

	   		return $estadosEspecies;
	   	}

	   	public function getEstadosEspecies2()
	   	{
	   		$estadosEspecies2 = EstadoEspecie2::where('idEstadoEspecie2', '>', '0')->get();

	   		return $estadosEspecies2;
	   	}
	}