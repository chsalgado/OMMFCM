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
		$incidente -> fecha = Request::get('fecha');
		$incidente -> idEspecie = Request::get('idEspecie');
		$incidente -> rutaFoto = 'oli.jpg';
		$incidente -> long = Request::get('long');
		$incidente -> lat = Request::get('lat');
		$incidente -> mpioOrigen = Request::get('mpioOrigen');
		$incidente -> mpioDestino = Request::get('mpioDestino');
		$incidente -> km = Request::get('km');

		$resultado = $this->servicioOMMFCM->crearIncidente($incidente);

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
		$incidente -> id = Request::get('id');
		$incidente -> idEspecie = Request::get('idEspecie');

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
