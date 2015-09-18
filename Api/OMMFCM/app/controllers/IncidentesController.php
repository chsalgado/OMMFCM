<?php
use services\ServicioOMMFCMInterface;

class IncidentesController extends \BaseController {

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
		$incidentes = $this->servicioOMMFCM->getIncidentes();

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
		$resultado = $this->servicioOMMFCM->crearIncidente($incidente, $imagen64);

		if ($resultado)
		{
			return Response::json(array(
				'error' => false,
				'incidente' => $incidente),
				201
			);
		}
		else
		{
			return Response::json(array(
				'error' => true),
				500
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
		$incidente -> idIncidente = Input::get('idIncidente');
		$incidente -> idEspecie = Input::get('idEspecie');

		$resultado = $this->servicioOMMFCM->modificarIncidente($incidente);

		if ($resultado)
		{
			return Response::json(array(
				'error' => false,
				'incidente' => $incidente),
				200
			);
		}
		else
		{
			return Response::json(array(
				'error' => true),
				500
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

		if ($resultado)
		{
			return Response::json(array(
				'error' => false),
				204
			);
		}
		else
		{
			return Response::json(array(
				'error' => true),
				500
			);
		}	
	}


}
