'use strict';
describe('probar servicio de incidentes', function(){
    var servicioIncidentes, $httpBackend;

    beforeEach(module('appPrivada'));

    beforeEach(inject(function(_servicioIncidentes_, _$httpBackend_){
        servicioIncidentes = _servicioIncidentes_;
        $httpBackend = _$httpBackend_;
    }));

    describe('llamada al servidor', function(){
        it('obtiene incidentes paginados', function(){
            $httpBackend.expectGET(servicioBase + 'api/incidentes?pagina=1&resultados=10').respond(200,{"error":false,"incidentes":{"total":2,"per_page":10,"current_page":1,"last_page":1,"from":1,"to":2,"data":[{"idIncidente":2,"idEspecie":0,"fecha":null,"rutaFoto":"\/Users\/jorgegonzac\/Documents\/bloque\/OMMFCM\/Api\/OMMFCM\/public\/imagenes\/incidentes\/incidente_1442720057.png","long":null,"lat":null,"mpioOrigen":0,"mpioDestino":0,"km":null,"created_at":"2015-09-20 03:34:17","updated_at":"2015-09-20 03:34:17","rutaThumbnail":"\/Users\/jorgegonzac\/Documents\/bloque\/OMMFCM\/Api\/OMMFCM\/public\/imagenes\/incidentes\/incidente_1442720057_thumbnail.png"},{"idIncidente":3,"idEspecie":14,"fecha":null,"rutaFoto":"\/Users\/jorgegonzac\/Documents\/bloque\/OMMFCM\/Api\/OMMFCM\/public\/imagenes\/incidentes\/incidente_1442720073.png","long":null,"lat":null,"mpioOrigen":0,"mpioDestino":0,"km":null,"created_at":"2015-09-20 03:34:33","updated_at":"2015-09-20 03:36:24","rutaThumbnail":"\/Users\/jorgegonzac\/Documents\/bloque\/OMMFCM\/Api\/OMMFCM\/public\/imagenes\/incidentes\/incidente_1442720073_thumbnail.png"}]}});
            servicioIncidentes.obtenerIncidentes(1, 10);
            $httpBackend.flush();
            $httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
        });

        it('elimina un incidente', function(){
            $httpBackend.expectDELETE(servicioBase + 'api/incidentes/15').respond(204);
            servicioIncidentes.eliminarIncidente(15);
            $httpBackend.flush();
            $httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
        });

        it('modifica un incidente', function(){
            $httpBackend.expectPUT(servicioBase + 'api/incidentes/15', {'idEspecie': 2}).respond(204);
            servicioIncidentes.modificarIncidente(15, 2);
            $httpBackend.flush();
            $httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
        });
    });
});