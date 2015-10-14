'use strict';
app.controller('controladorEspecies', ['$scope', '$timeout', 'servicioEspecies', function($scope, $timeout, servicioEspecies){

    // Lista de especies
    $scope.especies = [];

    // Estado de la especie
    $scope.estado = ['Sin Clasificar','Amenazada', 'Peligro de extinción', 'Endémica', 'Protegida', 'Sin estatus en la NOM-059'];

    // Variables de paginacion
    $scope.paginaActual = 1;
    $scope.resultadosDisponibles = [10, 20, 30, 40, 50];
    $scope.resultados = 10;

    // Retroalimentación al usuario
    $scope.mensaje = '';
    $scope.exito = false;
    $scope.errores = false;

    // Oculta la retroalimentacion al usuario
    $scope.ocultarMensaje = function(){
        $scope.exito = false;
        $scope.errores = false;
    }

    // Obtiene los incidentes para llenar la tabla
    // Se llama cuando:
    //      Se carga la pagina
    //      Se cambia de pagina
    //      Se selecciona numero de resultados a mostrar
    //      Se elimina o modifica una especie
    $scope.actualizarPagina = function(pagina){
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
        });
    }
}]);