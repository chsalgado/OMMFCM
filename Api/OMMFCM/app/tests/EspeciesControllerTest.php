<?php

class EspeciesControllerTest extends TestCase 
{
	/**
	 * Prueba la ruta get api/especies enviando parámetros válidos.
	 *
	 * @return void
	 */
	public function testObtenerEspeciesConParametrosValidos()
	{
	   	// Generar especies que devuelve el servicio mock
	   	$especiesEsperadas = new Especie();
	   	$especiesEsperadas -> nombreComun = 'especieTest';

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo getEspecies para que devuelva las especies esperadas
	    $mock->shouldReceive('getEspecies')->withAnyArgs()->once()->andReturn($especiesEsperadas);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);
	 
	    // Invocar al index de especiesControllers
	    $respuestaActual = $this->call('GET', 'api/especies', ['pagina' => '1', 'resultados' => '2']);

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => false,
			'especies' => $especiesEsperadas -> toArray()),
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
	 * Prueba la ruta get api/especies enviando parámetros inválidos.
	 *
	 * @return void
	 */
	public function testObtenerEspeciesConParametrosInvalidos()
	{
	   	// Generar especies que devuelve el servicio mock
	   	$especiesEsperadas = new Especie();

	   	// Invocar al index de especiesControllers
	    $respuestaActual = $this->call('GET', 'api/especies', ['pagina' => 'ABC', 'resultados' => '-2']);

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
	 * Prueba la ruta get api/especies enviando parámetros extras.
	 *
	 * @return void
	 */
	public function testObtenerEspeciesConParametrosExtras()
	{
	   	// Generar especies que devuelve el servicio mock
	   	$especiesEsperadas = new Especie();

	   	// Invocar al index de especiesControllers
	    $respuestaActual = $this->call('GET', 'api/especies', ['pagina' => 'ABC', 'resultados' => '-2', 'parametroExtra' => 'extra']);

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
	 * Prueba la ruta get api/especies sin enviar parámetros.
	 *
	 * @return void
	 */
	public function testObtenerEspeciesSinParametros()
	{
	   	// Generar especies que devuelve el servicio mock
	   	$especiesEsperadas = new Especie();

	    // Invocar al index de especiesControllers
	    $respuestaActual = $this->call('GET', 'api/especies');

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
	 * Prueba la ruta post api/especies con parámetros válidos 
	 * @return [type] [description]
	 */
	public function testCrearEspecieConParametrosValidos()
	{	 
	   	// codigo que regresa el mock
	   	$codigoEsperado = 201;

		// obtener los parámetros de la consulta
        $archivoJson = __DIR__ . '/jsons/especieConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);
 
		// Generar Especie que devuelve la ruta
 	   	$especieEsperada = new Especie();
	   	$especieEsperada -> nombreComun = $datos['nombreComun'];
	   	$especieEsperada -> nombreCientifico = $datos['nombreCientifico'];
	   	$especieEsperada -> idEstadoEspecie = $datos['idEstadoEspecie'];
	   	$especieEsperada -> idEstadoEspecie2 = $datos['idEstadoEspecie2'];

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo crearEspecie para obtener el codigo esperado
	    $mock->shouldReceive('crearEspecie')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);
	 
	    // Invocar al store de especies
	    $respuestaActual = $this->call('POST', 'api/especies', $datos);

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => false,
			'especie' => $especieEsperada),
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
	 * Prueba la ruta post api/especies con parámetros invalidos 
	 * @return [type] [description]
	 */
	public function testCrearEspecieConParametrosInvalidos()
	{	 
		// Generar el código que devuelve el servicio mock
	   	$codigoEsperado = 400;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo crearEspecie para obtener el codigo esperado
	    $mock->shouldReceive('crearEspecie')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Obtener los parámetros de la consulta
        $archivoJson = __DIR__ . '/jsons/especieConParametrosInvalidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);

	   	// Invocar al store de especies
	    $respuestaActual = $this->call('POST', 'api/especies', $datos);

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
	 * Prueba la ruta post api/especies sin enviar parámetros
	 * @return [type] [description]
	 */
	public function testCrearEspecieSinParametros()
	{	 
		// Generar código que devuelve el mock
	   	$codigoEsperado = 400;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo crearEspecie para obtener el codigo esperado
	    $mock->shouldReceive('crearEspecie')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Invocar al store de especies
	    $respuestaActual = $this->call('POST', 'api/especies');

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
	 * Prueba la ruta post api/especies con parámetros extras 
	 * @return [type] [description]
	 */
	public function testCrearEspecieConParametrosExtras()
	{	 
		// Generar código que devuelve el servicio mock
	   	$codigoEsperado = 400;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo crearEspecie para obtener el codigo esperado
	    $mock->shouldReceive('crearEspecie')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Obtener los parámetros de la consulta
        $archivoJson = __DIR__ . '/jsons/especieConParametrosExtras.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);
	    
	    // Invocar al store de especies
	    $respuestaActual = $this->call('POST', 'api/especies', $datos);

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
	 * Prueba la ruta put api/especies con parámetros válidos 
	 * @return [type] [description]
	 */
	public function testModificarEspecieConParametrosValidos()
	{	 
	   	// codigo que regresa el mock
	   	$codigoEsperado = 200;

		// obtener los parámetros de la consulta
        $archivoJson = __DIR__ . '/jsons/especieConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);
 
		// Generar Especie que devuelve la ruta
 	   	$especieEsperada = new Especie();
	   	$especieEsperada -> nombreComun = $datos['nombreComun'];
	   	$especieEsperada -> nombreCientifico = $datos['nombreCientifico'];
	   	$especieEsperada -> idEstadoEspecie = $datos['idEstadoEspecie'];
	   	$especieEsperada -> idEstadoEspecie2 = $datos['idEstadoEspecie2'];

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo modificarEspecie para obtener el codigo esperado
	    $mock->shouldReceive('modificarEspecie')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);
	 
	    // Invocar al update de especies
	    $respuestaActual = $this->call('PUT', 'api/especies/1', $datos);

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => false,
			'especie' => $especieEsperada),
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
	 * Prueba la ruta put api/especies con parámetros invalidos 
	 * @return [type] [description]
	 */
	public function testModificarEspecieConParametrosInvalidos()
	{	 
		// Generar el código que devuelve el servicio mock
	   	$codigoEsperado = 400;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo modificarEspecie para obtener el codigo esperado
	    $mock->shouldReceive('modificarEspecie')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Obtener los parámetros de la consulta
        $archivoJson = __DIR__ . '/jsons/especieConParametrosInvalidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);

	   	// Invocar al update de especies
	    $respuestaActual = $this->call('PUT', 'api/especies/1', $datos);

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
	 * Prueba la ruta put api/especies sin enviar parámetros
	 * @return [type] [description]
	 */
	public function testModificarEspecieSinParametros()
	{	 
		// Generar código que devuelve el mock
	   	$codigoEsperado = 400;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo modificarEspecie para obtener el codigo esperado
	    $mock->shouldReceive('modificarEspecie')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Invocar al update de especies
	    $respuestaActual = $this->call('PUT', 'api/especies/1');

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
	 * Prueba la ruta put api/especies con parámetros extras 
	 * @return [type] [description]
	 */
	public function testModificarEspecieConParametrosExtras()
	{	 
		// Generar el código que devuelve el servicio mock
	   	$codigoEsperado = 400;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo modificarEspecie para obtener el codigo esperado
	    $mock->shouldReceive('modificarEspecie')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Obtener los parámetros de la consulta
        $archivoJson = __DIR__ . '/jsons/especieConParametrosExtras.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);

	   	// Invocar al update de especies
	    $respuestaActual = $this->call('PUT', 'api/especies/1', $datos);

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
	 * Prueba la ruta delete api/especies con parámetros validos 
	 * @return [type] [description]
	 */
	public function testEliminarEspecie()
	{	 
		// Generar código que devuelve el mock
	   	$codigoEsperado = 204;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo eliminarEspecie para obtener el codigo esperado
	    $mock->shouldReceive('eliminarEspecie')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Invocar al delete de especies
	    $respuestaActual = $this->call('DELETE', 'api/especies/1');

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
	 * Prueba la ruta delete api/especies con parámetros extras 
	 * @return [type] [description]
	 */
	public function testEliminarEspecieConParametrosExtras()
	{	 
		// Generar código que devuelve el mock
	   	$codigoEsperado = 400;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo eliminarEspecie para obtener el codigo esperado
	    $mock->shouldReceive('eliminarEspecie')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Generar parámetros de la consulta
	 	$datos['nombreCientifico'] = 'Prueba';

	    // Invocar al delete de especies
	    $respuestaActual = $this->call('DELETE', 'api/especies/1', $datos);

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
	 * Prueba la ruta delete api/especies con una especie que no existe simulando un error interno 
	 * @return [type] [description]
	 */
	public function testEliminarEspecieError()
	{	 
		// Generar código que devuelve el mock
	   	$codigoEsperado = 404;

	   	// Crear servicio mock
		$mock = Mockery::mock('Services\ServicioOMMFCMInterface');

		// Mockear la llamada a metodo eliminarEspecie para obtener el codigo esperado
	    $mock->shouldReceive('eliminarEspecie')->withAnyArgs()->once()->andReturn($codigoEsperado);
	 
	 	// Inyectar servicio mock
	    $this->app->instance('Services\ServicioOMMFCMInterface', $mock);

	    // Invocar al delete de especies
	    $respuestaActual = $this->call('DELETE', 'api/especies/-1');

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
