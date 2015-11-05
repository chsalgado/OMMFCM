var app=angular.module('appPrivada', ['ngRoute']);

app.config(function($routeProvider, $httpProvider){

    $routeProvider.when("/resumen", {
        controller: "controladorDashboard",
        templateUrl: "/vistas/vistaDashboard.html"
    });

    $routeProvider.when("/incidentes", {
        controller: "controladorIncidentes",
        templateUrl: "/vistas/vistaIncidentes.html"
    });

    $routeProvider.when("/especies", {
        controller: "controladorEspecies",
        templateUrl: "/vistas/vistaEspecies.html"
    });

    $routeProvider.otherwise({redirectTo: "/resumen"});

    $httpProvider.defaults.useXDomain = true;
  delete $httpProvider.defaults.headers.common['X-Requested-With'];
});

// TODO Remove
var servicioBase = 'http://148.243.51.170:8007/obsfauna/public_html/index.php/';
//var servicioBase = 'http://localhost/OMMFCM/Api/OMMFCM/public/';
//var servicioBase = 'http://jorgegonzac-001-site1.hostbuddy.com/public_html/index.php/';