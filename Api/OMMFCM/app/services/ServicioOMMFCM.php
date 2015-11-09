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
				$columnas = array('fecha', 'ruta', 'km','nombreCientifico', 'nombreComun', 'estadosEspecies.estado', 'estadosEspecies2.estado AS estado2');
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
						->get($columnas);				

			return $incidentes;
		}

		public function getIncidentesPaginados($pagina, $resultados)
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

	    	// Le asignamos la idEspecie 0 para definir que no se le ha asignado ninguna especie
	    	$incidente -> idEspecie = 0;	    	

	    	$thumbnailAncho = 200;
	    	$thumbnailAlto = 200;
			$imagen = base64_decode($imagen64);
			$ruta = public_path() . "/imagenes/incidentes/";
			$nombreImagen 	= "incidente_" . time();
			$rutaThumbnail = $ruta . $nombreImagen . "_thumbnail" . $extensionImg;
			$nombreImagen  = $nombreImagen . $extensionImg;

			$resultado = File::put($ruta . $nombreImagen, $imagen);

			if(!$resultado)
			{
				return 500;
			}
			
			$imagenThumbnail = new ImageResize($ruta . $nombreImagen, $imagen);
			$imagenThumbnail -> resize($thumbnailAncho, $thumbnailAlto);
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

			if(is_null($incidente -> idEspecie))
			{
				return 400;	
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
	   		$especies = Especie::all();

	   		// Validar que los dos parámetros de paginación fueron enviados, de lo contrario mandar todas las especies
	   		if(!is_null($pagina) && !is_null($resultados))
	   		{
				Especie::resolveConnection()->getPaginator()->setCurrentPage($pagina);
		   		$especies = Especie::where('idEspecie', '>', 0)->paginate($resultados);	   			
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

			$nuevaEspecie = new Especie;
			$nuevaEspecie = $especie;
			$resultado = $nuevaEspecie -> save(); 

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
	   		$estadosEspecies = EstadoEspecie::where('idEstadoEspecie', '>', '0')->get();

	   		return $estadosEspecies;
	   	}

	   	public function getEstadosEspecies2()
	   	{
	   		$estadosEspecies2 = EstadoEspecie2::where('idEstadoEspecie2', '>', '0')->get();

	   		return $estadosEspecies2;
	   	}
	}