'use strict';
describe('servicio de reporte', function(){
	var ServicioReporteMapa, $httpBackend;

	beforeEach(module('appPrivada'));

	beforeEach(inject(function(_ServicioReporteMapa_, _$httpBackend_){
		ServicioReporteMapa = _ServicioReporteMapa_;
		$httpBackend = _$httpBackend_;
	}));

	describe('llamada al servidor', function(){
		it('obtiene incidentes', function(){
			$httpBackend.expectGET(servicioBase + 'api/incidentes?reporte=2').respond(200, {"error":false,"incidentes":[{"fecha":"2015-03-12 19:18:06","lat":"21.32008096","long":"-104.36462402","idEspecie":0,"nombreCientifico":"Sin especie","nombreComun":"Sin especie"},{"fecha":"2015-09-29 21:27:14","lat":"20.54399939","long":"-100.60225519","idEspecie":0,"nombreCientifico":"Sin especie","nombreComun":"Sin especie"}]});

			ServicioReporteMapa._obtenerIncidentes();
			$httpBackend.flush();
			$httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
		});

		it('obtiene especies', function(){
			$httpBackend.expectGET(servicioBase + 'api/especies').respond(200, {"error":false,"especies":[{"idEspecie":0,"nombreComun":"Sin especie","nombreCientifico":"Sin especie","created_at":"2015-11-07 12:13:42","updated_at":"-0001-11-30 00:00:00","idEstadoEspecie":0,"idEstadoEspecie2":0},{"idEspecie":12,"nombreComun":"Vaca","nombreCientifico":"Vacuno","created_at":"2015-11-07 19:21:51","updated_at":"2015-11-09 14:57:01","idEstadoEspecie":1,"idEstadoEspecie2":1}]});
			
			ServicioReporteMapa._obtenerEspecies();
			$httpBackend.flush();
			$httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
		});
	});
});