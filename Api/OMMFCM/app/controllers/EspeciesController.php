<?php
use services\ServicioOMMFCMInterface;

class EspeciesController extends \BaseController 
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
		$especies = $this->servicioOMMFCM->getEspecies();

		return Response::json(array(
			'error' => false,
			'especies' => $especies -> toArray()),
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
		$especie = new Especie;		
		$especie -> nombreComun = Input::get('nombreComun');
		$especie -> nombreCientifico = Input::get('nombreCientifico');
		$resultado = $this->servicioOMMFCM->crearEspecie($especie);

		if ($resultado <= 400)
		{
			return Response::json(array(
				'error' => false,
				'especie' => $especie),
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
		$especie = new Especie;
		$especie -> idEspecie = $id;
		$especie -> nombreComun = Input::get('nombreComun');
		$especie -> nombreCientifico = Input::get('nombreCientifico');

		$resultado = $this->servicioOMMFCM->modificarEspecie($especie);

		if ($resultado <= 400)
		{
			return Response::json(array(
				'error' => false,
				'especie' => $especie),
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
		$resultado = $this->servicioOMMFCM->eliminarEspecie($id);

		if ($resultado <= 400)
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
