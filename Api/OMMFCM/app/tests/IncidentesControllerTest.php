<?php

class IncidentesControllerTest extends TestCase {

	/**
	 * Prueba la ruta get api/incidentes enviando los 3 parámetros con valores válidos. 
	 * @return [type] [description]
	 */
	public function testObtenerIncidentesConParametrosValidos()
	{	 
		// Generar Incidente que devuelve el servicio mock
	   	$incidentesEsperados = new Incidente();
	   	$incidentesEsperados -> rutaImagen = 'imagenes/imagen.jpg';

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo getIncidentes/getIncidentesPorEspecie para que devuelva los incidentes esperados
	    $mock->shouldReceive('getIncidentesPorEspecie')->withAnyArgs()->once()->andReturn($incidentesEsperados);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);
	 
	    // Invocar al index de incidentesController
	    $respuestaActual = $this->call('GET', 'api/incidentes',['pagina' => '1', 'resultados' => '2', 'idEspecie' => '0']);

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
	public function testObtenerIncidentesConPaginaYResultadosValidos()
	{	 
		// Generar Incidente que devuelve el servicio mock
	   	$incidentesEsperados = new Incidente();
	   	$incidentesEsperados -> rutaImagen = 'imagenes/imagen.jpg';

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo getIncidentes/getIncidentesPorEspecie para que devuelva los incidentes esperados
	    $mock->shouldReceive('getIncidentes','getIncidentesPorEspecie')->withAnyArgs()->once()->andReturn($incidentesEsperados);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);
	 
	    // Invocar al index de incidentesController
	    $respuestaActual = $this->call('GET', 'api/incidentes', ['pagina' => '1', 'resultados' => '2']);

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

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo getIncidentes/getIncidentesPorEspecie para que devuelva los incidentes esperados
	    $mock->shouldReceive('getIncidentes','getIncidentesPorEspecie')->withAnyArgs()->once()->andReturn($incidentesEsperados);
		
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Invocar al index de incidentesController
	    $respuestaActual = $this->call('GET', 'api/incidentes', ['pagina' => 'ABC', 'resultados' => '-45', 'idEspecie' => 'Perro']);

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

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo getIncidentes/getIncidentesPorEspecie para que devuelva los incidentes esperados
	    $mock->shouldReceive('getIncidentes', 'getIncidentesPorEspecie')->withAnyArgs()->once()->andReturn($incidentesEsperados);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Invocar al index de incidentesController
	    $respuestaActual = $this->call('GET', 'api/incidentes',['carretera' => 'guanajuato', 'pagina' => 'ABC', 'resultados' => '-45', 'idEspecie' => 'Perro']);

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

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo getIncidentes/getIncidentesPorEspecie para que devuelva los incidentes esperados
	    $mock->shouldReceive('getIncidentes', 'getIncidentesPorEspecie')->withAnyArgs()->once()->andReturn($incidentesEsperados);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

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
	
