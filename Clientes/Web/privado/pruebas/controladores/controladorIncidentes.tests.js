'use strict';
describe('controlador incidentes', function(){
    var scope;

    beforeEach(angular.mock.module('appPrivada'));
    beforeEach(angular.mock.inject(function($rootScope, $controller){

        scope = $rootScope.$new();
        $controller('controladorIncidentes', {
            $scope: scope
        });

    }));

    it('prueba variables iniciales', function(){
        expect(scope.incidentes.length).toBe(0);
        expect(scope.paginaActual).toEqual(1);
        expect(scope.resultadosDisponibles.length).toBe(5);
        expect(scope.resultados).toEqual(10);
        expect(scope.mensaje).toMatch('');
        expect(scope.exito).toBe(false);
        expect(scope.errores).toBe(false);
        expect(scope.avanzar).toBeUndefined();
        expect(scope.regresar).toBeUndefined();
        expect(scope.total).toBeUndefined();
        expect(scope.desde).toBeUndefined();
        expect(scope.hasta).toBeUndefined();
        expect(scope.ultimaPagina).toBeUndefined();
    });

    it('prueba actualizar pagina', function(){
        scope.actualizarPagina(1);
        expect(scope.paginaActual).toEqual(1);
        expect(scope.avanzar).toBe(true);
        expect(scope.regresar).toBe(true);
    });
});