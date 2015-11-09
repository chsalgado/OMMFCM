<?php
use services\ServicioOMMFCM;

class ServicioOMMFCMEspeciesTest extends TestCase 
{
	public function tearDown()
	{
		Mockery::close();
	}

	/// CREAR ESPECIES ///

	/**
	 * Prueba el método crearEspecies enviando parámetros válidos.
	 *
	 * @return void
	 */
	public function testCrearEspecie()
	{
		$servicio = new ServicioOMMFCM;
		
		$archivoJson = __DIR__ . '/jsons/crearEspecieConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);
 		
		$this->mock = Mockery::mock('Eloquent','Especie[save]');                                           
        $this->mock
          	 ->shouldReceive('save')
             ->once()
             ->andReturn('true');   
		
 		$this ->mock ->nombreComun = $datos['nombreComun'];
		$this ->mock ->nombreCientifico = $datos['nombreCientifico'];
		$this ->mock ->idEstadoEspecie = $datos['idEstadoEspecie'];
		$this ->mock ->idEstadoEspecie2 = $datos['idEstadoEspecie2'];

       	$respuestaActual = $servicio->crearEspecie($this->mock);
       	$this->assertEquals(201, $respuestaActual);
	}

	/**
	 * Prueba el método crearEspecies enviando parámetros inválidos.
	 *
	 * @return void
	 */
	public function testCrearEspecieConParametrosInvalidos()
	{
		$servicio = new ServicioOMMFCM;
		
		$archivoJson = __DIR__ . '/jsons/crearEspecieConParametrosInvalidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);
 		
 		$especie = new Especie($datos);
 		
       	$respuestaActual = $servicio->crearEspecie($especie);
       	$this->assertEquals(400, $respuestaActual);
	}

	/**
	 * Prueba el método crearEspecies sin enviar parámetros requeridos.
	 *
	 * @return void
	 */
	public function testCrearEspecieSinParametrosRequeridos()
	{
		$servicio = new ServicioOMMFCM;
		
		$archivoJson = __DIR__ . '/jsons/crearEspecieConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);
 		
		$especie = new Especie;
 		$especie ->nombreComun = $datos['nombreComun'];
		$especie ->nombreCientifico = $datos['nombreCientifico'];

		// Eliminamos algunos parámetros requeridos
		// $especie ->idEstadoEspecie = $datos['idEstadoEspecie'];
		// $especie ->idEstadoEspecie2 = $datos['idEstadoEspecie2'];

       	$respuestaActual = $servicio->crearEspecie($especie);
       	$this->assertEquals(400, $respuestaActual);
	}

	/**
	 * Prueba el método crearEspecies con un error al guardar la especie.
	 *
	 * @return void
	 */
	public function testCrearEspecieFallaAlGuardar()
	{
		$servicio = new ServicioOMMFCM;
		
		$archivoJson = __DIR__ . '/jsons/crearEspecieConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);
 		
		$this->mock = Mockery::mock('Eloquent','Especie[save]');                                           
        $this->mock
          	 ->shouldReceive('save')
             ->once()
             ->andReturn(false);   // Simulamos una falla al momento de guardar
		
 		$this ->mock ->nombreComun = $datos['nombreComun'];
		$this ->mock ->nombreCientifico = $datos['nombreCientifico'];
		$this ->mock ->idEstadoEspecie = $datos['idEstadoEspecie'];
		$this ->mock ->idEstadoEspecie2 = $datos['idEstadoEspecie2'];

       	$respuestaActual = $servicio->crearEspecie($this->mock);
       	$this->assertEquals(500, $respuestaActual);
	}

	/// MODIFICAR ESPECIES ///

