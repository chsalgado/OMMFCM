<?php
use org\bovigo\vfs\vfsStream;
use services\ServicioOMMFCM;

class ServicioOMMFCMIncidentesTest extends TestCase 
{
	private $root;
	private $imagen64 = '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';
	
	public function setUp()
    {
    	parent::setUp();
    }

	public function tearDown()
	{
		Mockery::close();
	}

	/// CREAR INCIDENTES ///

	/**
	 * Prueba el método crearIncidente enviando parámetros válidos.
	 *
	 * @return void
	 */
	public function testCrearIncidente()
	{
		$servicio = new ServicioOMMFCM;

		$archivoJson = __DIR__ . '/jsons/crearIncidenteConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);

	   	$this->mock = Mockery::mock('Eloquent','Incidente[save]');                                           
        $this->mock
          	 ->shouldReceive('save')
             ->once()
             ->andReturn('true');

        $this ->mock ->fecha = $datos['fecha'];
		$this ->mock ->long = $datos['long'];
		$this ->mock ->lat = $datos['lat'];

		$mockedRoot = vfsStream::setup('publicPath');
		$mockedDir = vfsStream::newDirectory('someDir/imagenes/incidentes');

		$mockedRoot->addChild($mockedDir);

		$respuestaActual = $servicio->crearIncidente($this->mock, $this ->imagen64, '.png', vfsStream::url('publicPath/someDir'));

		$this->assertEquals(200, $respuestaActual);
	}

	/**
	 * Prueba el método crearIncidente enviando parámetros inválidos.
	 *
	 * @return void
	 */
	public function testCrearIncidenteConParametrosInvalidos()
	{
		$servicio = new ServicioOMMFCM;
		
		$archivoJson = __DIR__ . '/jsons/crearIncidenteConParametrosInvalidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);
 		
 		$incidente = new Incidente($datos);
 		
       	$respuestaActual = $servicio->crearIncidente($incidente, $this ->imagen64, '.png', "C://thisIsNotImportantToThisTest");
       	$this->assertEquals(400, $respuestaActual);
	}

	/**
	 * Prueba el método crearIncidente sin enviar parámetros requeridos.
	 *
	 * @return void
	 */
	public function testCrearIncidenteSinParametrosRequeridos()
	{
		$servicio = new ServicioOMMFCM;
		
		$archivoJson = __DIR__ . '/jsons/crearIncidenteConParametrosInvalidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);
 		
 		$incidente = new Incidente();
 		$incidente ->fecha = $datos['fecha'];
		$incidente ->long = $datos['long'];
		//$incidente ->lat = $datos['lat'];

       	$respuestaActual = $servicio->crearIncidente($incidente, $this ->imagen64, '.png', "C://thisIsNotImportantToThisTest");
       	$this->assertEquals(400, $respuestaActual);
	}

	/**
	 * Prueba el método crearIncidente enviando parámetros nulos.
	 *
	 * @return void
	 */
	public function testCrearIncidenteConParametrosNulos()
	{
		$servicio = new ServicioOMMFCM;
		
 		$incidente = new Incidente();
 		
       	$respuestaActual = $servicio->crearIncidente($incidente, $this ->imagen64, null, "C://thisIsNotImportantToThisTest");
       	$this->assertEquals(400, $respuestaActual);
	}

	/**
	 * Prueba el método crearIncidente enviando una extension invalida.
	 *
	 * @return void
	 */
	public function testCrearIncidenteConExtensionInvalida()
	{
		$servicio = new ServicioOMMFCM;
		
 		$incidente = new Incidente();
 		
       	$respuestaActual = $servicio->crearIncidente($incidente, $this ->imagen64, ".exe", "C://thisIsNotImportantToThisTest");
       	$this->assertEquals(403, $respuestaActual);
	}

	/**
	 * Prueba el método crearIncidente enviando un string que no es imagen.
	 *
	 * @return void
	 */
	public function testCrearIncidenteConStringQueNoEsImagen()
	{
		$servicio = new ServicioOMMFCM;
		
 		$invalidString = base64_encode("invalidString");

 		$archivoJson = __DIR__ . '/jsons/crearIncidenteConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);
 		$incidente = new Incidente($datos);
 		
       	$respuestaActual = $servicio->crearIncidente($incidente, $invalidString, ".png", "C://thisIsNotImportantToThisTest");
       	$this->assertEquals(403, $respuestaActual);
	}

