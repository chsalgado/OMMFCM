'use strict';
app.factory('servicioIncidentes', ['$http', function($http){

        var fabricaServicioIncidentes = {};

        // Obtiene incidentes paginados
        // Los incidentes pueden ser filtrados por especie
        var _obtenerIncidentes = function(pagina, resultados, idEspecie){
            if(idEspecie == -1){
                return $http.get(servicioBase + 'api/incidentes?pagina=' + pagina + '&resultados=' + resultados).then(function(resultado){
                    return {
                        incidentes: resultado.data.incidentes.data,
                        total: resultado.data.incidentes.total,
                        desde: resultado.data.incidentes.from,
                        hasta: resultado.data.incidentes.to,
                        ultimaPagina: resultado.data.incidentes.last_page
                    };
                });                
            }else{
                return $http.get(servicioBase + 'api/incidentes?pagina=' + pagina + '&resultados=' + resultados + '&idEspecie=' + idEspecie).then(function(resultado){
                    return {
                        incidentes: resultado.data.incidentes.data,
                        total: resultado.data.incidentes.total,
                        desde: resultado.data.incidentes.from,
                        hasta: resultado.data.incidentes.to,
                        ultimaPagina: resultado.data.incidentes.last_page
                    };
                });                
            }
        };

        // Elimina un incidente
        var _eliminarIncidente = function(id){
            return $http.delete(servicioBase + 'api/incidentes/' + id).then(function(resultado){
                return resultado.status;
            });
        };

        // Modifica la especie de un incidente
        var _modificarIncidente = function(idIncidente, idEspecie, ruta, km){
            return $http.put(servicioBase + 'api/incidentes/' + idIncidente, {'idEspecie': idEspecie, 'ruta': ruta, 'km': km}).then(function(resultado){
                return resultado.status;
            });
        };

        fabricaServicioIncidentes.obtenerIncidentes = _obtenerIncidentes;
        fabricaServicioIncidentes.eliminarIncidente = _eliminarIncidente;
        fabricaServicioIncidentes.modificarIncidente = _modificarIncidente;

        return fabricaServicioIncidentes;
}]);