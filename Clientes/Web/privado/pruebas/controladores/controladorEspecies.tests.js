'use strict';
describe('controlador especies', function(){
	var $scope, $timeout, exito, error;

	var controladorEspecies, mockServicioEspecies;

	var cambiarExito = function(valExito){
        exito = valExito;
    }

    var cambiarError = function(valError){
    	error = valError;
    }

	beforeEach(module('appPrivada'));

	beforeEach(function(){

        // Mockea el confirm de eliminarEspecie
        spyOn(window, 'confirm').and.returnValue(true);

		mockServicioEspecies = jasmine.createSpyObj('servicioEspecies', ['obtenerEspeciesPaginadas', 'obtenerEstadosEspecies', 'eliminarEspecie']);

		inject(function($rootScope, $controller, $q, _$timeout_){
			$scope = $rootScope.$new();
			$timeout = _$timeout_;

			// Mock del servicio
			mockServicioEspecies.obtenerEspeciesPaginadas.and.callFake(function(){
				if(exito){
					return ($q.resolve({
						"especies":
							[{"idEspecie":14,"nombreComun":"nombre comun0","nombreCientifico":"nombre cientifico0","created_at":"2015-09-20 03:22:44","updated_at":"2015-09-20 03:22:44"},
							{"idEspecie":15,"nombreComun":"nombre comun1","nombreCientifico":"nombre cientifico1","created_at":"2015-09-20 03:22:44","updated_at":"2015-09-20 03:22:44"}
							],
						"total": 2,
                        "desde": 1,
                        "hasta": 2,
                        "ultimaPagina": 1
					}));
				}
				return ($q.reject());
			});

			mockServicioEspecies.obtenerEstadosEspecies.and.callFake(function(){
				if(exito){
					return ($q.resolve([{"idEstadoEspecie":1,"estado":"Sin Clasificar","created_at":"2015-10-16 00:00:00","updated_at":"2015-10-16 00:00:00"},{"idEstadoEspecie":2,"estado":"Amenazada","created_at":"2015-10-16 00:00:00","updated_at":"2015-10-16 00:00:00"}]));
				}
				return ($q.reject());
			})

			mockServicioEspecies.eliminarEspecie.and.callFake(function(){
				if(exito){
					return ($q.resolve());
				}
				return ($q.reject({"status": error}));
			});

			// Controlador
			controladorEspecies = $controller('controladorEspecies', {
				$scope: $scope,
				servicioEspecies: mockServicioEspecies
			});
		});
	});

	it('asigna variables iniciales', function(){
		expect($scope.especies.length).toBe(0);
		expect($scope.paginaActual).toEqual(1);
		expect($scope.resultadosDisponibles.length).toBe(5);
        expect($scope.resultados).toEqual(10);
        expect($scope.mensaje).toMatch('');
        expect($scope.exito).toBe(false);
        expect($scope.errores).toBe(false);
        expect($scope.estados).toBeUndefined();
        expect($scope.nombreEstado).toBeUndefined();
        expect($scope.avanzar).toBeUndefined();
        expect($scope.regresar).toBeUndefined();
        expect($scope.total).toBeUndefined();
        expect($scope.desde).toBeUndefined();
        expect($scope.hasta).toBeUndefined();
        expect($scope.ultimaPagina).toBeUndefined();
        expect($scope.nEstado).toBeUndefined();
	});

	it('obtiene los estados que puede tener una especie', function(){
		cambiarExito(true);
		$scope.obtenerEstadosEspecies();
		expect(mockServicioEspecies.obtenerEstadosEspecies).toHaveBeenCalled();
		$timeout.flush();
		expect($scope.estados).toEqual([{"idEstadoEspecie":1,"estado":"Sin Clasificar","created_at":"2015-10-16 00:00:00","updated_at":"2015-10-16 00:00:00"},{"idEstadoEspecie":2,"estado":"Amenazada","created_at":"2015-10-16 00:00:00","updated_at":"2015-10-16 00:00:00"}]);
	});

	it('falla al obtener los estados que puede tener una especie', function(){
		cambiarExito(false);
		$scope.obtenerEstadosEspecies();
		expect(mockServicioEspecies.obtenerEstadosEspecies).toHaveBeenCalled();
		$timeout.flush();
		expect($scope.estados).toBeUndefined();
	});

	it('actualiza la pagina', function(){
		cambiarExito(true);
		$scope.actualizarPagina(1);
		expect($scope.nombreEstado.length).toBe(0);
		expect($scope.paginaActual).toEqual(1);
        expect($scope.avanzar).toBe(true);
        expect($scope.regresar).toBe(true);
        expect(mockServicioEspecies.obtenerEspeciesPaginadas).toHaveBeenCalledWith(1, 10);
        $timeout.flush();
        expect($scope.especies).toEqual([{"idEspecie":14,"nombreComun":"nombre comun0","nombreCientifico":"nombre cientifico0","created_at":"2015-09-20 03:22:44","updated_at":"2015-09-20 03:22:44"},{"idEspecie":15,"nombreComun":"nombre comun1","nombreCientifico":"nombre cientifico1","created_at":"2015-09-20 03:22:44","updated_at":"2015-09-20 03:22:44"}]);
        expect($scope.total).toEqual(2);
        expect($scope.desde).toEqual(1);
        expect($scope.hasta).toEqual(2);
        expect($scope.ultimaPagina).toEqual(1);
        expect($scope.regresar).toBe(false);
        expect($scope.avanzar).toBe(false);
	});

	it('obtiene el nombre del estado de una especie', function(){
		cambiarExito(true);
		$scope.obtenerEstadosEspecies();
		$scope.actualizarPagina(1);
		$timeout.flush();
		$scope.nombreEstadoEspecie(2);
		expect($scope.nombreEstado).toEqual(['Amenazada']);
	});

	it('muestra mensaje de exito cuando se elimina una especie', function(){
		cambiarExito(true);
		var idEspecie = 4;
		$scope.eliminarEspecie(idEspecie);
		expect(mockServicioEspecies.eliminarEspecie).toHaveBeenCalledWith(idEspecie);
		$timeout.flush();
		expect($scope.mensaje).toBe('La especie ha sido eliminada');
	});

	it('muestra mensaje de error cuando no se puede eliminar una especie', function(){
		cambiarExito(false);
		cambiarError(500);
		var idEspecie = 4;
		$scope.eliminarEspecie(idEspecie);
		expect(mockServicioEspecies.eliminarEspecie).toHaveBeenCalledWith(idEspecie);
		$timeout.flush();
		expect($scope.mensaje).toBe('La especie no fue eliminada. Intentelo más tarde');
	});

	it('muestra mensaje de error cuando hay incidentes asociados con la especie que se quiere eliminar', function(){
		cambiarExito(false);
		cambiarError(412);
		var idEspecie = 4;
		$scope.eliminarEspecie(idEspecie);
		expect(mockServicioEspecies.eliminarEspecie).toHaveBeenCalledWith(idEspecie);
		$timeout.flush();
		expect($scope.mensaje).toBe('La especie no puede ser eliminada porque hay incidentes asociados a ella');

	});
});