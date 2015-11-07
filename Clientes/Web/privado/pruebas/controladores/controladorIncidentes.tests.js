'use strict';
describe('controlador incidentes', function(){
    var $scope, $timeout, exito;

    var controladorIncidentes, mockServicioIncidentes, mockServicioEspecies;

    var cambiarExito = function(valExito){
        exito = valExito;
    }

    beforeEach(module('appPrivada'));

    beforeEach(function(){

        // Mockea el confirm de eliminarIncidente
        spyOn(window, 'confirm').and.returnValue(true);

        mockServicioEspecies = jasmine.createSpyObj('servicioEspecies', ['obtenerEspecies', 'obtenerEstadosEspecies', 'agregarEspecie']);
        mockServicioIncidentes = jasmine.createSpyObj('servicioIncidentes', ['obtenerIncidentes', 'eliminarIncidente' ,'modificarIncidente']);

        inject(function($rootScope, $controller, $q, _$timeout_){
            $scope = $rootScope.$new();
            $timeout = _$timeout_;

            // Mock de los servicios
            mockServicioEspecies.obtenerEspecies.and.callFake(function(){
                if(exito){
                    return ($q.resolve([
                        {"idEspecie":14,"nombreComun":"nombre comun0","nombreCientifico":"nombre cientifico0","created_at":"2015-09-20 03:22:44","updated_at":"2015-09-20 03:22:44","idEstadoEspecie":1,"idEstadoEspecie2":1},
                        {"idEspecie":15,"nombreComun":"nombre comun1","nombreCientifico":"nombre cientifico1","created_at":"2015-09-20 03:22:44","updated_at":"2015-09-20 03:22:44","idEstadoEspecie":1,"idEstadoEspecie2":1}
                    ]));
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

            mockServicioEspecies.agregarEspecie.and.callFake(function(){
                if(exito){
                    return ($q.resolve());
                }
                return ($q.reject());
            });

            mockServicioIncidentes.obtenerIncidentes.and.callFake(function(){
                if(exito){
                    return ($q.resolve({
                        "incidentes": 
                            [{"idIncidente":26,"idEspecie":15,"fecha":null,"rutaFoto":"as","long":null,"lat":null,"mpioOrigen":3,"mpioDestino":4,"km":null,"created_at":"-0001-11-30 00:00:00","updated_at":"-0001-11-30 00:00:00","rutaThumbnail":"x"},
                            {"idIncidente":27,"idEspecie":15,"fecha":null,"rutaFoto":"as","long":null,"lat":null,"mpioOrigen":3,"mpioDestino":4,"km":null,"created_at":"-0001-11-30 00:00:00","updated_at":"-0001-11-30 00:00:00","rutaThumbnail":"dd"}],
                        "total": 2,
                        "desde": 1,
                        "hasta": 2,
                        "ultimaPagina": 1
                    }));
                }
                return ($q.reject());
            });

            mockServicioIncidentes.eliminarIncidente.and.callFake(function(){
                if(exito){
                    return ($q.resolve());
                }
                return ($q.reject()); 
            });

            mockServicioIncidentes.modificarIncidente.and.callFake(function(){
                if(exito){
                    return ($q.resolve());
                }
                return ($q.reject());
            });

            // Controlador
            controladorIncidentes = $controller('controladorIncidentes',{
                $scope: $scope,
                servicioIncidentes: mockServicioIncidentes,
                servicioEspecies: mockServicioEspecies
            });
        });
    });

    it('asigna variables iniciales', function(){
        expect($scope.incidentes.length).toBe(0);
        expect($scope.nuevaEspecie).toEqual({"nombreComun":null,"nombreCientifico":null,"idEstadoEspecie":1, "idEstadoEspecie2":1});
        expect($scope.paginaActual).toEqual(1);
        expect($scope.resultadosDisponibles.length).toBe(5);
        expect($scope.resultados).toEqual(10);
        expect($scope.especieSeleccionada).toEqual(-1);
        expect($scope.mensaje).toMatch('');
        expect($scope.exito).toBe(false);
        expect($scope.errores).toBe(false);
        expect($scope.editandoIn).toBe(false);
        expect($scope.ruta).toEqual('http://watch.imt.mx/public_html/imagenes');
        expect($scope.rutaFoto).toBeUndefined();
        expect($scope.estados).toBeUndefined();
        expect($scope.estados2).toBeUndefined();
        expect($scope.especies).toBeUndefined();
        expect($scope.especiesFiltro).toBeUndefined();
        expect($scope.nombreEspecie).toBeUndefined();
        expect($scope.editando).toBeUndefined();
        expect($scope.avanzar).toBeUndefined();
        expect($scope.regresar).toBeUndefined();
        expect($scope.total).toBeUndefined();
        expect($scope.desde).toBeUndefined();
        expect($scope.hasta).toBeUndefined();
        expect($scope.ultimaPagina).toBeUndefined();
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

    it('obtiene todas las especies', function(){
        cambiarExito(true);
        var pagina = 1;
        $scope.obtenerEspecies(pagina);
        expect(mockServicioEspecies.obtenerEspecies).toHaveBeenCalledWith();
        $timeout.flush();
        expect($scope.especies).toEqual([{"idEspecie":14,"nombreComun":"nombre comun0","nombreCientifico":"nombre cientifico0","created_at":"2015-09-20 03:22:44","updated_at":"2015-09-20 03:22:44","idEstadoEspecie":1,"idEstadoEspecie2":1},{"idEspecie":15,"nombreComun":"nombre comun1","nombreCientifico":"nombre cientifico1","created_at":"2015-09-20 03:22:44","updated_at":"2015-09-20 03:22:44","idEstadoEspecie":1,"idEstadoEspecie2":1}]);
        expect($scope.especiesFiltro).toEqual([{"idEspecie": -1, "nombreComun": "- - Todas las especies", "nombreCientifico": "-", "created_at":"2015-09-19 00:00:00","updated_at":"2015-09-19 00:00:00"},{"idEspecie":14,"nombreComun":"nombre comun0","nombreCientifico":"nombre cientifico0","created_at":"2015-09-20 03:22:44","updated_at":"2015-09-20 03:22:44","idEstadoEspecie":1,"idEstadoEspecie2":1},{"idEspecie":15,"nombreComun":"nombre comun1","nombreCientifico":"nombre cientifico1","created_at":"2015-09-20 03:22:44","updated_at":"2015-09-20 03:22:44","idEstadoEspecie":1,"idEstadoEspecie2":1}]);
    });

    it('falla al obtener todas las especies', function(){
        cambiarExito(false);
        var pagina = 1;
        $scope.obtenerEspecies(pagina);
        expect(mockServicioEspecies.obtenerEspecies).toHaveBeenCalled();
        $timeout.flush();
        expect($scope.especies).toBeUndefined();
        expect($scope.especiesFiltro).toBeUndefined();
    });

    it('actualiza la pagina', function(){
        cambiarExito(true);
        var pagina = 1;
        $scope.obtenerEspecies(pagina);
        $timeout.flush();
        expect(mockServicioIncidentes.obtenerIncidentes).toHaveBeenCalledWith(1, 10, -1);
        expect($scope.incidentes).toEqual([{"idIncidente":26,"idEspecie":15,"fecha":null,"rutaFoto":"as","long":null,"lat":null,"mpioOrigen":3,"mpioDestino":4,"km":null,"created_at":"-0001-11-30 00:00:00","updated_at":"-0001-11-30 00:00:00","rutaThumbnail":"x"},{"idIncidente":27,"idEspecie":15,"fecha":null,"rutaFoto":"as","long":null,"lat":null,"mpioOrigen":3,"mpioDestino":4,"km":null,"created_at":"-0001-11-30 00:00:00","updated_at":"-0001-11-30 00:00:00","rutaThumbnail":"dd"}]);
        expect($scope.total).toEqual(2);
        expect($scope.desde).toEqual(1);
        expect($scope.hasta).toEqual(2);
        expect($scope.ultimaPagina).toEqual(1);
        expect($scope.regresar).toBe(false);
        expect($scope.avanzar).toBe(false);
        expect($scope.editando).toEqual([false,false]);
        expect($scope.nombreEspecie).toEqual(['nombre comun1 - nombre cientifico1', 'nombre comun1 - nombre cientifico1'])
    });

    it('muestra mensaje de exito cuando se elimina un incidente', function(){
        cambiarExito(true);
        $scope.obtenerEspecies();
        var idIncidente = 2;
        $scope.eliminarIncidente(idIncidente);
        expect(mockServicioIncidentes.eliminarIncidente).toHaveBeenCalledWith(idIncidente);
        $timeout.flush();
        expect($scope.mensaje).toBe('El incidente ha sido eliminado');
    });

    it('muestra mensaje de error cuando no se puede eliminar un incidente', function(){
        cambiarExito(false);
        var idIncidente = 2;
        $scope.eliminarIncidente(idIncidente);
        expect(mockServicioIncidentes.eliminarIncidente).toHaveBeenCalledWith(idIncidente);
        $timeout.flush();
        expect($scope.mensaje).toBe('El incidente no fue eliminado. Intentelo más tarde');
    });

    it('muestra mensaje de éxito cuando se modifica un incidente', function(){
        cambiarExito(true);
        // Se llama para definir editando
        $scope.obtenerEspecies();
        $timeout.flush();

        var index = 1;
        var idIncidente = 15;
        var idEspecie = 2;
        var ruta = '45D';
        var km = 20;
        $scope.modificarIncidente(index, idIncidente, idEspecie, ruta, km);
        expect($scope.editando).toEqual([false, false]);
        expect($scope.editandoIn).toBe(false);
        expect(mockServicioIncidentes.modificarIncidente).toHaveBeenCalledWith(idIncidente, idEspecie, ruta, km);
        $timeout.flush();
        expect($scope.mensaje).toBe('El incidente ha sido modificado');
    });

    it('muestra mensaje de error cuando no se puede modificar un incidente', function(){
        cambiarExito(true);
        // Se llama para definir editando
        $scope.obtenerEspecies();
        $timeout.flush();
        
        cambiarExito(false);
        var index = 1;
        var idIncidente = 15;
        var idEspecie = 2;
        var ruta = '45D';
        var km = 20;
        $scope.modificarIncidente(index, idIncidente, idEspecie, ruta, km);
        expect($scope.editando).toEqual([false, false]);
        expect($scope.editandoIn).toBe(false);
        expect(mockServicioIncidentes.modificarIncidente).toHaveBeenCalledWith(idIncidente, idEspecie, ruta, km);
        $timeout.flush();
        expect($scope.mensaje).toBe('El incidente no fue modificado. Intentelo más tarde');
    });

    it('muestra mensaje de exito cuando se agrega una especie', function(){
        cambiarExito(true);
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
