'use strict';
describe('probar servicio de especies', function(){
    var servicioEspecies, $httpBackend;

    beforeEach(module('appPrivada'));

    beforeEach(inject(function(_servicioEspecies_, _$httpBackend_){
        servicioEspecies = _servicioEspecies_;
        $httpBackend = _$httpBackend_;
    }));

    describe('llamada al servidor', function(){
        it('obtiene todas las especies', function(){
            $httpBackend.expectGET(servicioBase + 'api/especies').respond(200, {"error":false,"especies":[{"idEspecie":14,"nombreComun":"nombre comun0","nombreCientifico":"nombre cientifico0","created_at":"2015-09-20 03:22:44","updated_at":"2015-09-20 03:22:44"},{"idEspecie":15,"nombreComun":"nombre comun1","nombreCientifico":"nombre cientifico1","created_at":"2015-09-20 03:22:44","updated_at":"2015-09-20 03:22:44"}]});

            servicioEspecies.obtenerEspecies();
            $httpBackend.flush();
            $httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
        });

        it('obtiene especies paginadas', function(){
            $httpBackend.expectGET(servicioBase + 'api/especies?pagina=1&resultados=10').respond(200, {"error":false,"especies":{"total":11,"per_page":2,"current_page":1,"last_page":6,"from":1,"to":2,"data":[{"idEspecie":0,"nombreComun":"Especie","nombreCientifico":"Especie","created_at":"2015-09-19 00:00:00","updated_at":"2015-09-19 00:00:00"},{"idEspecie":14,"nombreComun":"nombre comun0","nombreCientifico":"nombre cientifico0","created_at":"2015-09-20 03:22:44","updated_at":"2015-09-20 03:22:44"}]}});
            servicioEspecies.obtenerEspeciesPaginadas(1, 10);
            $httpBackend.flush();
            $httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
        });

        it('obtiene los estados que puede tener una especie', function(){
            $httpBackend.expectGET(servicioBase + 'api/estadosEspecies').respond({"error":false,"estadosEspecies":[{"idEstadoEspecie":1,"estado":"Sin Clasificar","created_at":"2015-10-16 00:00:00","updated_at":"2015-10-16 00:00:00"},{"idEstadoEspecie":2,"estado":"Amenazada","created_at":"2015-10-16 00:00:00","updated_at":"2015-10-16 00:00:00"}]});
            servicioEspecies.obtenerEstadosEspecies();
            $httpBackend.flush();
            $httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
        });

        it('elimina una especie', function(){
            $httpBackend.expectDELETE(servicioBase + 'api/especies/4').respond(204);
            servicioEspecies.eliminarEspecie(4);
            $httpBackend.flush();
            $httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
        });

        it('modifica una especie', function(){
            $httpBackend.expectPUT(servicioBase + 'api/especies/4', {'nombreComun': 'nombreComun', 'nombreCientifico': 'nombreCientifico', 'idEstadoEspecie': 1}).respond(200);
            servicioEspecies.modificarEspecie(4, 'nombreComun', 'nombreCientifico', 1);
            $httpBackend.flush();
            $httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
        });

        it('agrega una nueva especie', function(){
            $httpBackend.expectPOST(servicioBase + 'api/especies', {'nombreComun': 'nombreComun', 'nombreCientifico': 'nombreCientifico', 'idEstadoEspecie': 1}).respond(201);
            servicioEspecies.agregarEspecie({'nombreComun': 'nombreComun', 'nombreCientifico': 'nombreCientifico', 'idEstadoEspecie': 1});
            $httpBackend.flush();
            $httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
        });
    });
});