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
		$pagina = Request::get('pagina');
	   	$resultados = Request::get('resultados');
	   	$idEspecie = Request::get('idEspecie');

	   	if(!$idEspecie)	
		{
			$incidentes = $this->servicioOMMFCM->getIncidentes($pagina, $resultados);
		}
		else
		{
			$incidentes = $this->servicioOMMFCM->getIncidentesPorEspecie($idEspecie, $pagina, $resultados);	
		}

		return Response::json(array(
			'error' => false,
			'incidentes' => $incidentes -> toArray()),
			200
		);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$incidente = new Incidente;
		$incidente -> idIncidente = null;
		$incidente -> fecha = Input::get('fecha');
		$incidente -> idEspecie = Input::get('idEspecie');
		$incidente -> long = Input::get('long');
		$incidente -> lat = Input::get('lat');
		$incidente -> mpioOrigen = Input::get('mpioOrigen');
		$incidente -> mpioDestino = Input::get('mpioDestino');
		$incidente -> km = Input::get('km');
		$imagen64  = Input::get('imagen');
		$extensionImg = Input::get('extension');
		$resultado = $this->servicioOMMFCM->crearIncidente($incidente, $imagen64, $extensionImg);

		if($resultado < 400)
		{
			return Response::json(array(
				'error' => false,
				'incidente' => $incidente),
				$resultado 
			);
		}
		else
		{
			return Response::json(array(
				'error' => true),
				$resultado
			);
		}	
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$incidente = new Incidente;
		$incidente -> idIncidente = $id;
		$incidente -> idEspecie = Input::get('idEspecie');

		$resultado = $this->servicioOMMFCM->modificarIncidente($incidente);

		if($resultado < 400)
		{
			return Response::json(array(
				'error' => false,
				'incidente' => $incidente),
				$resultado
			);
		}
		else
		{
			return Response::json(array(
				'error' => true),
				$resultado
			);
		}	
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$resultado = $this->servicioOMMFCM->eliminarIncidente($id);

		if($resultado < 400)
		{
			return Response::json(array(
				'error' => false),
				$resultado
			);
		}
		else
		{
			return Response::json(array(
				'error' => true),
				$resultado
			);
		}	
	}
}
