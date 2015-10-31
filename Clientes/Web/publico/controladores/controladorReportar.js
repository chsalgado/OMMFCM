'use strict';
app.controller('controladorReportar', ['$scope', '$window', '$filter', '$timeout', 'servicioIncidentes', function($scope, $window, $filter, $timeout, servicioIncidentes){

	// Modelo con el formato requerido por input date
	$scope.fecha = new Date();

	// Objeto usado para guardar los datos del incidente
	$scope.incidente = {
		"fecha": '',
		"long": 0,
		"lat": 0,
		"imagen": '',
		"extension": ''
	};

	// Objeto usado para guardar el destino y origen que trazan la ruta
	$scope.ruta = {
		"origen": {
			"idEstado": 1,
			"nombreMunicipio": ''
		},
		"destino": {
			"idEstado": 1,
			"nombreMunicipio": ''
		}
	};

	// Retroalimentación al usuario
    $scope.mensaje = '';
    $scope.exito = false;
    $scope.errores = false;

    // Oculta la retroalimentacion al usuario
    $scope.ocultarMensaje = function(){
        $scope.exito = false;
        $scope.errores = false;
    }

    // Limpia la forma después de enviar el incidente
    $scope.limpiarForma = function(){
    	$scope.incidente.fecha = '';
    	$scope.incidente.long = 0;
    	$scope.incidente.lat = 0;
    	$scope.incidente.imagen = '';
    	$scope.incidente.extension = '';
    	$scope.fecha = new Date();
    	document.getElementById("foto").innerHTML = 'sin foto';
    	document.getElementById("imagen").value = null;
    	document.getElementById("vistaPrevia").innerHTML = '<img src="/img/vistaPrevia.png" width="100%" title="Vista Previa">'
    	$scope.limpiarMapa();
    	$scope.ruta.origen.idEstado = 1;
    	$scope.ruta.destino.idEstado = 1;
    	$scope.obtenerMunicipiosOrigen($scope.ruta.origen.idEstado);
    	$scope.obtenerMunicipiosDestino($scope.ruta.destino.idEstado);
    }

	// Obtiene todos los estados
	$scope.obtenerEstados = function(){
		servicioIncidentes.obtenerEstados().then(function(resultado){
			$scope.estados = resultado;
		});
	}

	// Obtiene los municipios asociados al estado de origen
	$scope.obtenerMunicipiosOrigen = function(){
		servicioIncidentes.obtenerMunicipios($scope.ruta.origen.idEstado).then(function(resultado){
			$scope.municipiosOrigen = resultado;
			$scope.ruta.origen.nombreMunicipio = $scope.municipiosOrigen[0].nombre_municipio;
		});
	}

	// Obtiene los municipios asociados al estado de destino
	$scope.obtenerMunicipiosDestino = function(){
		servicioIncidentes.obtenerMunicipios($scope.ruta.destino.idEstado).then(function(resultado){
			$scope.municipiosDestino = resultado;
			$scope.ruta.destino.nombreMunicipio = $scope.municipiosDestino[0].nombre_municipio;
		});
	}

	// Agrega un incidente
	$scope.agregarIncidente = function(){
		$scope.foto = document.getElementById("foto").innerHTML;
		if($scope.foto == 'sin foto'){
			$scope.mensaje = 'Selecciona una imagen';
			$scope.errores = true;
		}else if($scope.fecha > new Date()){
			$scope.mensaje = 'La fecha no puede ser posterior al día de hoy';
			$scope.errores = true;
		}else if(!$scope.marker){
			$scope.mensaje = 'Selecciona la ubicación de incidente';
			$scope.errores = true;
		}else{
			// Formato apropiado para POST
			$scope.incidente.imagen = ($scope.foto).split('base64,')[1];
			$scope.incidente.extension = "." + ($scope.foto).split('image/')[1].split(';')[0];
			$scope.incidente.fecha = $filter('date')($scope.fecha, 'yyyy-MM-dd HH:mm:ss');
			$scope.incidente.lat = $scope.marker.getPosition().lat();
			$scope.incidente.long = $scope.marker.getPosition().lng();

			servicioIncidentes.agregarIncidente($scope.incidente).then(function(resultado){
				$scope.limpiarForma();
				$scope.mensaje = 'El incidente ha sido registrado. Gracias por tu participación';
				$scope.exito = true;
        		$timeout($scope.ocultarMensaje, 6000);
			}, function(resultado){
				$scope.mensaje = 'El incidente no pudo ser registrado. Por favor inténtalo más tarde';
				$scope.errores = true;
        		$timeout($scope.ocultarMensaje, 6000);
			});
		}
        $timeout($scope.ocultarMensaje, 4000);
	}

	/****************** MAPA ******************/
	$scope.map;
	$scope.toRender = new Array();
	
	// Inicializa el mapa
	$window.initMap = function(){
	    $scope.directionsService = new google.maps.DirectionsService();
	    $scope.directionsDisplay = new google.maps.DirectionsRenderer();
	    $scope.mexico = new google.maps.LatLng(23.045963, -102.537750);
	    $scope.mapOptions = {
	        zoom: 7,
	        center: $scope.mexico
	    }
	    $scope.map = new google.maps.Map(document.getElementById("map-canvas"), $scope.mapOptions);
	    $scope.directionsDisplay.setMap($scope.map);
	    google.maps.event.addListener($scope.map, 'click', function(event) {
	        colocarMarcador(event.latLng);
	    });
	}

	// Limpia el mapa cuando se trazan nuevas rutas
	$scope.limpiarMapa = function(){
		if($scope.marker)
            $scope.marker.setPosition(null);
	    for(var i = 0; i < $scope.toRender.length; i++){
	        $scope.toRender[i].setMap(null);
	    }
	    $scope.toRender = new Array();
	    $scope.map.setCenter($scope.mexico);
	    $scope.map.setZoom(7);
	}

	// Coloca el marcador de la ubicación en la que sucedió el incidente
	$scope.marker;
	function colocarMarcador(location){
	  if ($scope.marker){
	    $scope.marker.setPosition(location);
	  }else{
	    $scope.marker = new google.maps.Marker({
	      position: location,
	      draggable:true,
	      map: $scope.map
	    });
	  }
	}

	// Calcula las rutas utilizando los municipios origen y destino
	$scope.calcRuta = function(){
		// Necesario porque existen municipios con el mismo nombre en distintos estados
		$scope.estadoOrigen = $filter('filter')($scope.estados, function(resultado){
			return resultado.id_estado === $scope.ruta.origen.idEstado;
		})[0];
		$scope.estadoDestino = $filter('filter')($scope.estados, function(resultado){
			return resultado.id_estado === $scope.ruta.destino.idEstado;
		})[0];
        $scope.request = {
            origin: $scope.ruta.origen.nombreMunicipio + ',' + $scope.estadoOrigen.estado + ',Mexico',
            destination: $scope.ruta.destino.nombreMunicipio + ',' + $scope.estadoDestino.estado + ',Mexico',
            travelMode: google.maps.TravelMode.DRIVING,
            provideRouteAlternatives: true,
        };
        $scope.directionsService.route($scope.request, function(result, status) {
            if (status == google.maps.DirectionsStatus.OK) {

                $scope.limpiarMapa();

                for (var i = 0; i < result.routes.length; i++) {

                    $scope.directionsDisplay = new google.maps.DirectionsRenderer({
                        polylineOptions: {
                          strokeColor: "#14D730",
                          strokeOpacity: 0.7
                        }
                    });
                    $scope.directionsDisplay.setMap($scope.map);
                    $scope.directionsDisplay.setDirections(result);
                    $scope.directionsDisplay.setRouteIndex(i);
                    $scope.toRender.push($scope.directionsDisplay);
                }
            }
        });
    }
}]);

