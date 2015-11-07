'use strict';
app.factory('servicioIncidentes', ['$http', 'ngServicio', function($http, ngServicio) {

    var servicioBase = ngServicio.apiServicioBase;
    var fabricaServicioIncidentes = {};

    // Obtiene los estados
    var _obtenerEstados = function(){
    	return $http.get(servicioBase + 'api/estados').then(function(resultado){
    		return resultado.data.estados;
    	});
    };

    // Obtiene los municipios asociados a un estado
    var _obtenerMunicipios = function(idEstado){
    	return $http.get(servicioBase + 'api/municipios?estado=' + idEstado).then(function(resultado){
    		return resultado.data.municipios;
    	});
    };

    // Agrega un incidente
    var _agregarIncidente = function(incidente){
        return $http.post(servicioBase + 'api/incidentes', incidente).then(function(resultado){
            return resultado.status;
        });
    };

    fabricaServicioIncidentes.obtenerEstados = _obtenerEstados;
    fabricaServicioIncidentes.obtenerMunicipios = _obtenerMunicipios;
    fabricaServicioIncidentes.agregarIncidente = _agregarIncidente;

    return fabricaServicioIncidentes;
}]);
