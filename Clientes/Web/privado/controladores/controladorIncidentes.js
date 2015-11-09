'use strict';
app.controller('controladorIncidentes', ['$scope', '$timeout', '$filter', '$window', 'servicioIncidentes', 'servicioEspecies', function($scope, $timeout, $filter, $window, servicioIncidentes, servicioEspecies){

    // Lista de incidentes
    $scope.incidentes = [];

    // Objeto utilizado para agregar una especie
    $scope.nuevaEspecie = {
        "nombreComun":null,
        "nombreCientifico":null,
        "idEstadoEspecie":1,
        "idEstadoEspecie2":1
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

    // Variables para visualizar foto
    $scope.ruta = 'http://watch.imt.mx/public_html/imagenes';
    $scope.asignarRutaFoto = function(rutaFoto){
        $scope.rutaFoto = rutaFoto;
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
        if(!$scope.estados){
            servicioEspecies.obtenerEstadosEspecies().then(function(resultado){
                $scope.estados = resultado[0];
                $scope.estados2 = resultado[1];
            });
        }
    }

    // Obtiene todas las especies
    // Las especies llenan los selects
    $scope.obtenerEspecies = function(pagina){
        servicioEspecies.obtenerEspecies().then(function(resultados){
            $scope.especies = resultados;
            $scope.especiesFiltro = [{"idEspecie": -1, "nombreComun": "- - Todas las especies", "nombreCientifico": "-", "created_at":"2015-09-19 00:00:00","updated_at":"2015-09-19 00:00:00"}];
            $scope.especiesFiltro.push.apply($scope.especiesFiltro, resultados);
            $scope.actualizarPagina(pagina);
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
            if(pagina == $scope.ultimaPagina || $scope.ultimaPagina == 0){
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
            $scope.obtenerEspecies($scope.paginaActual);
            $scope.mensaje = 'La especie ha sido agregada';
            $scope.limpiarEspecie();
            $scope.exito = true;
        }, function(resultado){
            $scope.mensaje = 'La especie no fue agregada. Intentelo más tarde';
            $scope.errores = true;
            $scope.limpiarEspecie();
        });
        $timeout($scope.ocultarMensaje, 3000);
    }

    // Descarga los incidentes a un Excel
    $scope.obtenerExcel = function(){
        servicioIncidentes.obtenerExcel().then(function(resultado){
            // Si resultado no es un objeto JSON.parse convierte el string en objeto
            var arrInc = typeof resultado != 'object' ? JSON.parse(resultado) : resultado;

            var XLS = '';

            // Generar los encabezados
            var encabezado = "";
            for(var index in arrInc[0]){
                encabezado += index + ',';
            }
            encabezado = encabezado.slice(0, -1);
            XLS += encabezado + '\r\n';

            // Extraer cada renglón
            for(var i = 0; i < arrInc.length; i++){
                var renglon = "";

                // Extraer cada columna
                for(var index in arrInc[i]){
                    renglon += '"' + arrInc[i][index] + '",';
                }

                renglon.slice(0, renglon.length - 1);
                XLS += renglon + '\r\n';
            }

            // Verifica que los datos se crearon bien
            if(XLS == ''){
                $scope.mensaje = "No ha sido posible extraer los datos. Inténtelo más tarde";
                $scope.errores = true;
            }

            // Nombre del archivo
            var nombreArch = "Reporte_de_incidentes";

            // Formato de archivo
            var uri = 'data:text/csv;charset=utf-8,' + escape(XLS);

            // Generar link que descargará archivo
            // Esconder el link y dar click automático
            // Remover link
            var link = document.createElement("a");
            link.href = uri;
            link.setAttribute('style', 'visibility:hidden');
            link.download = nombreArch + ".csv";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }, function(resultado){
            $scope.mensaje = "No ha sido posible extraer los datos. Inténtelo más tarde";
            $scope.errores = true;            
        });
        $timeout($scope.ocultarMensaje, 3000);

    }

    // MAPA
    // Crear el mapa
    $scope.map;
    $window.initMap = function(){
        $scope.mexico = new google.maps.LatLng(23.945963, -102.537750);
        $scope.mapOptions = {
            zoom: 7,
            center: $scope.mexico
        }
        $scope.map = new google.maps.Map(document.getElementById("map-canvas"), $scope.mapOptions);
     }

     // Muestra la ubicación del incidente
     $scope.mostrarUbicacion = function(index){
        if($scope.marker)
            $scope.marker.setMap(null);
        $scope.latLng = {
            lat: parseFloat($scope.incidentes[index].lat),
            lng: parseFloat($scope.incidentes[index].long)
            };
        $scope.marker = new google.maps.Marker({
            map: $scope.map,
            position: $scope.latLng
        });
        $timeout($scope.recentrar, 300);
     }

     // Re-centra el mapa cuando se abre la modal
     $scope.recentrar = function(){
        google.maps.event.trigger($scope.map, 'resize');
        $scope.map.setCenter($scope.latLng);
     }
}]);