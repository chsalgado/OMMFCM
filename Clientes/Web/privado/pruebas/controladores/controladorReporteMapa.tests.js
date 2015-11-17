'use strict';
describe('controlador reporte', function(){
	var $scope, $timeout, exito;

	var controladorReporteMapa, mockServicioReporteMapa;

	var cambiarExito = function(valExito){
		exito = valExito;
	}

	beforeEach(module('appPrivada'));

	beforeEach(function(){
		mockServicioReporteMapa = jasmine.createSpyObj('ServicioReporteMapa', ['_obtenerIncidentes', '_obtenerEspecies']);

		inject(function($rootScope, $controller, $q, _$timeout_){
			$scope = $rootScope.$new();
			$timeout = _$timeout_;

			// Mock del servicio
			mockServicioReporteMapa._obtenerIncidentes.and.callFake(function(){
				if(exito){
					return ($q.resolve([{"fecha":"2015-03-12 19:18:06","lat":"21.32008096","long":"-104.36462402","idEspecie":0,"nombreCientifico":"Sin especie","nombreComun":"Sin especie"},{"fecha":"2015-09-29 21:27:14","lat":"20.54399939","long":"-100.60225519","idEspecie":0,"nombreCientifico":"Sin especie","nombreComun":"Sin especie"}]));
				}
				return ($q.reject());
			});

			mockServicioReporteMapa._obtenerEspecies.and.callFake(function(){
				if(exito){
					return ($q.resolve([{"idEspecie":0,"nombreComun":"Sin especie","nombreCientifico":"Sin especie","created_at":"2015-11-07 12:13:42","updated_at":"-0001-11-30 00:00:00","idEstadoEspecie":0,"idEstadoEspecie2":0},{"idEspecie":12,"nombreComun":"Vaca","nombreCientifico":"Vacuno","created_at":"2015-11-07 19:21:51","updated_at":"2015-11-09 14:57:01","idEstadoEspecie":1,"idEstadoEspecie2":1}]));
				}
				return ($q.reject());
			});

			// Controlador
			controladorReporteMapa = $controller('controladorReporteMapa', {
				$scope: $scope,
				ServicioReporteMapa: mockServicioReporteMapa
			});
		});
	});

	it('asigna variables iniciales', function(){
		expect($scope.mostrar_mapa_calor).toBe(false);
		expect($scope.btn_deshabilitado).toBe(true);
		expect($scope.fecha_inicial).toBeDefined();
		expect($scope.fecha_final).toBeDefined();
		expect($scope.fecha_inicial_abierta).toBe(false);
		expect($scope.fecha_final_abierta).toBe(false);
		expect($scope.borrar_ruta_deshabilitado).toBe(true);
		expect($scope.mapa).toBeUndefined();
		expect($scope.ventana_info_marcadores).toBeUndefined();
	});
});