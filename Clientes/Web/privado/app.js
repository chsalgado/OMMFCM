var app=angular.module('appPrivada', ['ngRoute', 'angular-loading-bar', 'appPrivada.config']);

app.config(function($routeProvider, $httpProvider){

    $routeProvider.when("/incidentes", {
        controller: "controladorIncidentes",
        templateUrl: "vistas/vistaIncidentes.html"
    });

    $routeProvider.when("/especies", {
        controller: "controladorEspecies",
        templateUrl: "vistas/vistaEspecies.html"
    });

    $routeProvider.otherwise({redirectTo: "/incidentes"});

    $httpProvider.defaults.useXDomain = true;
    delete $httpProvider.defaults.headers.common['X-Requested-With'];
});

app.config(function($httpProvider){
    $httpProvider.interceptors.push('servicioAutenticacion');
});

var servicioBase = 'http://watch.imt.mx/public_html/index.php/';
app.constant('ngServicio', {
    apiServicioBase: servicioBase
});
