'use strict';
app.factory('servicioEspecies', ['$http', function($http){

		var fabricaServicioEspecies = {};

		var _obtenerEspecies = function(){
			return $http.get(servicioBase + 'api/especies').then(function(resultados){
					return resultados.data.especies;
			});
		};

		fabricaServicioEspecies.obtenerEspecies = _obtenerEspecies;

		return fabricaServicioEspecies;
}]);