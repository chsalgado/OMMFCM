<?php
use services\ServicioOMMFCMInterface;

class IncidentesController extends \BaseController 
{
	public $servicioOMMFCM;

    public function __construct(ServicioOMMFCMInterface $servicioOMMFCM)
     {
        $this->servicioOMMFCM = $servicioOMMFCM; 
     }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$paramsQty = count(Request::query());

		// Para reportes, el unico argumento en la url debe ser $reporte
		if ($paramsQty == 1)
		{
			if(!Request::has('reporte'))
			{
	   			return Response::json(array('error' => true), 404);
			}

	   		$reporte = Request::get('reporte');

			if(!is_numeric($reporte))
			{
				return Response::json(array('error' => true), 400);
			}

			$incidentes = $this->servicioOMMFCM->getIncidentesPorReporte($reporte);

			return Response::json(array('error' => false, 'incidentes' => $incidentes),	200);	
		}

		// Si no se trata de un reporte, pagina y resultados son obligatorios
		if(($paramsQty != 2 && $paramsQty != 3) || !Request::has('pagina') || !Request::has('resultados'))
		{
			return Response::json(array('error' => true), 404);
		}

		$pagina = Request::get('pagina');
	   	$resultados = Request::get('resultados');

	   	if(!is_numeric($pagina) || !is_numeric($resultados))
		{
			return Response::json(array('error' => true), 400);
		}

	   	// idEspecie es opcional
	   	if(!Request::has('idEspecie'))
		{
			$incidentes = $this->servicioOMMFCM->getIncidentesPaginados($pagina, $resultados);
		}
		else
		{
	   		$idEspecie = Request::get('idEspecie');

	   		if(!is_numeric($idEspecie))
			{
				return Response::json(array('error' => true), 400);
			}

			$incidentes = $this->servicioOMMFCM->getIncidentesPorEspecie($idEspecie, $pagina, $resultados);	
		}

		return Response::json(array('error' => false, 'incidentes' => $incidentes -> toArray()), 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		if(count(Request::query()) != 0)
		{
			return Response::json(array('error' => true), 404);
		}

		// El controlador unicamente toma parametros de la solicitud y crea un modelo usando massive assignment
		$incidente = new Incidente(Input::all());		

		$imagen64  = Input::get('imagen');
		$extensionImg = Input::get('extension');
		$publicPath = public_path();
		$resultado = $this->servicioOMMFCM->crearIncidente($incidente, $imagen64, $extensionImg, $publicPath);

		return Response::json(array('error' => $resultado >= 400), $resultado);	
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		if(count(Request::query()) != 0)
		{
			return Response::json(array('error' => true), 404);
		}

		// El controlador unicamente toma parametros de la solicitud y crea un modelo usando massive assignment
		$incidente = new Incidente(Input::all());

		// El id no es parte de $fillable en el modelo ni de Input:all, por lo que la asignamos manualmente
		$incidente -> idIncidente = $id;

		$resultado = $this->servicioOMMFCM->modificarIncidente($incidente);

		return Response::json(array('error' => $resultado >= 400), $resultado);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if(count(Request::query()) != 0)
		{
			return Response::json(array('error' => true), 404);
		}

		$incidente = new Incidente;
		$incidente -> idIncidente = $id;

		$resultado = $this->servicioOMMFCM->eliminarIncidente($incidente);

		return Response::json(array('error' => $resultado >= 400), $resultado);
	}
}
