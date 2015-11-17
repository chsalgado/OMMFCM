'use strict';
describe('controlador reportar', function(){
    var $scope, $timeout, exito;

    var controladorReportar, mockServicioIncidentes;

    var cambiarExito = function(valExito){
        exito = valExito;
    }

    beforeEach(module('appPublica'));

    beforeEach(function(){
        mockServicioIncidentes = jasmine.createSpyObj('servicioIncidentes', ['obtenerEstados', 'obtenerMunicipios', 'agregarIncidente']);

        // Mockea el comportamiento de createElement
        var HTMLElements = {};
        document.getElementById = jasmine.createSpy('HTML Element').and.callFake(function(ID) {
           if(!HTMLElements[ID]) {
              var newElement = document.createElement('div');
              HTMLElements[ID] = newElement;
           }
           return HTMLElements[ID];
        });

        inject(function($rootScope, $controller, $q, _$timeout_){
            $scope = $rootScope.$new();
            $timeout = _$timeout_;

            // Mock del servicio
            mockServicioIncidentes.obtenerEstados.and.callFake(function(){
                if(exito){
                    return ($q.resolve([{"id_estado":1,"estado":"Aguascalientes"},{"id_estado":2,"estado":"Baja California"},{"id_estado":3,"estado":"Baja California Sur"},{"id_estado":4,"estado":"Campeche"},{"id_estado":5,"estado":"Chiapas"},{"id_estado":6,"estado":"Chihuahua"},{"id_estado":7,"estado":"Coahuila"},{"id_estado":8,"estado":"Colima"},{"id_estado":9,"estado":"Distrito Federal"},{"id_estado":10,"estado":"Durango"},{"id_estado":11,"estado":"Estado de M\u00e9xico"},{"id_estado":12,"estado":"Guanajuato"},{"id_estado":13,"estado":"Guerrero"},{"id_estado":14,"estado":"Hidalgo"},{"id_estado":15,"estado":"Jalisco"},{"id_estado":16,"estado":"Michoac\u00e1n"},{"id_estado":17,"estado":"Morelos"},{"id_estado":18,"estado":"Nayarit"},{"id_estado":19,"estado":"Nuevo Le\u00f3n"},{"id_estado":20,"estado":"Oaxaca"},{"id_estado":21,"estado":"Puebla"},{"id_estado":22,"estado":"Quer\u00e9taro"},{"id_estado":23,"estado":"Quintana Roo"},{"id_estado":24,"estado":"San Luis Potos\u00ed"},{"id_estado":25,"estado":"Sinaloa"},{"id_estado":26,"estado":"Sonora"},{"id_estado":27,"estado":"Tabasco"},{"id_estado":28,"estado":"Tamaulipas"},{"id_estado":29,"estado":"Tlaxcala"},{"id_estado":30,"estado":"Veracruz"},{"id_estado":31,"estado":"Yucat\u00e1n"},{"id_estado":32,"estado":"Zacatecas"}]));
                }
                return ($q.reject());
            });

            mockServicioIncidentes.obtenerMunicipios.and.callFake(function(){
                if(exito){
                    return ($q.resolve([{"id_municipio":1,"nombre_municipio":"Aguascalientes","estado":1},{"id_municipio":2,"nombre_municipio":"Asientos","estado":1},{"id_municipio":3,"nombre_municipio":"Calvillo","estado":1},{"id_municipio":4,"nombre_municipio":"Cos\u00edo","estado":1},{"id_municipio":5,"nombre_municipio":"Jes\u00fas Maria","estado":1},{"id_municipio":6,"nombre_municipio":"Pabell\u00f3n De Arteaga","estado":1},{"id_municipio":7,"nombre_municipio":"Rinc\u00f3n De Romos","estado":1},{"id_municipio":8,"nombre_municipio":"San Jos\u00e9 De Gracia","estado":1},{"id_municipio":9,"nombre_municipio":"Tepezal\u00e1","estado":1},{"id_municipio":10,"nombre_municipio":"San Francisco De Los Romo","estado":1},{"id_municipio":11,"nombre_municipio":"El Llano","estado":1}]));
                }
                return ($q.reject());
            });

            mockServicioIncidentes.agregarIncidente.and.callFake(function(){
                if(exito){
                    return ($q.resolve());
                }
                return ($q.reject());
            });

            // Controlador
            controladorReportar = $controller('controladorReportar', {
                $scope: $scope,
                servicioIncidentes: mockServicioIncidentes
            });
        });
    });

    it('asigna variables iniciales', function(){
        expect($scope.foto).toMatch('');
        expect($scope.incidente).toEqual({"fecha":'',"long":0,"lat":0,"imagen":'',"extension":''});
        expect($scope.ruta).toEqual({"origen":{"idEstado":1,"nombreMunicipio":''},"destino":{"idEstado":1,"nombreMunicipio":''}});
        expect($scope.map).toBeUndefined();
        expect($scope.toRender).toBeDefined();
        expect($scope.marker).toBeUndefined();
        expect($scope.mensaje).toMatch('');
        expect($scope.exito).toBe(false);
        expect($scope.errores).toBe(false);
        expect($scope.estados).toBeUndefined();
        expect($scope.municipiosOrigen).toBeUndefined();
        expect($scope.municipiosDestino).toBeUndefined();
    });

    it('obtiene los estados', function(){
        cambiarExito(true);
        $scope.obtenerEstados();
        expect(mockServicioIncidentes.obtenerEstados).toHaveBeenCalled();
        $timeout.flush();
        expect($scope.estados).toEqual([{"id_estado":1,"estado":"Aguascalientes"},{"id_estado":2,"estado":"Baja California"},{"id_estado":3,"estado":"Baja California Sur"},{"id_estado":4,"estado":"Campeche"},{"id_estado":5,"estado":"Chiapas"},{"id_estado":6,"estado":"Chihuahua"},{"id_estado":7,"estado":"Coahuila"},{"id_estado":8,"estado":"Colima"},{"id_estado":9,"estado":"Distrito Federal"},{"id_estado":10,"estado":"Durango"},{"id_estado":11,"estado":"Estado de M\u00e9xico"},{"id_estado":12,"estado":"Guanajuato"},{"id_estado":13,"estado":"Guerrero"},{"id_estado":14,"estado":"Hidalgo"},{"id_estado":15,"estado":"Jalisco"},{"id_estado":16,"estado":"Michoac\u00e1n"},{"id_estado":17,"estado":"Morelos"},{"id_estado":18,"estado":"Nayarit"},{"id_estado":19,"estado":"Nuevo Le\u00f3n"},{"id_estado":20,"estado":"Oaxaca"},{"id_estado":21,"estado":"Puebla"},{"id_estado":22,"estado":"Quer\u00e9taro"},{"id_estado":23,"estado":"Quintana Roo"},{"id_estado":24,"estado":"San Luis Potos\u00ed"},{"id_estado":25,"estado":"Sinaloa"},{"id_estado":26,"estado":"Sonora"},{"id_estado":27,"estado":"Tabasco"},{"id_estado":28,"estado":"Tamaulipas"},{"id_estado":29,"estado":"Tlaxcala"},{"id_estado":30,"estado":"Veracruz"},{"id_estado":31,"estado":"Yucat\u00e1n"},{"id_estado":32,"estado":"Zacatecas"}]);
    });

    it('falla al obtener los estados', function(){
        cambiarExito(false);
        $scope.obtenerEstados();
        expect(mockServicioIncidentes.obtenerEstados).toHaveBeenCalled();
        $timeout.flush();
        expect($scope.estados).toBeUndefined();
    });

    it('obtiene los municipios del estado origen', function(){
        cambiarExito(true);
        $scope.obtenerMunicipiosOrigen();
        expect(mockServicioIncidentes.obtenerMunicipios).toHaveBeenCalledWith(1);
        $timeout.flush();
        expect($scope.municipiosOrigen).toEqual([{"id_municipio":1,"nombre_municipio":"Aguascalientes","estado":1},{"id_municipio":2,"nombre_municipio":"Asientos","estado":1},{"id_municipio":3,"nombre_municipio":"Calvillo","estado":1},{"id_municipio":4,"nombre_municipio":"Cos\u00edo","estado":1},{"id_municipio":5,"nombre_municipio":"Jes\u00fas Maria","estado":1},{"id_municipio":6,"nombre_municipio":"Pabell\u00f3n De Arteaga","estado":1},{"id_municipio":7,"nombre_municipio":"Rinc\u00f3n De Romos","estado":1},{"id_municipio":8,"nombre_municipio":"San Jos\u00e9 De Gracia","estado":1},{"id_municipio":9,"nombre_municipio":"Tepezal\u00e1","estado":1},{"id_municipio":10,"nombre_municipio":"San Francisco De Los Romo","estado":1},{"id_municipio":11,"nombre_municipio":"El Llano","estado":1}]);
        expect($scope.ruta.origen.nombreMunicipio).toMatch('Aguascalientes');
    });

    it('falla al obtener los municipios del estado origen', function(){
        cambiarExito(false);
        $scope.obtenerMunicipiosOrigen();
        expect(mockServicioIncidentes.obtenerMunicipios).toHaveBeenCalledWith(1);
        $timeout.flush();
        expect($scope.municipiosOrigen).toBeUndefined();
        expect($scope.ruta.origen.nombreMunicipio).toMatch('');
    });

    it('obtiene los municipios del estado destino', function(){
        cambiarExito(true);
        $scope.obtenerMunicipiosDestino();
        expect(mockServicioIncidentes.obtenerMunicipios).toHaveBeenCalledWith(1);
        $timeout.flush();
        expect($scope.municipiosDestino).toEqual([{"id_municipio":1,"nombre_municipio":"Aguascalientes","estado":1},{"id_municipio":2,"nombre_municipio":"Asientos","estado":1},{"id_municipio":3,"nombre_municipio":"Calvillo","estado":1},{"id_municipio":4,"nombre_municipio":"Cos\u00edo","estado":1},{"id_municipio":5,"nombre_municipio":"Jes\u00fas Maria","estado":1},{"id_municipio":6,"nombre_municipio":"Pabell\u00f3n De Arteaga","estado":1},{"id_municipio":7,"nombre_municipio":"Rinc\u00f3n De Romos","estado":1},{"id_municipio":8,"nombre_municipio":"San Jos\u00e9 De Gracia","estado":1},{"id_municipio":9,"nombre_municipio":"Tepezal\u00e1","estado":1},{"id_municipio":10,"nombre_municipio":"San Francisco De Los Romo","estado":1},{"id_municipio":11,"nombre_municipio":"El Llano","estado":1}]);
        expect($scope.ruta.destino.nombreMunicipio).toMatch('Aguascalientes');
    });

    it('falla al obtener los municipios del estado destino', function(){
        cambiarExito(false);
        $scope.obtenerMunicipiosDestino();
        expect(mockServicioIncidentes.obtenerMunicipios).toHaveBeenCalledWith(1);
        $timeout.flush();
        expect($scope.municipiosOrigen).toBeUndefined();
        expect($scope.ruta.origen.nombreMunicipio).toMatch('');
    });

    it('valida la forma', function(){
        $scope.validarForma();
        expect($scope.mensaje).toMatch('Selecciona una imagen');

        $scope.foto = 'data:image/jpeg;base64,/9j/AAA==';
        $scope.validarForma();
        expect($scope.mensaje).toMatch('Selecciona la ubicación de incidente');

        $scope.fecha = new Date('3000-12-31 00:00:00');
        $scope.validarForma();
        expect($scope.mensaje).toMatch('La fecha no puede ser posterior al día de hoy');
    });

    it('registra un incidente', function(){
        cambiarExito(true);
        $scope.foto = 'data:image/jpeg;base64,/9j/AAA==';
        $scope.fecha = new Date('2015-01-01 00:00:00');
        $scope.marker = new google.maps.Marker({position: {lat: 23, lng: -102}});
        $scope.map = new google.maps.Map(document.getElementById("map-canvas"));

        $scope.validarForma();
        expect($scope.incidente.imagen).toMatch('/9j/AAA==');
        expect($scope.incidente.extension).toMatch('.jpeg');
        expect($scope.incidente.fecha).toEqual('2015-01-01 00:00:00');
        expect($scope.incidente.lat).toEqual(23);
        expect($scope.incidente.long).toEqual(-102);
        expect(mockServicioIncidentes.agregarIncidente).toHaveBeenCalledWith({"fecha":'2015-01-01 00:00:00',"long":-102,"lat":23,"imagen":'/9j/AAA==',"extension":'.jpeg'});

        $timeout.flush();
        expect($scope.mensaje).toMatch('El incidente ha sido registrado. Gracias por tu participación');
    });

    it('falla al registrar un incidente', function(){
        cambiarExito(false);
        $scope.foto = 'data:image/jpeg;base64,/9j/AAA==';
        $scope.fecha = new Date('2015-01-01 00:00:00');
        $scope.marker = new google.maps.Marker({position: {lat: 23, lng: -102}});
        $scope.map = new google.maps.Map(document.getElementById("map-canvas"));

        $scope.validarForma();
        expect($scope.incidente.imagen).toMatch('/9j/AAA==');
        expect($scope.incidente.extension).toMatch('.jpeg');
        expect($scope.incidente.fecha).toEqual('2015-01-01 00:00:00');
        expect($scope.incidente.lat).toEqual(23);
        expect($scope.incidente.long).toEqual(-102);
        expect(mockServicioIncidentes.agregarIncidente).toHaveBeenCalledWith({"fecha":'2015-01-01 00:00:00',"long":-102,"lat":23,"imagen":'/9j/AAA==',"extension":'.jpeg'});

        $timeout.flush();
        expect($scope.mensaje).toMatch('El incidente no pudo ser registrado. Por favor inténtalo más tarde');
    });
});