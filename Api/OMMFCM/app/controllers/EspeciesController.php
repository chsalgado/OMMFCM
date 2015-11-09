<?php
use services\ServicioOMMFCMInterface;

class EspeciesController extends \BaseController 
{
	public $servicioOMMFCM;

    public function __construct(ServicioOMMFCMInterface $servicioOMMFCM)
    {
        $this->servicioOMMFCM = $servicioOMMFCM; 
    }

    public function tearDown()
	{
		Mockery::close();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$responseCode = 200;
		$paramsQty = count(Request::query());

		$pagina = Request::get('pagina');
		$resultados = Request::get('resultados');
		
		if($paramsQty != 0)
		{
			if($paramsQty != 2 || !Request::has('pagina') || !Request::has('resultados'))
			{
				return Response::json(array('error' => true), 404);
			}
			
			if(!is_numeric($pagina) || !is_numeric($resultados))
			{
				return Response::json(array('error' => true), 400);
			}
		}
		   	
		$especies = $this->servicioOMMFCM->getEspecies($pagina, $resultados);
	
		return Response::json(array('error' => false, 'especies' => $especies -> toArray()), $responseCode);
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
		$especie = new Especie(Input::all());		

		$resultado = $this->servicioOMMFCM->crearEspecie($especie);

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

		$especie = new Especie;

		// El controlador unicamente toma parametros de la solicitud y crea un modelo usando massive assignment
		$especie = new Especie(Input::all());

		// El id no es parte de $fillable en el modelo ni de Input:all, por lo que la asignamos manualmente
		$especie -> idEspecie = $id;

		$resultado = $this->servicioOMMFCM->modificarEspecie($especie);

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

		$especie = new Especie;
		$especie -> idEspecie = $id;

		$resultado = $this->servicioOMMFCM->eliminarEspecie($especie);

		return Response::json(array('error' => $resultado >= 400), $resultado);
	}
}
