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

		mockServicioEspecies = jasmine.createSpyObj('servicioEspecies', ['obtenerEspeciesPaginadas', 'obtenerEstadosEspecies', 'eliminarEspecie', 'modificarEspecie', 'agregarEspecie']);

		inject(function($rootScope, $controller, $q, _$timeout_){
			$scope = $rootScope.$new();
			$timeout = _$timeout_;

			// Mock del servicio
			mockServicioEspecies.obtenerEspeciesPaginadas.and.callFake(function(){
				if(exito){
					return ($q.resolve({
						"especies":
							[{"idEspecie":14,"nombreComun":"nombre comun0","nombreCientifico":"nombre cientifico0","idEstadoEspecie":2, "idEstadoEspecie2":1, "created_at":"2015-09-20 03:22:44","updated_at":"2015-09-20 03:22:44"},
							{"idEspecie":15,"nombreComun":"nombre comun1","nombreCientifico":"nombre cientifico1","idEstadoEspecie":1, "idEstadoEspecie2":1, "created_at":"2015-09-20 03:22:44","updated_at":"2015-09-20 03:22:44"}
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
					return ($q.resolve([
						[{"idEstadoEspecie":1,"estado":"Sin Clasificar","created_at":"2015-10-16 00:00:00","updated_at":"2015-10-16 00:00:00"},{"idEstadoEspecie":2,"estado":"Amenazada","created_at":"2015-10-16 00:00:00","updated_at":"2015-10-16 00:00:00"}],
						[{"idEstadoEspecie2":1,"estado":"Endemica","created_at":"2015-10-16 00:00:00","updated_at":"2015-10-16 00:00:00"},{"idEstadoEspecie2":2,"estado":"No Endemica","created_at":"2015-10-16 00:00:00","updated_at":"2015-10-16 00:00:00"}]
						]));
				}
				return ($q.reject());
			});

			mockServicioEspecies.eliminarEspecie.and.callFake(function(){
				if(exito){
					return ($q.resolve());
				}
				return ($q.reject({"status": error}));
			});

			mockServicioEspecies.modificarEspecie.and.callFake(function(){
				if(exito){
					return ($q.resolve());
				}
				return ($q.reject());
			});

			mockServicioEspecies.agregarEspecie.and.callFake(function(){
				if(exito){
					return ($q.resolve());
				}
				return ($q.reject());
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
		expect($scope.nuevaEspecie).toEqual({"nombreComun":null,"nombreCientifico":null,"idEstadoEspecie":1, "idEstadoEspecie2":1});
		expect($scope.paginaActual).toEqual(1);
		expect($scope.resultadosDisponibles.length).toBe(5);
        expect($scope.resultados).toEqual(10);
        expect($scope.mensaje).toMatch('');
        expect($scope.exito).toBe(false);
        expect($scope.errores).toBe(false);
        expect($scope.editandoEs).toBe(false);
        expect($scope.estados).toBeUndefined();
        expect($scope.estados2).toBeUndefined();
        expect($scope.nombreEstado).toBeUndefined();
        expect($scope.nombreEstado2).toBeUndefined();
        expect($scope.editando).toBeUndefined();
        expect($scope.avanzar).toBeUndefined();
        expect($scope.regresar).toBeUndefined();
        expect($scope.total).toBeUndefined();
        expect($scope.desde).toBeUndefined();
        expect($scope.hasta).toBeUndefined();
        expect($scope.ultimaPagina).toBeUndefined();
        expect($scope.nEstado).toBeUndefined();
        expect($scope.nEstado2).toBeUndefined();
	});

	it('obtiene los estados que puede tener una especie', function(){
		cambiarExito(true);
		$scope.obtenerEstadosEspecies();
		expect(mockServicioEspecies.obtenerEstadosEspecies).toHaveBeenCalled();
		$timeout.flush();
		expect($scope.estados).toEqual([{"idEstadoEspecie":1,"estado":"Sin Clasificar","created_at":"2015-10-16 00:00:00","updated_at":"2015-10-16 00:00:00"},{"idEstadoEspecie":2,"estado":"Amenazada","created_at":"2015-10-16 00:00:00","updated_at":"2015-10-16 00:00:00"}]);
		expect($scope.estados2).toEqual([{"idEstadoEspecie2":1,"estado":"Endemica","created_at":"2015-10-16 00:00:00","updated_at":"2015-10-16 00:00:00"},{"idEstadoEspecie2":2,"estado":"No Endemica","created_at":"2015-10-16 00:00:00","updated_at":"2015-10-16 00:00:00"}]);
	});

	it('falla al obtener los estados que puede tener una especie', function(){
		cambiarExito(false);
		$scope.obtenerEstadosEspecies();
		expect(mockServicioEspecies.obtenerEstadosEspecies).toHaveBeenCalled();
		$timeout.flush();
		expect($scope.estados).toBeUndefined();
		expect($scope.estados2).toBeUndefined();
	});

	it('actualiza la pagina', function(){
		cambiarExito(true);
		$scope.obtenerEstadosEspecies();
		$timeout.flush();
        expect(mockServicioEspecies.obtenerEspeciesPaginadas).toHaveBeenCalledWith(1, 10);
        expect($scope.especies).toEqual([{"idEspecie":14,"nombreComun":"nombre comun0","nombreCientifico":"nombre cientifico0","idEstadoEspecie":2, "idEstadoEspecie2":1, "created_at":"2015-09-20 03:22:44","updated_at":"2015-09-20 03:22:44"},{"idEspecie":15,"nombreComun":"nombre comun1","nombreCientifico":"nombre cientifico1","idEstadoEspecie":1, "idEstadoEspecie2":1, "created_at":"2015-09-20 03:22:44","updated_at":"2015-09-20 03:22:44"}]);
        expect($scope.total).toEqual(2);
        expect($scope.desde).toEqual(1);
        expect($scope.hasta).toEqual(2);
        expect($scope.ultimaPagina).toEqual(1);
        expect($scope.regresar).toBe(false);
        expect($scope.avanzar).toBe(false);
        expect($scope.editando).toEqual([false, false]);
        expect($scope.nombreEstado).toEqual(['Amenazada', 'Sin Clasificar']);
        expect($scope.nombreEstado2).toEqual(['Endemica', 'Endemica']);
	});

	it('muestra mensaje de exito cuando se elimina una especie', function(){
		cambiarExito(true);
		var idEspecie = 4;
		$scope.obtenerEstadosEspecies();
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

	it('muestra mensaje de exito cuando se modifica una especie', function(){
		cambiarExito(true);
		$scope.obtenerEstadosEspecies();
		$timeout.flush();

		var index = 1;
		var idEspecie = 2;
		var nombreComun = 'nombreComun';
		var nombreCientifico = 'nombreCientifico';
		var idEstadoEspecie = 1;
		var idEstadoEspecie2 = 1;
		$scope.modificarEspecie(index, idEspecie, nombreComun, nombreCientifico, idEstadoEspecie, idEstadoEspecie2);
		expect($scope.editando).toEqual([false, false]);
		expect($scope.editandoEs).toBe(false);
		expect(mockServicioEspecies.modificarEspecie).toHaveBeenCalledWith(idEspecie, nombreComun, nombreCientifico, idEstadoEspecie, idEstadoEspecie2);
		$timeout.flush();
		expect($scope.mensaje).toBe('La especie ha sido modificada');
	});

	it('muestra mensaje de error cuando no se puede modificar una especie', function(){
		cambiarExito(true);
		$scope.obtenerEstadosEspecies();
		$timeout.flush();

		cambiarExito(false);
		var index = 1;
		var idEspecie = 2;
		var nombreComun = 'nombreComun';
		var nombreCientifico = 'nombreCientifico';
		var idEstadoEspecie = 1;
		var idEstadoEspecie2 = 1;
		$scope.modificarEspecie(index, idEspecie, nombreComun, nombreCientifico, idEstadoEspecie, idEstadoEspecie2);
		expect($scope.editando).toEqual([false, false]);
		expect($scope.editandoEs).toBe(false);
		expect(mockServicioEspecies.modificarEspecie).toHaveBeenCalledWith(idEspecie, nombreComun, nombreCientifico, idEstadoEspecie, idEstadoEspecie2);
		$timeout.flush();
		expect($scope.mensaje).toBe('La especie no fue modificada. Intentelo más tarde');
	});

	it('muestra mensaje de exito cuando se agrega una especie', function(){
		cambiarExito(true);
		$scope.obtenerEstadosEspecies();
		$scope.agregarEspecie();
		expect(mockServicioEspecies.agregarEspecie).toHaveBeenCalled();
		$timeout.flush();
		expect($scope.mensaje).toBe('La especie ha sido agregada');
	});

	it('muestra mensaje de error cuando no se puede agregar una especie', function(){
		cambiarExito(false);
		$scope.agregarEspecie();
		expect(mockServicioEspecies.agregarEspecie).toHaveBeenCalled();
		$timeout.flush();
		expect($scope.mensaje).toBe('La especie no fue agregada. Intentelo más tarde')
	});
});