'use strict';
app.controller('controladorIncidentes', ['$scope', '$timeout', 'servicioIncidentes', 'servicioEspecies', function($scope, $timeout, servicioIncidentes, servicioEspecies){

	// Inicializar especies
	servicioEspecies.obtenerEspecies().then(function(resultados){
		$scope.especies = resultados;
	});

	// Lista de incidentes
	$scope.incidentes = [];

	// Variables de paginación
	$scope.paginaActual = 1;
	$scope.resultadosDisponibles = [10, 20, 30, 40, 50];
	$scope.resultados = 10;

	// Retroalimentación al usuario
	$scope.mensaje = '';
	$scope.exito = false;
	$scope.errores = false;

	$scope.ocultarMensaje = function(){
		$scope.exito = false;
		$scope.errores = false;
	}

	$scope.actualizarPagina = function(pagina){
		$scope.paginaActual = pagina;
		$scope.avanzar = true;
		$scope.regresar = true;

		servicioIncidentes.obtenerIncidentes($scope.paginaActual, $scope.resultados).then(function(resultado){
			$scope.incidentes = resultado.incidentes;
			$scope.total = resultado.total;
			$scope.desde = resultado.desde;
			$scope.hasta = resultado.hasta;
			$scope.ultimaPagina = resultado.ultimaPagina;

			// Deshabilitar botones de avanzar y regresar
			if(pagina == 1){
				$scope.regresar = false;
			}
			if(pagina == $scope.ultimaPagina){
				$scope.avanzar = false;
			}
		});
	}

	$scope.eliminarIncidente = function(id){
		servicioIncidentes.eliminarIncidente(id).then(function(resultado){
			$scope.actualizarPagina($scope.paginaActual);

			if(resultado == 204){
				$scope.mensaje = 'El incidente ha sido eliminado';
				$scope.exito = true;
			}else{
				$scope.mensaje = 'El incidente no fue eliminado. Intentelo más tarde';
				$scope.exito = false;
			}
			$timeout($scope.ocultarMensaje, 3000);
		});
	}

	// TODO
	$scope.modificarIncidetne = function(){
		servicioIncidentes.modificarIncidetne().then(function(resultado){

		});
	}
}]);