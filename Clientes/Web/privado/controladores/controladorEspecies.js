'use strict';
app.controller('controladorEspecies', ['$scope', '$timeout', '$filter', 'servicioEspecies', function($scope, $timeout, $filter, servicioEspecies){

    // Lista de especies
    $scope.especies = [];

    // Objeto utilizado para agregar una especie
    $scope.nuevaEspecie = {
        "nombreComun":null,
        "nombreCientifico":null,
        "idEstadoEspecie":1
    };

    // Variables de paginacion
    $scope.paginaActual = 1;
    $scope.resultadosDisponibles = [10, 20, 30, 40, 50];
    $scope.resultados = 10;

    // Retroalimentación al usuario
    $scope.mensaje = '';
    $scope.exito = false;
    $scope.errores = false;

    // Variable que permite que se edite un incidente a la vez 
    $scope.editandoEs = false;

    // Oculta la retroalimentacion al usuario
    $scope.ocultarMensaje = function(){
        $scope.exito = false;
        $scope.errores = false;
    }

    // Regresa nueva especie a sus valores originales
    // Utilizada para limpiar la forma de agregar especie
    $scope.limpiarEspecie = function(){
        $scope.nuevaEspecie.nombreComun = null;
        $scope.nuevaEspecie.nombreCientifico = null;
        $scope.nuevaEspecie.idEstadoEspecie = 1;
    }

    // Obtiene los estados que puede tener una especie
    $scope.obtenerEstadosEspecies = function(){
        servicioEspecies.obtenerEstadosEspecies().then(function(resultado){
            $scope.estados = resultado;
            $scope.actualizarPagina(1);
        });
    }

    // Obtiene los incidentes para llenar la tabla
    // Se llama cuando:
    //      Se carga la pagina
    //      Se cambia de pagina
    //      Se selecciona numero de resultados a mostrar
    //      Se elimina o modifica una especie
    $scope.actualizarPagina = function(pagina){
        // Arreglo que guarda el nombre del estado de cada especie
        $scope.nombreEstado = [];

        $scope.editando = [];
        $scope.paginaActual = pagina;
        $scope.avanzar = true;
        $scope.regresar = true;

        servicioEspecies.obtenerEspeciesPaginadas($scope.paginaActual, $scope.resultados).then(function(resultado){
            $scope.especies = resultado.especies;
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

            angular.forEach($scope.especies, function(esp){
                // Arreglo que permite saber que especie se está modificando
                $scope.editando.push.apply($scope.editando, [false]);
                $scope.nombreEstadoEspecie(esp.idEstadoEspecie);
            });

        });
    }

    // Obtiene el nombre del estado de una especie
    $scope.nombreEstadoEspecie = function(idEstadoEspecie){
        // Encuentra el estado con el id recibido
        $scope.nEstado = $filter('filter')($scope.estados, function(resultado){
            return resultado.idEstadoEspecie === idEstadoEspecie;
        })[0];

        // Agrega el nombre al arreglo para mostrarlo en la tabla
        $scope.nombreEstado.push.apply($scope.nombreEstado, [$scope.nEstado.estado]);
    }

    // Elimina una especie
    $scope.eliminarEspecie = function(id){
        if(confirm('¿Estás seguro que quieres eliminar esta especie?')){
            servicioEspecies.eliminarEspecie(id).then(function(resultado){
                $scope.actualizarPagina($scope.paginaActual);
                $scope.mensaje = 'La especie ha sido eliminada';
                $scope.exito = true;
            }, function(resultado){
                $scope.errores = true;
                if(resultado.status == 412){
                    $scope.mensaje = 'La especie no puede ser eliminada porque hay incidentes asociados a ella';
                }else{
                    $scope.mensaje = 'La especie no fue eliminada. Intentelo más tarde';
                }
            });
            $timeout($scope.ocultarMensaje, 3000);
        }
    }

    // Muestra los campos que pueden ser modificados en una especie
    $scope.editables = function(index){
        $scope.editando[index] = true;
        $scope.editandoEs = true;
    }

    // Modifica una especie
    $scope.modificarEspecie = function (index, idEspecie, nComun, nCientifico, idEstado){
        $scope.editando[index] = false;
        $scope.editandoEs = false;
        
        servicioEspecies.modificarEspecie(idEspecie, nComun, nCientifico, idEstado).then(function(resultado){
            $scope.actualizarPagina($scope.paginaActual);
            $scope.mensaje = 'La especie ha sido modificada';
            $scope.exito = true;
        }, function(resultado){
            $scope.mensaje = 'La especie no fue modificada. Intentelo más tarde';
            $scope.errores = true;
        });
        $timeout($scope.ocultarMensaje, 3000);
    }

    // Agrega una nueva especie
    $scope.agregarEspecie = function(){
        servicioEspecies.agregarEspecie($scope.nuevaEspecie).then(function(resultado){
            $scope.actualizarPagina($scope.paginaActual);
            $scope.mensaje = 'La especie ha sido agregada';
            $scope.exito = true;
            $scope.limpiarEspecie();
        }, function(resultado){
            $scope.mensaje = 'La especie no fue agregada. Intentelo más tarde';
            $scope.errores = true;
            $scope.limpiarEspecie();
        });
        $timeout($scope.ocultarMensaje, 3000);
    }
}]);