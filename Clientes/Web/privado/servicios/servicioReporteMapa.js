app.factory('ServicioReporteMapa', ['$http', 'ngServicio', function($http, ngServicio) {

    /**
    *   Funcion que convierte un string de fecha en un objeto fecha de Javacript
    *
    *   @param jsonDateString: string en formato de fecha 
    */
	function parseJsonDate(fecha){
        return new Date(fecha);
    }

	return {

        /**
        *   Funcion que recupera los incidentes del api y convierte su fecha a un objeto de Javascript
        *
        *   @return incidentes: arreglo de objetos incidente con fechas en objeto tipo Javascript
        */
		_obtenerIncidentes: function(){
            return $http.get(ngServicio.apiServicioBase + 'api/incidentes?reporte=2')
            .then(function(respuesta){
                var incidentes = respuesta.data.incidentes;

                for(var i=0; i<incidentes.length; i++){
                    incidentes[i].fecha = parseJsonDate(incidentes[i].fecha);
                }

                return incidentes;
            });
		},

        /** 
        *   Funcion que crea un nuevo arreglo de puntos de google maps para el mapa de calor
        *
        *   @param arreglo_incidentes: arreglo de objetos incidente 
        *   @return puntos: arreglo de objetos de punto de localizacion de google maps
        */
		_obtenerPuntosMapaCalor: function(arreglo_incidentes) {
			var puntos = [];

            for(i = 0; i<arreglo_incidentes.length; i++){
            	var lat = arreglo_incidentes[i].lat;
            	var lon = arreglo_incidentes[i].long;
            	var localizacion =  new google.maps.LatLng(lat, lon);
            	puntos.push(localizacion);
            }

            return puntos;
        },

        /**
        *   Funcion que crea un nuevo arreglo de marcadores para poder desplegarlos en el mapa
        *   
        *   @param arreglo_incidentes: arreglo de objetos incidente
        *   @return marcadores: arreglo de objetos marcador
        */
        _obtenerMarcadores: function(arreglo_incidentes) {
        	var marcadores = [];
            
        	for(i = 0; i<arreglo_incidentes.length; i++){
            	var marcador = {
            		latitude: arreglo_incidentes[i].lat,
            		longitude: arreglo_incidentes[i].long,
            		idEspecie: arreglo_incidentes[i].idEspecie,
            		nombreComun: arreglo_incidentes[i].nombreComun,
                    nombreCientifico: arreglo_incidentes[i].nombreCientifico,
            		fecha: arreglo_incidentes[i].fecha
            	};
            	marcador['id'] = i 
            	marcadores.push(marcador);
        	}

        	return marcadores;
        },

        /*
        *   Funcion que recupera las especies de la base de datos y las guarda en un arreglo modificado
        *   
        *   @return especies: arreglo de objetos especie modificado para la busqueda
        */
        _obtenerEspecies: function(){

            return $http.get(ngServicio.apiServicioBase + 'api/especies')
            .then(function(respuesta){
                var especies = [];
                var especies_res = respuesta.data.especies;

                for(var i=0; i<especies_res.length; i++){
                    var objEspecie = {
                        idEspecie: especies_res[i].idEspecie,
                        nombreEspecie: especies_res[i].nombreComun + ' - ' + especies_res[i].nombreCientifico
                    };
                    especies.push(objEspecie);
                }

                return especies;
            });
        }
	}

}]);