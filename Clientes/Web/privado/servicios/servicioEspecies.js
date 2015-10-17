'use strict';
app.factory('servicioEspecies', ['$http', function($http){

        var fabricaServicioEspecies = {};

        // Obtiene todas las especies
        // Se usará para la búsqueda
        var _obtenerEspecies = function(){
            return $http.get(servicioBase + 'api/especies').then(function(resultado){
                    return resultado.data.especies;
            });
        };

        // Obtiene las especies paginadas, para mostrarlas en la tabla
        var _obtenerEspeciesPaginadas = function(pagina, resultados){
            return $http.get(servicioBase + 'api/especies?pagina=' + pagina + '&resultados=' + resultados).then(function(resultado){
                return {
                    especies: resultado.data.especies.data,
                    total: resultado.data.especies.total,
                    desde: resultado.data.especies.from,
                    hasta: resultado.data.especies.to,
                    ultimaPagina: resultado.data.especies.last_page
                };
            });
        };

        // Obtiene los estados que puede tener una especie
        var _obtenerEstadosEspecies = function(){
            return $http.get(servicioBase + 'api/estadosEspecies').then(function(resultado){
                return resultado.data.estadosEspecies;
            });
        }

        // Elimina una especie
        var _eliminarEspecie = function(id){
            return $http.delete(servicioBase + 'api/especies/' + id).then(function(resultado){
                return resultado.status;
            });
        };

        fabricaServicioEspecies.obtenerEspecies = _obtenerEspecies;
        fabricaServicioEspecies.obtenerEspeciesPaginadas = _obtenerEspeciesPaginadas;
        fabricaServicioEspecies.obtenerEstadosEspecies = _obtenerEstadosEspecies;
        fabricaServicioEspecies.eliminarEspecie = _eliminarEspecie;

        return fabricaServicioEspecies;
}]);