	/**
	 * Prueba el método modificarEspecie enviando parámetros válidos.
	 *
	 * @return void
	 */
	public function testModificarEspecie()
	{
		$servicio = new ServicioOMMFCM;
		
		$archivoJson = __DIR__ . '/jsons/modificarEspecieConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);

	   	$this->mock = Mockery::mock('Eloquent','Especie[save, find]');                                           
        $this->mock
          	 ->shouldReceive('save')
             ->once()
             ->andReturn('true');   
		
        $this->mock
          	 ->shouldReceive('find')
             ->once()
             ->andReturn($this ->mock);

 		$this ->mock ->idEspecie = $datos['idEspecie'];
 		$this ->mock ->nombreComun = $datos['nombreComun'];
		$this ->mock ->nombreCientifico = $datos['nombreCientifico'];
		$this ->mock ->idEstadoEspecie = $datos['idEstadoEspecie'];
		$this ->mock ->idEstadoEspecie2 = $datos['idEstadoEspecie2'];

       	$respuestaActual = $servicio->modificarEspecie($this->mock);
       	$this->assertEquals(200, $respuestaActual);
	}

	/**
	 * Prueba el método modificarEspecie enviando parámetros inválidos.
	 *
	 * @return void
	 */
	public function testModificarEspecieConParametrosInvalidos()
	{
		$servicio = new ServicioOMMFCM;
		
		$archivoJson = __DIR__ . '/jsons/modificarEspecieConParametrosInvalidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);

	   	$especie = new Especie($datos);
 		
       	$respuestaActual = $servicio->modificarEspecie($especie);
       	$this->assertEquals(400, $respuestaActual);
	}

	/**
	 * Prueba el método modificarEspecies sin enviar parámetros requeridos.
	 *
	 * @return void
	 */
	public function testModificarEspecieSinParametrosRequeridos()
	{
		$servicio = new ServicioOMMFCM;
		
		$archivoJson = __DIR__ . '/jsons/modificarEspecieConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);
 		
 		// Sabemos que el massive assignment no asigna idEspecie
		$especie = new Especie($datos);
		// $especie ->idEspecie = $datos['idEspecie'];

       	$respuestaActual = $servicio->modificarEspecie($especie);
       	$this->assertEquals(400, $respuestaActual);
	}

	/**
	 * Prueba el método modificarEspecie con un error al guardar la especie.
	 *
	 * @return void
	 */
	public function testModificarEspecieFallaAlGuardar()
	{
		$servicio = new ServicioOMMFCM;
		
		$archivoJson = __DIR__ . '/jsons/modificarEspecieConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);

	   	$this->mock = Mockery::mock('Eloquent','Especie[save, find]');                                           
        $this->mock
          	 ->shouldReceive('save')
             ->once()
             ->andReturn(false);   // Simulamos una falla al momento de guardar
		
        $this->mock
          	 ->shouldReceive('find')
             ->once()
             ->andReturn($this ->mock);

 		$this ->mock ->idEspecie = $datos['idEspecie'];
 		$this ->mock ->nombreComun = $datos['nombreComun'];
		$this ->mock ->nombreCientifico = $datos['nombreCientifico'];
		$this ->mock ->idEstadoEspecie = $datos['idEstadoEspecie'];
		$this ->mock ->idEstadoEspecie2 = $datos['idEstadoEspecie2'];

       	$respuestaActual = $servicio->modificarEspecie($this->mock);
       	$this->assertEquals(500, $respuestaActual);
	}

	/**
	 * Prueba el método modificarEspecie con una especie no encontrada.
	 *
	 * @return void
	 */
	public function testModificarEspecieNoEncontrada()
	{
		$servicio = new ServicioOMMFCM;
		
		$archivoJson = __DIR__ . '/jsons/modificarEspecieConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);

	   	$this->mock = Mockery::mock('Eloquent','Especie[find]');                                           
		
        $this->mock
          	 ->shouldReceive('find')
             ->once()
             ->andReturn(null);    // Simulamos especie no encontrada

 		$this ->mock ->idEspecie = $datos['idEspecie'];
 		$this ->mock ->nombreComun = $datos['nombreComun'];
		$this ->mock ->nombreCientifico = $datos['nombreCientifico'];
		$this ->mock ->idEstadoEspecie = $datos['idEstadoEspecie'];
		$this ->mock ->idEstadoEspecie2 = $datos['idEstadoEspecie2'];

       	$respuestaActual = $servicio->modificarEspecie($this->mock);
       	$this->assertEquals(404, $respuestaActual);
	}

