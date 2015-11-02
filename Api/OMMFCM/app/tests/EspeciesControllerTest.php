<?php

class EspeciesControllerTest extends TestCase {

/*	public function setUp()
	{
	    parent::setUp();
	 
	    $this->mock('Services\ServicioOMMFCMInterface');
	}
	
	public function tearDown()
    {
        Mockery::close();
    } 
	
	public function mock($class)
	{
	    $mock = Mockery::mock($class);
	 
	    $this->app->instance($class, $mock);
	 
	    return $mock;
	}
	*/
	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testIndex()
	{
	   	// Generar especies que devuelve el servicio mock
	   	$especiesEsperadas = new Especie();
	   	$especiesEsperadas -> nombreComun = 'unNombreDePrueba';

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

}
