<?php

class IncidentesControllerTest extends TestCase
{
	public function setUp()
    {
    	parent::setUp();
    }

	public function tearDown()
	{
		Mockery::close();
	}

	/// OBTENER INCIDENTES ///
	
	/**
	 * Prueba la ruta get api/incidentes enviando pagina, resultados y idEspecie. 
	 * @return [type] [description]
	 */
	public function testObtenerIncidentesPorEspecie()
	{	 
		// Generar Incidente que devuelve el servicio mock
	   	$incidentesEsperados = new Incidente();
	   	$incidentesEsperados -> rutaImagen = 'imagenes/imagen.jpg';

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo getIncidentesPorEspecie para que devuelva los incidentes esperados
	    $mock->shouldReceive('getIncidentesPorEspecie')->withAnyArgs()->once()->andReturn($incidentesEsperados);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);
	 
	    // Invocar al index de incidentesController
	    $respuestaActual = $this->call('GET', 'api/incidentes?pagina=1&resultados=2&idEspecie=1');

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => false,
			'incidentes' => $incidentesEsperados -> toArray()),
			200
		);

	    // Comparar que contenido y código sean iguales
		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'data'),  /* valor esperado */
            'data',  /* nombre del atributo */
            $respuestaActual); /* objeto actual */

		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'statusCode'), 
            'statusCode',
            $respuestaActual);
	}

	/**
	 * Prueba la ruta get api/incidentes enviando los parámetros pagina y resultados válidos.
	 * @return [type] [description]
	 */
	public function testObtenerIncidentesPaginados()
	{	 
		// Generar Incidente que devuelve el servicio mock
	   	$incidentesEsperados = new Incidente();
	   	$incidentesEsperados -> rutaImagen = 'imagenes/imagen.jpg';

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo getIncidentesPaginados para que devuelva los incidentes esperados
	    $mock->shouldReceive('getIncidentesPaginados')->withAnyArgs()->once()->andReturn($incidentesEsperados);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);
	 
	    // Invocar al index de incidentesController
	    $respuestaActual = $this->call('GET', 'api/incidentes?pagina=1&resultados=2');

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => false,
			'incidentes' => $incidentesEsperados -> toArray()),
			200
		);

	    // Comparar que contenido y código sean iguales
		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'data'),  /* valor esperado */
            'data',  /* nombre del atributo */
            $respuestaActual); /* objeto actual */

		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'statusCode'), 
            'statusCode',
            $respuestaActual);
	}

	/**
	 * Prueba la ruta get api/incidentes enviando el parámetros reporte.
	 * @return [type] [description]
	 */
	public function testObtenerIncidentesPorReporte()
	{	 
		// Generar Incidente que devuelve el servicio mock
	   	$incidentesEsperados = new Incidente();
	   	$incidentesEsperados -> rutaImagen = 'imagenes/imagen.jpg';

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo getIncidentesPorReporte para que devuelva los incidentes esperados.
		// getIncidentesPorReporte devuelve un arreglo de incidentes, no una colección.
	    $mock->shouldReceive('getIncidentesPorReporte')->withAnyArgs()->once()->andReturn($incidentesEsperados ->toArray());
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);
	 
	    // Invocar al index de incidentesController
	    $respuestaActual = $this->call('GET', 'api/incidentes?reporte=1');

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => false,
			'incidentes' => $incidentesEsperados -> toArray()),
			200
		);

	    // Comparar que contenido y código sean iguales
		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'data'),  /* valor esperado */
            'data',  /* nombre del atributo */
            $respuestaActual); /* objeto actual */

		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'statusCode'), 
            'statusCode',
            $respuestaActual);
	}

	/**
	 * Prueba la ruta get api/incidentes con Parámetros Invalidos 
	 * @return [type] [description]
	 */
	public function testObtenerIncidentesConParametrosInvalidos()
	{	 
		// Generar Incidente que devuelve el servicio mock
	    $incidentesEsperados = new Incidente();

	   	// Invocar al index de incidentesController
	    $respuestaActual = $this->call('GET', 'api/incidentes?pagina=1&resultados=2&idEspecie=Perro');

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => true),
			400
		);

	    // Comparar que contenido y código sean iguales
		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'data'),  /* valor esperado */
            'data',  /* nombre del atributo */
            $respuestaActual); /* objeto actual */

		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'statusCode'), 
            'statusCode',
            $respuestaActual);
	}

	/**
	 * Prueba la ruta get api/incidentes con parámetros extras  
	 * @return [type] [description]
	 */
	public function testObtenerIncidentesConParametrosExtras()
	{	 
		// Generar Incidente que devuelve el servicio mock
	    $incidentesEsperados = new Incidente();

	   	// Invocar al index de incidentesController
	    $respuestaActual = $this->call('GET', 'api/incidentes?carretera=guanajuato&pagina=1&resultados=2&idEspecie=1');

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => true),
			404
		);

	    // Comparar que contenido y código sean iguales
		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'data'),  /* valor esperado */
            'data',  /* nombre del atributo */
            $respuestaActual); /* objeto actual */

		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'statusCode'), 
            'statusCode',
            $respuestaActual);
	}

	/**
	 * Prueba la ruta get api/incidentes sin parámetros   
	 * @return [type] [description]
	 */
	public function testObtenerIncidentesSinParametros()
	{	 
		// Generar Incidente que devuelve el servicio mock
	    $incidentesEsperados = new Incidente();

	   	// Invocar al index de incidentesController
	    $respuestaActual = $this->call('GET', 'api/incidentes');

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => true),
			404
		);

	    // Comparar que contenido y código sean iguales
		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'data'),  /* valor esperado */
            'data',  /* nombre del atributo */
            $respuestaActual); /* objeto actual */

		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'statusCode'), 
            'statusCode',
            $respuestaActual);
	}

	/// CREAR INCIDENTES ///

	/**
	 * Prueba la ruta post api/incidentes con respuesta exitosa del servicio  
	 * @return [type] [description]
	 */
	public function testCrearIncidenteExito()
	{	 
		// codigo que regresa el mock
	   	$codigoEsperado = 201;

	   	// obtener los parámetros de la consulta
        $archivoJson = __DIR__ . '/jsons/crearIncidenteConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);
 
		// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo crearIncidente para obtener el codigo esperado
	    $mock->shouldReceive('crearIncidente')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);
	 
	    // Invocar al store de incidentes
	    $respuestaActual = $this->call('POST', 'api/incidentes', $datos);

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => false),
			$codigoEsperado
		);

	    // Comparar que contenido y código sean iguales
		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'data'),  /* valor esperado */
            'data',  /* nombre del atributo */
            $respuestaActual); /* objeto actual */

		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'statusCode'), 
            'statusCode',
            $respuestaActual);
	}

	/**
	 * Prueba la ruta post api/incidentes cuando el servicio devuelve un error 
	 * @return [type] [description]
	 */
	public function testCrearIncidenteConError()
	{	 
		// Generar el código que devuelve el servicio mock
	   	$codigoEsperado = 400;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo crearIncidente para obtener el codigo esperado
	    $mock->shouldReceive('crearIncidente')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Obtener los parámetros de la consulta
        $archivoJson = __DIR__ . '/jsons/crearIncidenteConParametrosInvalidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);

	   	// Invocar al store de incidentes
	    $respuestaActual = $this->call('POST', 'api/incidentes', $datos);

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => true),
			$codigoEsperado
		);

	    // Comparar que contenido y código sean iguales
		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'data'),  /* valor esperado */
            'data',  /* nombre del atributo */
            $respuestaActual); /* objeto actual */

		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'statusCode'), 
            'statusCode',
            $respuestaActual);
	}

	/**
	 * Prueba la ruta post api/incidentes con parámetros inválidos en la URL
	 * @return [type] [description]
	 */
	public function testCrearIncidenteConParametroEnUrl()
	{	 
		// Generar el código que devuelve el servicio mock
	   	$codigoEsperado = 404;

	   	// Invocar al store de incidentes
	    $respuestaActual = $this->call('POST', 'api/incidentes?unParametro=1');

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => true),
			$codigoEsperado
		);

	    // Comparar que contenido y código sean iguales
		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'data'),  /* valor esperado */
            'data',  /* nombre del atributo */
            $respuestaActual); /* objeto actual */

		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'statusCode'), 
            'statusCode',
            $respuestaActual);
	}

	/// MODIFICAR INCIDENTES ///

	/**
	 * Prueba la ruta put api/incidentes con respuesta exitosa del servicio 
	 * @return [type] [description]
	 */
	public function testModificarIncidenteExito()
	{	 
		// Generar código que devuelve el mock
	   	$codigoEsperado = 200;

	   	// obtener los parámetros de la consulta
        $archivoJson = __DIR__ . '/jsons/modificarIncidenteConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo modificarIncidente para obtener el codigo esperado
	    $mock->shouldReceive('modificarIncidente')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);
	 	
	    // Invocar al update de incidentes	 	
	    $respuestaActual = $this->call('PUT', 'api/incidentes/2', $datos);

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => false),
			$codigoEsperado
		);

	    // Comparar que contenido y código sean iguales
		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'data'),  /* valor esperado */
            'data',  /* nombre del atributo */
            $respuestaActual); /* objeto actual */

		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'statusCode'), 
            'statusCode',
            $respuestaActual);
	}

	/**
	 * Prueba la ruta put api/incidentes cuando el servicio devuelve un error 
	 * @return [type] [description]
	 */
	public function testModificarIncidenteConError()
	{	 
		// Generar código que devuelve el mock
	   	$codigoEsperado = 400;

	   	// obtener los parámetros de la consulta
        $archivoJson = __DIR__ . '/jsons/modificarIncidenteConParametrosInvalidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo modificarIncidente para obtener el codigo esperado
	    $mock->shouldReceive('modificarIncidente')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);
	 	
	    // Invocar al update de incidentes	 	
	    $respuestaActual = $this->call('PUT', 'api/incidentes/2', $datos);

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => true),
			$codigoEsperado
		);

	    // Comparar que contenido y código sean iguales
		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'data'),  /* valor esperado */
            'data',  /* nombre del atributo */
            $respuestaActual); /* objeto actual */

		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'statusCode'), 
            'statusCode',
            $respuestaActual);
	}

	/**
	 * Prueba la ruta put api/incidentes con parámetros inválidos en la URL
	 * @return [type] [description]
	 */
	public function testModificarIncidenteConParametroEnUrl()
	{	 
		// Generar el código esperado
	   	$codigoEsperado = 404;

	   	// Invocar al store de incidentes
	    $respuestaActual = $this->call('PUT', 'api/incidentes/2?unParametro=1');

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => true),
			$codigoEsperado
		);

	    // Comparar que contenido y código sean iguales
		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'data'),  /* valor esperado */
            'data',  /* nombre del atributo */
            $respuestaActual); /* objeto actual */

		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'statusCode'), 
            'statusCode',
            $respuestaActual);
	}

	/// ELIMINAR INCIDENTES ///

	/**
	 * Prueba la ruta delete api/incidentes con respuesta exitosa del servicio 
	 * @return [type] [description]
	 */
	public function testEliminarIncidenteExito()
	{	 
		// Generar código que devuelve el mock
	   	$codigoEsperado = 204;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo modificarIncidente para obtener el codigo esperado
	    $mock->shouldReceive('eliminarIncidente')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Invocar al delete de incidentes
	    $respuestaActual = $this->call('DELETE', 'api/incidentes/2');

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => false),
			$codigoEsperado
		);

	    // Comparar que contenido y código sean iguales
		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'data'),  /* valor esperado */
            'data',  /* nombre del atributo */
            $respuestaActual); /* objeto actual */

		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'statusCode'), 
            'statusCode',
            $respuestaActual);
	}

	/**
	 * Prueba la ruta delete api/incidentes cuando el servicio devuelve un error 
	 * @return [type] [description]
	 */
	public function testEliminarIncidenteConError()
	{	 
		// Generar código que devuelve el mock
	   	$codigoEsperado = 404;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo eliminarIncidente para obtener el codigo esperado
	    $mock->shouldReceive('eliminarIncidente')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Generar parámetros de la consulta
	 	$datos['nombreCientifico'] = 'Prueba';

	    // Invocar al delete de incidentes
	    $respuestaActual = $this->call('DELETE', 'api/incidentes/1', $datos);

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => true),
			$codigoEsperado
		);

	    // Comparar que contenido y código sean iguales
		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'data'),  /* valor esperado */
            'data',  /* nombre del atributo */
            $respuestaActual); /* objeto actual */

		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'statusCode'), 
            'statusCode',
            $respuestaActual);
	}

	/**
	 * Prueba la ruta delete api/incidentes con parámetros inválidos en la URL 
	 * @return [type] [description]
	 */
	public function testEliminarIncidenteConParametroEnUrl()
	{	 
		// Generar código qesperado
	   	$codigoEsperado = 404;

	   	// Invocar al delete de incidentes
	    $respuestaActual = $this->call('DELETE', 'api/incidentes/1?unParametro=1');

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => true),
			$codigoEsperado
		);

	    // Comparar que contenido y código sean iguales
		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'data'),  /* valor esperado */
            'data',  /* nombre del atributo */
            $respuestaActual); /* objeto actual */

		$this->assertAttributeEquals(
            PHPUnit_Framework_Assert::readAttribute($respuestaEsperada, 'statusCode'), 
            'statusCode',
            $respuestaActual);
	}
}