	/// ELIMINAR ESPECIES ///

	/**
	 * Prueba el método eliminarEspecie enviando parámetros válidos.
	 *
	 * @return void
	 */
	public function testEliminarEspecie()
	{
		$servicio = new ServicioOMMFCM;
		
	   	$this->mock = Mockery::mock('Eloquent','Especie[delete, find]');                                           
        $this->mock
          	 ->shouldReceive('delete')
             ->once()
             ->andReturn('true');   
		
        $this->mock
          	 ->shouldReceive('find')
             ->once()
             ->andReturn($this ->mock);

 		$this ->mock ->idEspecie = 1;

       	$respuestaActual = $servicio->eliminarEspecie($this->mock);

       	$this->assertEquals(200, $respuestaActual);
	}

	/**
	 * Prueba el método eliminarEspecie enviando parámetros inválidos.
	 *
	 * @return void
	 */
	public function testEliminarEspecieConParametrosInvalidos()
	{
		$servicio = new ServicioOMMFCM;
		
	   	$especie = new Especie();
	   	$especie ->idEspecie = 'idInvalido';
 		
       	$respuestaActual = $servicio->eliminarEspecie($especie);
       	$this->assertEquals(400, $respuestaActual);
	}

	/**
	 * Prueba el método eliminarEspecie sin enviar parámetros requeridos.
	 *
	 * @return void
	 */
	public function testEliminarEspecieSinParametrosRequeridos()
	{
		$servicio = new ServicioOMMFCM;
		
		$especie = new Especie();
		// $especie ->idEspecie = 1

       	$respuestaActual = $servicio->eliminarEspecie($especie);
       	$this->assertEquals(400, $respuestaActual);
	}

	/**
	 * Prueba el método eliminarEspecie con un error al borrar la especie.
	 *
	 * @return void
	 */
	public function testEliminarEspecieFallaAlBorrar()
	{
		$servicio = new ServicioOMMFCM;
		
	   	$this->mock = Mockery::mock('Eloquent','Especie[delete, find]');                                           
        $this->mock
          	 ->shouldReceive('delete')
             ->once()
             ->andReturn(false);   // Simulamos una falla al momento de guardar
		
        $this->mock
          	 ->shouldReceive('find')
             ->once()
             ->andReturn($this ->mock);

 		$this ->mock ->idEspecie = 1;

       	$respuestaActual = $servicio->eliminarEspecie($this->mock);

       	$this->assertEquals(500, $respuestaActual);
	}

	/**
	 * Prueba el método eliminarEspecie con una especie no encontrada.
	 *
	 * @return void
	 */
	public function testEliminarEspecieNoEncontrada()
	{
		$servicio = new ServicioOMMFCM;

	   	$this->mock = Mockery::mock('Eloquent','Especie[find]');                                           
		
        $this->mock
          	 ->shouldReceive('find')
             ->once()
             ->andReturn(null);    // Simulamos especie no encontrada

 		$this ->mock ->idEspecie = 1;

       	$respuestaActual = $servicio->eliminarEspecie($this->mock);
       	$this->assertEquals(404, $respuestaActual);
	}

	/**
	 * Prueba el método eliminarEspecie con una especie que contiene incidentes.
	 *
	 * @return void
	 */
	public function testEliminarEspecieConIncidentes()
	{
		$servicio = new ServicioOMMFCM;
		
	   	$this->mock = Mockery::mock('Eloquent','Especie[find]');                                           
		
        $this->mock
          	 ->shouldReceive('find')
             ->once()
             ->andReturn($this ->mock);

 		$this ->mock ->idEspecie = 1;

 		// La especie tiene incidentes, añadimos una incidente a la coleccion 'incidentes'
	   	$this->mockIncidente = Mockery::mock('Eloquent','Incidente');                                           
 		$this ->mock ->incidentes = new \Illuminate\Database\Eloquent\Collection;
 		$this ->mock ->incidentes-> add($this->mockIncidente);

       	$respuestaActual = $servicio->eliminarEspecie($this->mock);

       	$this->assertEquals(412, $respuestaActual);
	}


}