	/**
	 * Prueba el método crearIncidente enviando una ruta erronea.
	 *
	 * @return void
	 */
	public function testCrearIncidenteConRutaErronea()
	{
		$servicio = new ServicioOMMFCM;
		
 		$archivoJson = __DIR__ . '/jsons/crearIncidenteConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);
 		$incidente = new Incidente($datos);
 		
       	$respuestaActual = $servicio->crearIncidente($incidente, $this ->imagen64, '.png', "C://cinexistente");
       	$this->assertEquals(500, $respuestaActual);
	}

	/**
	 * Prueba el método crearIncidente simulando una falla al guardar.
	 *
	 * @return void
	 */
	public function testCrearIncidenteFallaAlGuardar()
	{
		$servicio = new ServicioOMMFCM;

		$archivoJson = __DIR__ . '/jsons/crearIncidenteConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);

	   	$this->mock = Mockery::mock('Eloquent','Incidente[save]');                                           
        $this->mock
          	 ->shouldReceive('save')
             ->once()
             ->andReturn(false);	// Simulamos falla al guardar

        $this ->mock ->fecha = $datos['fecha'];
		$this ->mock ->long = $datos['long'];
		$this ->mock ->lat = $datos['lat'];

		$mockedRoot = vfsStream::setup('publicPath');
		$mockedDir = vfsStream::newDirectory('someDir/imagenes/incidentes');

		$mockedRoot->addChild($mockedDir);

		$respuestaActual = $servicio->crearIncidente($this->mock, $this ->imagen64, '.png', vfsStream::url('publicPath/someDir'));

		$this->assertEquals(500, $respuestaActual);
	}

	/// MODIFICAR ESPECIES ///

	/**
	 * Prueba el método modificarIncidente enviando parámetros válidos.
	 *
	 * @return void
	 */
	public function testModificarIncidente()
	{
		$servicio = new ServicioOMMFCM;
		
		$archivoJson = __DIR__ . '/jsons/modificarIncidenteConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);

	   	$this->mock = Mockery::mock('Eloquent','Incidente[save, find]');                                           
        $this->mock
          	 ->shouldReceive('save')
             ->once()
             ->andReturn('true');   
		
        $this->mock
          	 ->shouldReceive('find')
             ->once()
             ->andReturn($this ->mock);

 		$this ->mock ->idEspecie = $datos['idEspecie'];
 		$this ->mock ->km = $datos['km'];
		$this ->mock ->ruta = $datos['ruta'];

       	$respuestaActual = $servicio->modificarIncidente($this->mock);
       	$this->assertEquals(200, $respuestaActual);
	}

	/**
	 * Prueba el método modificarIncidente enviando parámetros inválidos.
	 *
	 * @return void
	 */
	public function testModificarIncidenteConParametrosInvalidos()
	{
		$servicio = new ServicioOMMFCM;
		
		$archivoJson = __DIR__ . '/jsons/modificarIncidenteConParametrosInvalidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);

	   	$incidente = new Incidente($datos);

       	$respuestaActual = $servicio->modificarIncidente($incidente);
       	$this->assertEquals(400, $respuestaActual);
	}

	/**
	 * Prueba el método modificarIncidente sin enviar parámetros requeridos.
	 *
	 * @return void
	 */
	public function testModificarIncidenteSinParametrosRequeridos()
	{
		$servicio = new ServicioOMMFCM;
		
		$archivoJson = __DIR__ . '/jsons/modificarIncidenteConParametrosInvalidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);

	   	$incidente = new Incidente();
	   	//$incidente ->idEspecie = $datos['idEspecie'];
 		$incidente ->km = $datos['km'];
		$incidente ->ruta = $datos['ruta'];

       	$respuestaActual = $servicio->modificarIncidente($incidente);
       	$this->assertEquals(400, $respuestaActual);
	}

	/**
	 * Prueba el método modificarIncidente con un error al guardar la especie.
	 *
	 * @return void
	 */
	public function testModificarEspecieFallaAlGuardar()
	{
		$servicio = new ServicioOMMFCM;
		
		$archivoJson = __DIR__ . '/jsons/modificarIncidenteConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);

	   	$this->mock = Mockery::mock('Eloquent','Incidente[save, find]');                                           
        $this->mock
          	 ->shouldReceive('save')
             ->once()
             ->andReturn(false);   // Simulamos una falla al momento de guardar
		
        $this->mock
          	 ->shouldReceive('find')
             ->once()
             ->andReturn($this ->mock);

 		$this ->mock ->idEspecie = $datos['idEspecie'];
 		$this ->mock ->km = $datos['km'];
		$this ->mock ->ruta = $datos['ruta'];

       	$respuestaActual = $servicio->modificarIncidente($this->mock);
       	$this->assertEquals(500, $respuestaActual);
	}

