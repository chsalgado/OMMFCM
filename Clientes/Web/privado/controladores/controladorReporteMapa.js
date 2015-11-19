app.controller('controladorReporteMapa', function($scope, ServicioReporteMapa, uiGmapGoogleMapApi, uiGmapIsReady){
	//Variables para controlar elementos de la vista
	$scope.mostrar_mapa_calor = false;
	$scope.btn_deshabilitado = true;
	$scope.fecha_inicial = new Date();
	$scope.fecha_final = new Date();
	$scope.fecha_inicial_abierta = false;
	$scope.fecha_final_abierta = false;
	$scope.borrar_ruta_deshabilitado = true;
	$scope.origen = '';
	$scope.destino = '';

	// Variables para uso interno del controlador
	var incidentes = [];
	var especies = [];

	/**
	*	Funcion que recupera las especies de la base de datos
	*/
	ServicioReporteMapa._obtenerEspecies()
	.then(function(respuesta){
		especies = respuesta; // Guardar las especies en una variable de controlador 
	});

	/**
	*	Funcion que se ejecuta despues de que el api de google maps ha sido cargado
	* 	@return mapa: objeto que contiene un mapa de google
	*/
	uiGmapGoogleMapApi
	.then(function(mapa) {
		// Recuperar incidentes de la base de datos
		ServicioReporteMapa._obtenerIncidentes()
		.then(function(respuesta){
			incidentes = respuesta; // Guardar los incidentes en una variable del Controlador
			$scope.fecha_inicial = incidentes[0].fecha;
			$scope.fecha_final = incidentes[incidentes.length - 1].fecha;
			iniciarVariablesMapa(); // Iniciar las variables del mapa
		});
    });

	/**
	*	Funcion que se ejecuta en el momento en que el mapaha sido cargado
	*	@return instancias_mapa: arreglo de objetos mapa que han sido cargados
	*/
	uiGmapIsReady.promise(1)
	.then(function(instancias_mapa) {
		$scope.btn_deshabilitado = false;
    });

	/*
	*	Funcion que inicializa las variables que se van a utilizar en el mapa
	*/
	function iniciarVariablesMapa(){
		// Objeto mapa 
		$scope.mapa = { 
			center: { 
				latitude: 19.41220201, 
				longitude: -99.12139893
			},
			zoom: 5,
			heatLayerCallback: function(layer) {
				cargarDatosMapaCalor(layer);
			},
			control: {}, 
			marcadores: ServicioReporteMapa._obtenerMarcadores(incidentes)
		};

		// Objeto ventana para mostrar información asociada a un marcador en el mapa
		$scope.ventana_info_marcadores = {
			coordenadas: {
				latitude: 0,
				longitude: 0,
			},
			mostrar: false
		};
	}
	
	/**
	*	Funcion que llama al servicio de direcciones de google para calcular una ruta
	*	de acuerdo a un origen y destino para posteriormente imprimir la ruta en el mapa
	*	
	*	@param origen: string con lugar de origen
	*	@param destino: string con el lugar destino
	*/
	$scope.calcularRuta = function(origen, destino){
		// Inicializar variables para obtener y dibujar la ruta
		var directionsService = new google.maps.DirectionsService;
		if($scope.directionsDisplay == null)
	  		$scope.directionsDisplay = new google.maps.DirectionsRenderer;

	  	// Establecer el mapa donde se va a mostrar la ruta
	  	$scope.directionsDisplay.setMap($scope.mapa.control.getGMap());

	  	// Llamar al servicio de google para obtener una ruta de acuerdo a los parametros especificados
	  	directionsService.route({
		    origin: origen + ', Mexico',
		    destination: destino + ', Mexico',
		    travelMode: google.maps.TravelMode.DRIVING,
		    provideRouteAlternatives: true
	  	}, 
	  	function(respuesta, status) {
	    	if (status === google.maps.DirectionsStatus.OK) {
	      		$scope.directionsDisplay.setDirections(respuesta);
	      		$scope.borrar_ruta_deshabilitado = false;
	    	} else {
	      		window.alert('Directions request failed due to ' + status);
	    	}
	  	});
	}

	/**
	*	Funcion que elimina la ruta dibujada en el mapa
	*/
	$scope.borrarRuta = function(){
		if($scope.directionsDisplay != null){
			$scope.directionsDisplay.setMap(null);
			$scope.borrar_ruta_deshabilitado = true;
			$scope.mapa.control.getGMap().setCenter({ 
				latitude: 19.41220201, 
				longitude: -99.12139893
			});
			$scope.mapa.control.getGMap().setZoom(5);
			$scope.origen = '';
			$scope.destino = '';
		}
	}

	/**
	*	Funcion que filtra los incidentes de acuerdo a uan fecha inicial, final y a un arreglo de incidentes
	*	especificados desde la vista
	*/
	$scope.filtrarIncidentes = function(){
		if($scope.fecha_inicial.getTime() > $scope.fecha_final.getTime()){
			alert('Fecha de inicio es mayor que la fecha final');
			return;
		}
		var especies = $scope.especies_seleccionadas || [];
		var arr_filtrados = [];
		for(i=0; i<incidentes.length; i++){
			if(incidentes[i].fecha.getTime() >= $scope.fecha_inicial.getTime() 
				&& incidentes[i].fecha.getTime() <= $scope.fecha_final.getTime()
				&& buscarEspecie(incidentes[i], especies)){
				arr_filtrados.push(incidentes[i]);
			}
		}

		$scope.mapa.marcadores = [];
		$scope.mapa.marcadores = ServicioReporteMapa._obtenerMarcadores(arr_filtrados);
	}

	/** 
	*	Funcion que filtra las especies de acuerdo a un string parametro como consulta
	*	
	*	@param consulta: string para realizar el filtrado
	*	@return arr_filtrado: arreglo de especies que cumplen con la consulta pasada como parametro
	*/
	$scope.filtrarEspecies = function(consulta){
		var arr_filtrado = [];

		for(i=0; i<especies.length; i++){
			var nombreEspecie = especies[i].nombreEspecie;

			if(nombreEspecie.toLowerCase().indexOf(consulta.toLowerCase()) > -1){
				arr_filtrado.push(especies[i]);
			}

			if(arr_filtrado.length == 10){
				return arr_filtrado;
			}
		}
		return arr_filtrado;
	}

	/**
	*	Funcion que muestra información relacionada a un marcador seleccionado
	*	
	*	@param marcador: marcador seleccionado en el mapa
	*/
	$scope.mostrarInfoMarcador = function(marcador){
		$scope.ventana_info_marcadores.coordenadas.latitude = marcador.model.latitude;
		$scope.ventana_info_marcadores.coordenadas.longitude = marcador.model.longitude;
		$scope.ventana_info_marcadores.nombreComun = marcador.model.nombreComun;
		$scope.ventana_info_marcadores.nombreCientifico = marcador.model.nombreCientifico;
		$scope.ventana_info_marcadores.fecha = marcador.model.fecha;
		$scope.ventana_info_marcadores.mostrar = true;
	}

	/**
	* 	Funcion que cierra la ventana de informacion de un marcador 
	*/
	$scope.cerrarVentanaInfo = function(){
		$scope.ventana_info_marcadores.mostrar = false;
	}

	/**
	*	Funcion que se ejecuta al seleccionar el elemento de seleccion de fecha inicial
	*	@param $event: objeto de evento de javascript asociado al elemento de seleccion de fecha inicial
	*/
	$scope.abrirFechaInicial = function($event) {
    	$scope.fecha_inicial_abierta = true;
  	};

  	/**
  	*	Funcion que se ejecuta al seleccionar el elemento de seleccion de fecha final 
  	*
  	*	@param $event: objeto de evento de javascript asociado al elemento de seleccion de fecha final
  	*/
  	$scope.abrirFechaFinal = function($event) {
    	$scope.fecha_final_abierta = true;
  	};

  	/**
  	*	Funcion que cambia el valor de la fecha final con respecto a la inicial
  	*/
  	$scope.actualizarFechaFinal = function(){
  		if($scope.fecha_inicial > $scope.fecha_final)
  			$scope.fecha_final = $scope.fecha_inicial;
  	}

  	/**
  	*	Funcion que establece los datos para el mapa de calor
  	*
  	*	@param capa_mapa_calor: capa donde se va a mostrar el mapa de calor
  	*/
    function cargarDatosMapaCalor(capa_mapa_calor) {
	    var puntosMapaCalor = ServicioReporteMapa._obtenerPuntosMapaCalor(incidentes);
	    capa_mapa_calor.setData(puntosMapaCalor);
    };

    /**
    *	Funcion que busca una especie de acuerdo a su id
	*
	*	@param incidente: objeto incidente con una especie a buscar
	*	@param arreglo_especies: arreglo de especies 
    */
    function buscarEspecie(incidente, arreglo_especies){
    	if(arreglo_especies.length == 0){
    		return true;
    	}
    	for(j=0; j<arreglo_especies.length; j++){
    		if(incidente.idEspecie == arreglo_especies[j].idEspecie){
    			return true;
    		}
    	}
    	return false;
    };
})