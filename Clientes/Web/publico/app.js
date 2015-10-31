var app = angular.module('appPublica', ['ngRoute', 'angular-loading-bar']);

app.config(function ($routeProvider) {
    $routeProvider.when("/inicio", {
        controller: "controladorInicio",
        templateUrl: "/vistas/vistaInicio.html"
    });

    $routeProvider.when("/reportar", {
        controller: "controladorReportar",
        templateUrl: "/vistas/vistaReportar.html"
    });

    $routeProvider.otherwise({ redirectTo: "/inicio" });
});

// TODO Remove
//var servicioBase = 'http://localhost/OMMFCM/Api/OMMFCM/public/';
var servicioBase = 'http://148.243.51.170:8007/obsfauna/public_html/index.php/';