var app = angular.module('appPublica', ['ngRoute', 'angular-loading-bar']);

app.config(function ($routeProvider) {
    $routeProvider.when("/inicio", {
        templateUrl: "vistas/vistaInicio.html"
    });

    $routeProvider.when("/politicas", {
        templateUrl: "vistas/vistaPoliticas.html"
    });

    $routeProvider.when("/reportar", {
        controller: "controladorReportar",
        templateUrl: "vistas/vistaReportar.html"
    });

    $routeProvider.otherwise({ redirectTo: "/inicio" });
});

var servicioBase = 'http://watch.imt.mx/public_html/index.php/';
app.constant('ngServicio', {
    apiServicioBase: servicioBase
});