	/**
	 * Prueba el método modificarIncidente con un incidente no encontrado.
	 *
	 * @return void
	 */
	public function testModificarIncidenteNoEncontrado()
	{
		$servicio = new ServicioOMMFCM;
		
		$archivoJson = __DIR__ . '/jsons/modificarIncidenteConParametrosValidos.json';
	   	$datos = json_decode(file_get_contents($archivoJson), true);

	   	$this->mock = Mockery::mock('Eloquent','Incidente[find]');                                           
		
        $this->mock
          	 ->shouldReceive('find')
             ->once()
             ->andReturn(null);    // Simulamos incidente no encontrado

 		$this ->mock ->idEspecie = $datos['idEspecie'];
 		$this ->mock ->km = $datos['km'];
		$this ->mock ->ruta = $datos['ruta'];

       	$respuestaActual = $servicio->modificarIncidente($this->mock);
       	$this->assertEquals(404, $respuestaActual);
	}

	/// ELIMINAR INCIDENTES ///

	/**
	 * Prueba el método eliminarIncidente enviando parámetros válidos.
	 *
	 * @return void
	 */
	public function testEliminarIncidente()
	{
		$servicio = new ServicioOMMFCM;
		
	   	$this->mock = Mockery::mock('Eloquent','Incidente[delete, find]');                                           
        $this->mock
          	 ->shouldReceive('delete')
             ->once()
             ->andReturn('true');   
		
        $this->mock
          	 ->shouldReceive('find')
             ->once()
             ->andReturn($this ->mock);

 		$this ->mock ->idIncidente = 1;

       	$respuestaActual = $servicio->eliminarIncidente($this->mock);

       	$this->assertEquals(204, $respuestaActual);
	}

	/**
	 * Prueba el método eliminarIncidente enviando parámetros inválidos.
	 *
	 * @return void
	 */
	public function testEliminarIncidenteConParametrosInvalidos()
	{
		$servicio = new ServicioOMMFCM;
		
	   	$incidente = new Incidente();
	   	$incidente ->idIncidente = 'idInvalido';
 		
       	$respuestaActual = $servicio->eliminarIncidente($incidente);
       	$this->assertEquals(400, $respuestaActual);
	}

	/**
	 * Prueba el método eliminarIncidente sin enviar parámetros requeridos.
	 *
	 * @return void
	 */
	public function testEliminarIncidenteSinParametrosRequeridos()
	{
		$servicio = new ServicioOMMFCM;
		
		$incidente = new Incidente();
		// $incidente ->idEspecie = 1

       	$respuestaActual = $servicio->eliminarIncidente($incidente);
       	$this->assertEquals(400, $respuestaActual);
	}

	/**
	 * Prueba el método eliminarIncidente con un error al borrar la especie.
	 *
	 * @return void
	 */
	public function testEliminarIncidenteFallaAlBorrar()
	{
		$servicio = new ServicioOMMFCM;
		
	   	$this->mock = Mockery::mock('Eloquent','Incidente[delete, find]');                                           
        $this->mock
          	 ->shouldReceive('delete')
             ->once()
             ->andReturn(false);   // Simulamos una falla al momento de guardar
		
        $this->mock
          	 ->shouldReceive('find')
             ->once()
             ->andReturn($this ->mock);

 		$this ->mock ->idIncidente = 1;

       	$respuestaActual = $servicio->eliminarIncidente($this->mock);

       	$this->assertEquals(500, $respuestaActual);
	}

	/**
	 * Prueba el método eliminarIncidente con un incidente no encontrado.
	 *
	 * @return void
	 */
	public function testEliminarIncidenteNoEncontrado()
	{
		$servicio = new ServicioOMMFCM;

	   	$this->mock = Mockery::mock('Eloquent','Incidente[find]');                                           
		
        $this->mock
          	 ->shouldReceive('find')
             ->once()
             ->andReturn(null);    // Simulamos incidente no encontrado

 		$this ->mock ->idIncidente = 1;

       	$respuestaActual = $servicio->eliminarIncidente($this->mock);
       	$this->assertEquals(404, $respuestaActual);
	}
}
