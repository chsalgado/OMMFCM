'use strict';
app.factory('servicioIncidentes', ['$http', function($http){

		var servicioBase = 'http://localhost/OMMFCM/Api/OMMFCM/public/';
		//var servicioBase = 'http://10.25.108.12/api/OMMFCM/public/';
		var fabricaServicioIncidentes = {};

		var _obtenerIncidentes = function(pagina, resultados){
			return $http.get(servicioBase + 'api/incidentes?pagina=' + pagina + '&resultados=' + resultados).then(function(resultado){
				return {
					incidentes: resultado.data.incidentes.data,
					total: resultado.data.incidentes.total,
					desde: resultado.data.incidentes.from,
					hasta: resultado.data.incidentes.to,
					ultimaPagina: resultado.data.incidentes.last_page
				};
			});
		};

		var _eliminarIncidente = function(id){
			return $http.delete(servicioBase + 'api/incidentes/' + id).then(function(resultado){
				return resultado.status;
			});
		};

		// TODO
		var _modificarIncidente = function(){

		};

		fabricaServicioIncidentes.obtenerIncidentes = _obtenerIncidentes;
		fabricaServicioIncidentes.eliminarIncidente = _eliminarIncidente;
		fabricaServicioIncidentes.modificarIncidente = _modificarIncidente;

		return fabricaServicioIncidentes;
}]);