'use strict';
app.controller('controladorIncidentes', ['$scope', '$timeout', 'servicioIncidentes', 'servicioEspecies', function($scope, $timeout, servicioIncidentes, servicioEspecies){

    // Lista de incidentes
    $scope.incidentes = [];

    // Variables de paginación
    $scope.paginaActual = 1;
    $scope.resultadosDisponibles = [10, 20, 30, 40, 50];
    $scope.resultados = 10;
    $scope.especieSeleccionada = -1;

    // Retroalimentación al usuario
    $scope.mensaje = '';
    $scope.exito = false;
    $scope.errores = false;

    // Oculta la retroalimentacion al usuario
    $scope.ocultarMensaje = function(){
        $scope.exito = false;
        $scope.errores = false;
    }

    // Obtiene todas las especies
    // Las especies llenan los selects
    $scope.obtenerEspecies = function(){
        servicioEspecies.obtenerEspecies().then(function(resultados){
            $scope.especies = resultados;
            $scope.especiesFiltro = [{"idEspecie": -1, "nombreComun": "- - Todas especies", "nombreCientifico": "-", "created_at":"2015-09-19 00:00:00","updated_at":"2015-09-19 00:00:00"}];
            $scope.especiesFiltro.push.apply($scope.especiesFiltro, resultados); 
        });        
    }

    // Obtiene los incidentes para llenar la tabla
    // Se llama cuando:
    //      Se carga la pagina
    //      Se cambia de pagina
    //      Se uiliza el filtro por especie
    //      Se selecciona numero de resultados a mostrar
    //      Se elimina o modifica un incidente
    $scope.actualizarPagina = function(pagina){
        $scope.paginaActual = pagina;
        $scope.avanzar = true;
        $scope.regresar = true;

        servicioIncidentes.obtenerIncidentes($scope.paginaActual, $scope.resultados, $scope.especieSeleccionada).then(function(resultado){
            $scope.incidentes = resultado.incidentes;
            $scope.total = resultado.total;
            $scope.desde = resultado.desde;
            $scope.hasta = resultado.hasta;
            $scope.ultimaPagina = resultado.ultimaPagina;

            // Deshabilitar botones de avanzar y regresar
            if(pagina == 1){
                $scope.regresar = false;
            }
            if(pagina == $scope.ultimaPagina){
                $scope.avanzar = false;
            }
        });
    }

    // Elimina un incidente
    $scope.eliminarIncidente = function(id){
        if(confirm('¿Estás seguro que quieres eliminar este incidente?')){
            servicioIncidentes.eliminarIncidente(id).then(function(resultado){
                $scope.actualizarPagina($scope.paginaActual);
                $scope.mensaje = 'El incidente ha sido eliminado';
                $scope.exito = true;
            }, function(resultado){
                $scope.mensaje = 'El incidente no fue eliminado. Intentelo más tarde';
                $scope.errores = true;
            });
            $timeout($scope.ocultarMensaje, 3000);
        }
    }

    // Modifica la especie de un incidente
    $scope.modificarIncidente = function(idIncidente, idEspecie){
        servicioIncidentes.modificarIncidente(idIncidente, idEspecie).then(function(resultado){
            $scope.actualizarPagina($scope.paginaActual);
            $scope.mensaje = 'El incidente ha sido modificado';
            $scope.exito = true;
        }, function(resultado){
            $scope.mensaje = 'El incidente no fue modificado. Intentelo más tarde';
            $scope.errores = true;
        });
        $timeout($scope.ocultarMensaje, 3000);
    }
}]);