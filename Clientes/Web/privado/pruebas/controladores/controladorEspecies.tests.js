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

		mockServicioEspecies = jasmine.createSpyObj('servicioEspecies', ['obtenerEspeciesPaginadas', 'eliminarEspecie']);

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
        expect($scope.avanzar).toBeUndefined();
        expect($scope.regresar).toBeUndefined();
        expect($scope.total).toBeUndefined();
        expect($scope.desde).toBeUndefined();
        expect($scope.hasta).toBeUndefined();
        expect($scope.ultimaPagina).toBeUndefined();
	});

	it('actualiza la pagina', function(){
		cambiarExito(true);
		$scope.actualizarPagina(1);
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