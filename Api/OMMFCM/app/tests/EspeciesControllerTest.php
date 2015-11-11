<?php

class EspeciesControllerTest extends TestCase 
{
	/// OBTENER ESPECIES ///

	/**
	 * Prueba la ruta get api/especies enviando parámetros válidos.
	 *
	 * @return void
	 */
	public function testObtenerEspeciesConParametrosValidos()
	{
	   	// Generar código esperado
		$codigoEsperado = 200;
	   	
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
	    $respuestaActual = $this->call('GET', 'api/especies?pagina=1&resultados=2');

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => false,
			'especies' => $especiesEsperadas -> toArray()),
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
	 * Prueba la ruta get api/especies enviando parámetros inválidos.
	 *
	 * @return void
	 */
	public function testObtenerEspeciesConParametrosInvalidos()
	{
	   	// Generar código esperado
		$codigoEsperado = 400;

	   	// Invocar al index de especiesControllers
	    $respuestaActual = $this->call('GET', 'api/especies?pagina=ABC&resultados=-2');

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
	 * Prueba la ruta get api/especies enviando parámetros extras.
	 *
	 * @return void
	 */
	public function testObtenerEspeciesConParametrosExtras()
	{
	   	// Generar código esperado
	   	$codigoEsperado = 404;

	   	// Invocar al index de especiesControllers
	    $respuestaActual = $this->call('GET', 'api/especies?pagina=ABC&resultados=-2&parametroExtra=extra']);

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
	 * Prueba la ruta get api/especies sin enviar parámetros.
	 *
	 * @return void
	 */
	public function testObtenerEspeciesSinParametros()
	{
	   // Generar código esperado
		$codigoEsperado = 200;
	   	
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
	    $respuestaActual = $this->call('GET', 'api/especies');

	    // Generar respuesta esperada
	    $respuestaEsperada = Response::json(array(
			'error' => false,
			'especies' => $especiesEsperadas -> toArray()),
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

	/// CREAR ESPECIES ///
	
	/**
	 * Prueba la ruta post api/especies con respuesta exitosa del servicio 
	 * @return [type] [description]
	 */
	public function testCrearEspecieExito()
	{	 
	   	// codigo que regresa el mock
	   	$codigoEsperado = 201;

		// obtener los parámetros de la consulta
        $archivoJson = __DIR__ . '/jsons/crearEspecieConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);
 
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
	 * Prueba la ruta post api/especies cuando el servicio devuelve un error 
	 * @return [type] [description]
	 */
	public function testCrearEspecieConError()
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
        $archivoJson = __DIR__ . '/jsons/crearEspecieConParametrosInvalidos.json';
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
	 * Prueba la ruta post api/especies con parámetros inválidos en la URL
	 * @return [type] [description]
	 */
	public function testCrearEspecieConParametroEnUrl()
	{	 
		// Generar el código que devuelve el servicio mock
	   	$codigoEsperado = 404;

	   	// Invocar al store de especies
	    $respuestaActual = $this->call('POST', 'api/especies?unParametro=1');

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
	
	/// MODIFICAR ESPECIES ///

	/**
	 * Prueba la ruta put api/especies con respuesta exitosa del servicio 
	 * @return [type] [description]
	 */
	public function testModificarEspecieExito()
	{	 
	   	// codigo que regresa el mock
	   	$codigoEsperado = 200;

		// obtener los parámetros de la consulta
        $archivoJson = __DIR__ . '/jsons/modificarEspecieConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);
 
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
	 * Prueba la ruta put api/especies cuando el servicio devuelve un error 
	 * @return [type] [description]
	 */
	public function testModificarEspecieConError()
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
        $archivoJson = __DIR__ . '/jsons/modificarEspecieConParametrosInvalidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);

	   	// Invocar al store de especies
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
	 * Prueba la ruta put api/especies con parámetros inválidos en la URL
	 * @return [type] [description]
	 */
	public function testModificarEspecieConParametroEnUrl()
	{	 
		// Generar el código esperado
	   	$codigoEsperado = 404;

	   	// Invocar al store de especies
	    $respuestaActual = $this->call('PUT', 'api/especies/1?unParametro=1');

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

	/// ELIMINAR ESPECIES ///
	
	/**
	 * Prueba la ruta delete api/especies con respuesta exitosa del servicio 
	 * @return [type] [description]
	 */
	public function testEliminarEspecieExito()
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
	 * Prueba la ruta delete api/especies cuando el servicio devuelve un error 
	 * @return [type] [description]
	 */
	public function testEliminarEspecieConError()
	{	 
		// Generar código que devuelve el mock
	   	$codigoEsperado = 404;

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
	 * Prueba la ruta delete api/especies con parámetros inválidos en la URL 
	 * @return [type] [description]
	 */
	public function testEliminarEspecieConParametroEnUrl()
	{	 
		// Generar código qesperado
	   	$codigoEsperado = 404;

	   	// Invocar al delete de especies
	    $respuestaActual = $this->call('DELETE', 'api/especies/1?unParametro=1');

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