	/**
	 * Prueba la ruta post api/incidentes con Parámetros Validos 
	 * @return [type] [description]
	 */
	public function testCrearIncidenteConParametrosValidos()
	{	 
		// codigo que regresa el mock
	   	$codigoEsperado = 200;

	   	// obtener los parámetros de la consulta
        $archivoJson = __DIR__ . '/jsons/crearIncidenteParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);
 
		// Generar Incidente que devuelve la ruta
 	   	$incidenteEsperado = new Incidente();
	   	$incidenteEsperado -> fecha = $datos['fecha'];
	   	$incidenteEsperado -> idEspecie = $datos['idEspecie'];
	   	$incidenteEsperado -> long = $datos['long'];
	   	$incidenteEsperado -> lat = $datos['lat'];
	   	$incidenteEsperado -> mpioOrigen = $datos['mpioOrigen'];
	   	$incidenteEsperado -> mpioDestino = $datos['mpioDestino'];
	   	$incidenteEsperado -> km = $datos['km'];
	   	$incidenteEsperado -> imagen = $datos['imagen'];
	   	$incidenteEsperado -> extension = $datos['extension'];

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
			'error' => false,
			'incidente' => $incidenteEsperado),
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
	 * Prueba la ruta post api/incidentes con Parámetros invalidos 
	 * @return [type] [description]
	 */
	public function testCrearIncidenteConParametrosInvalidos()
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
	 * Prueba la ruta post api/incidentes sin enviar parámetros
	 * @return [type] [description]
	 */
	public function testCrearIncidenteSinParametros()
	{	 
		// Generar código que devuelve el mock
	   	$codigoEsperado = 400;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo crearIncidente para obtener el codigo esperado
	    $mock->shouldReceive('crearIncidente')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Invocar al store de incidentes
	    $respuestaActual = $this->call('POST', 'api/incidentes');

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
	 * Prueba la ruta post api/incidentes con Parámetros extras 
	 * @return [type] [description]
	 */
	public function testCrearIncidenteConParametrosExtras()
	{	 
		// Generar código que devuelve el servicio mock
	   	$codigoEsperado = 404;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo crearIncidente para obtener el codigo esperado
	    $mock->shouldReceive('crearIncidente')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Obtener los parámetros de la consulta
        $archivoJson = __DIR__ . '/jsons/crearIncidenteConParametrosExtras.json';
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
	 * Prueba la ruta put api/incidentes con Parámetros Validos 
	 * @return [type] [description]
	 */
	public function testModificarIncidenteConParametrosValidos()
	{	 
		// Generar código que devuelve el mock e incidente esperado
	   	$codigoEsperado = 200;

	   	// Generar incidente que regresa la consulta
	   	$datos['idEspecie'] = 14;
	   	$incidenteEsperado = new Incidente();
	   	$incidenteEsperado -> idEspecie = $datos['idEspecie'];

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
			'error' => false,
			'incidente' => $incidenteEsperado),
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
	 * Prueba la ruta put api/incidentes con Parámetros invalidos (tipo)
	 * @return [type] [description]
	 */
	public function testModificarIncidenteConParametrosInvalidos()
	{	 
		// Generar código que devuelve el mock
	   	$codigoEsperado = 400;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo modificarIncidente para obtener el codigo esperado
	    $mock->shouldReceive('modificarIncidente')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);
	 
	    // Generar parámetros de la consulta
	    $data['idEspecie'] = 'Elefante';
	    
	    // Invocar al store de incidentes
	    $respuestaActual = $this->call('PUT', 'api/incidentes/2', $data);

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
	 * Prueba la ruta put api/incidentes sin enviar parámetros
	 * @return [type] [description]
	 */
	public function testModificarIncidenteSinParametros()
	{	 
		// Generar código que devuelve el mock
	   	$codigoEsperado = 404;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo modificarIncidente para obtener el codigo esperado
	    $mock->shouldReceive('modificarIncidente')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Invocar al store de incidentes
	    $respuestaActual = $this->call('PUT', 'api/incidentes/2');

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
	 * Prueba la ruta put api/incidentes con Parámetros extras 
	 * @return [type] [description]
	 */
	public function testModificarIncidenteConParametrosExtras()
	{	 
		// Generar código que devuelve el mock e incidente esperado
	   	$codigoEsperado = 404;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo modificarIncidente para obtener el codigo esperado
	    $mock->shouldReceive('modificarIncidente')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Generar parámetros de la consulta
	 	$datos['idEspecie'] = 14;
	 	$datos['mpioOrigen'] = 2;

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
	 * Prueba la ruta delete api/incidentes con Parámetros extras 
	 * @return [type] [description]
	 */
	public function testEliminarIncidente()
	{	 
		// Generar código que devuelve el mock e incidente esperado
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
	 * Prueba la ruta delete api/incidentes con Parámetros extras 
	 * @return [type] [description]
	 */
	public function testEliminarIncidenteConParametrosExtras()
	{	 
		// Generar código que devuelve el mock e incidente esperado
	   	$codigoEsperado = 404;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo modificarIncidente para obtener el codigo esperado
	    $mock->shouldReceive('eliminarIncidente')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Generar parámetros de la consulta
	 	$datos['idEspecie'] = 14;
	 	$datos['mpioOrigen'] = 2;

	    // Invocar al delete de incidentes
	    $respuestaActual = $this->call('DELETE', 'api/incidentes/2', $datos);

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
	 * Prueba la ruta delete api/incidentes con un incidente que no existe 
	 * @return [type] [description]
	 */
	public function testEliminarIncidenteConIdInexistente()
	{	 
		// Generar código que devuelve el mock e incidente esperado
	   	$codigoEsperado = 404;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo modificarIncidente para obtener el codigo esperado
	    $mock->shouldReceive('eliminarIncidente')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Invocar al delete de incidentes
	    $respuestaActual = $this->call('DELETE', 'api/incidentes/-1');

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
}
