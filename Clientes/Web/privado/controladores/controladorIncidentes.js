'use strict';
app.controller('controladorIncidentes', ['$scope', '$timeout', '$filter', 'servicioIncidentes', 'servicioEspecies', function($scope, $timeout, $filter, servicioIncidentes, servicioEspecies){

    // Lista de incidentes
    $scope.incidentes = [];

    // Objeto utilizado para agregar una especie
    $scope.nuevaEspecie = {
        "nombreComun":null,
        "nombreCientifico":null,
        "idEstadoEspecie":1
    };

    // Variables de paginación
    $scope.paginaActual = 1;
    $scope.resultadosDisponibles = [10, 20, 30, 40, 50];
    $scope.resultados = 10;
    $scope.especieSeleccionada = -1;

    // Retroalimentación al usuario
    $scope.mensaje = '';
    $scope.exito = false;
    $scope.errores = false;

    // Variable que permite que se edite un incidente a la vez 
    $scope.editandoIn = false;

    // Oculta la retroalimentacion al usuario
    $scope.ocultarMensaje = function(){
        $scope.exito = false;
        $scope.errores = false;
    }

    // Obtiene los estados que puede tener una especie
    $scope.obtenerEstadosEspecies = function(){
        if(!$scope.estados){
            servicioEspecies.obtenerEstadosEspecies().then(function(resultado){
                $scope.estados = resultado;
            });
        }
    }

    // Obtiene todas las especies
    // Las especies llenan los selects
    $scope.obtenerEspecies = function(){
        servicioEspecies.obtenerEspecies().then(function(resultados){
            $scope.especies = resultados;
            $scope.especiesFiltro = [{"idEspecie": -1, "nombreComun": "- - Todas especies", "nombreCientifico": "-", "created_at":"2015-09-19 00:00:00","updated_at":"2015-09-19 00:00:00"}];
            $scope.especiesFiltro.push.apply($scope.especiesFiltro, resultados);
            $scope.actualizarPagina(1);
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
        // Arreglo que guarda los nombres de especie de cada incidente
        $scope.nombreEspecie = [];

        $scope.editando = [];
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

            angular.forEach($scope.incidentes, function(inc){
                // Arreglo que permite saber que incidente se está modificando
                $scope.editando.push.apply($scope.editando, [false]);
                $scope.nombresDeEspecie(inc.idEspecie);
            });
        });
    }

    // Obtiene los nombres de especie de un incidente
    $scope.nombresDeEspecie = function(idEspecie){
        // Encuentra la especie con el id recibido
        $scope.nEspecie = $filter('filter')($scope.especies, function(resultado){
            return resultado.idEspecie === idEspecie;
        })[0];

        // Agrega los nombres al arreglo para mostrarlos en la tabla
        $scope.nombreEspecie.push.apply($scope.nombreEspecie, [$scope.nEspecie.nombreComun + ' - ' + $scope.nEspecie.nombreCientifico]);
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

    // Muestra los campos que pueden ser modificados en un incidente
    $scope.editables = function(index){
        $scope.editando[index] = true;
        $scope.editandoIn = true;
    }

    // Modifica un incidente
    $scope.modificarIncidente = function(index, idIncidente, idEspecie, ruta, km){        
        $scope.editando[index] = false;
        $scope.editandoIn = false;
        
        servicioIncidentes.modificarIncidente(idIncidente, idEspecie, ruta, km).then(function(resultado){
            $scope.actualizarPagina($scope.paginaActual);
            $scope.mensaje = 'El incidente ha sido modificado';
            $scope.exito = true;
        }, function(resultado){
            $scope.mensaje = 'El incidente no fue modificado. Intentelo más tarde';
            $scope.errores = true;
        });
        $timeout($scope.ocultarMensaje, 3000);
    }

    // Agrega una nueva especie
    $scope.agregarEspecie = function(){
        servicioEspecies.agregarEspecie($scope.nuevaEspecie).then(function(resultado){
            $scope.obtenerEspecies();
            $scope.mensaje = 'La especie ha sido agregada';
            $scope.exito = true;
        }, function(resultado){
            $scope.mensaje = 'La especie no fue agregada. Intentelo más tarde';
            $scope.errores = true;
        });
        $timeout($scope.ocultarMensaje, 3000);
    }
}]);