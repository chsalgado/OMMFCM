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
    });
});