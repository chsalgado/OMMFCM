'use strict';
describe('servicio de incidentes', function(){
    var servicioIncidentes, $httpBackend;

    beforeEach(module('appPublica'));

    beforeEach(inject(function(_servicioIncidentes_, _$httpBackend_){
        servicioIncidentes = _servicioIncidentes_;
        $httpBackend = _$httpBackend_;
    }));

    describe('llamada al servidor', function(){
        it('obtiene los estados', function(){
            $httpBackend.expectGET(servicioBase + 'api/estados').respond(200,{"error":false,"estados":[{"id_estado":1,"estado":"Aguascalientes"},{"id_estado":2,"estado":"Baja California"},{"id_estado":3,"estado":"Baja California Sur"},{"id_estado":4,"estado":"Campeche"},{"id_estado":5,"estado":"Chiapas"},{"id_estado":6,"estado":"Chihuahua"},{"id_estado":7,"estado":"Coahuila"},{"id_estado":8,"estado":"Colima"},{"id_estado":9,"estado":"Distrito Federal"},{"id_estado":10,"estado":"Durango"},{"id_estado":11,"estado":"Estado de M\u00e9xico"},{"id_estado":12,"estado":"Guanajuato"},{"id_estado":13,"estado":"Guerrero"},{"id_estado":14,"estado":"Hidalgo"},{"id_estado":15,"estado":"Jalisco"},{"id_estado":16,"estado":"Michoac\u00e1n"},{"id_estado":17,"estado":"Morelos"},{"id_estado":18,"estado":"Nayarit"},{"id_estado":19,"estado":"Nuevo Le\u00f3n"},{"id_estado":20,"estado":"Oaxaca"},{"id_estado":21,"estado":"Puebla"},{"id_estado":22,"estado":"Quer\u00e9taro"},{"id_estado":23,"estado":"Quintana Roo"},{"id_estado":24,"estado":"San Luis Potos\u00ed"},{"id_estado":25,"estado":"Sinaloa"},{"id_estado":26,"estado":"Sonora"},{"id_estado":27,"estado":"Tabasco"},{"id_estado":28,"estado":"Tamaulipas"},{"id_estado":29,"estado":"Tlaxcala"},{"id_estado":30,"estado":"Veracruz"},{"id_estado":31,"estado":"Yucat\u00e1n"},{"id_estado":32,"estado":"Zacatecas"}]});
            servicioIncidentes.obtenerEstados();
            $httpBackend.flush();
            $httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
        });

        it('obtiene los municipios', function(){
            $httpBackend.expectGET(servicioBase + 'api/municipios?estado=9').respond(200, {"error":false,"municipios":[{"id_municipio":2578,"nombre_municipio":"Distrito Federal","estado":9}]});
            servicioIncidentes.obtenerMunicipios(9);
            $httpBackend.flush();
            $httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
        });

        it('agrega un incidente', function(){
            var incidente = {"fecha":'2015-11-14 11:11:11',"long":-102.537750,"lat":23.045963,"imagen":'/9j/AAA=',"extension":'.jpeg'};
            $httpBackend.expectPOST(servicioBase + 'api/incidentes', incidente).respond(200);
            servicioIncidentes.agregarIncidente(incidente);
            $httpBackend.flush();
            $httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
        });
    });
});