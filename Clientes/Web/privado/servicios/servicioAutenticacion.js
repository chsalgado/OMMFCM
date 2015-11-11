'use strict';
app.factory('servicioAutenticacion', ['USUARIO', 'CONTRASENA', function(USUARIO, CONTRASENA){

	var fabricaServicioAutenticacion = {};

	var _request = function(config){
		config.headers = config.headers || {};

		var authData = USUARIO + ":" + CONTRASENA;
		if(authData){
			config.headers.Authorization = btoa(authData);
		}

		return config;
	}

	fabricaServicioAutenticacion.request = _request;

	return fabricaServicioAutenticacion;
}]);