var app=angular.module('appPrivada', ['ngRoute', 'angular-loading-bar', 'uiGmapgoogle-maps', 'ui.bootstrap', 'ngTagsInput']);

app.config(function($routeProvider, $httpProvider){

    $routeProvider.when("/incidentes", {
        controller: "controladorIncidentes",
        templateUrl: "vistas/vistaIncidentes.html"
    });

    $routeProvider.when("/especies", {
        controller: "controladorEspecies",
        templateUrl: "vistas/vistaEspecies.html"
    });

    $routeProvider.when("/reporte", {
        controller: "controladorReporteMapa",
        templateUrl: 'vistas/vistaReporteMapa.html'
    });

    $routeProvider.otherwise({redirectTo: "/incidentes"});

    $httpProvider.defaults.useXDomain = true;
    delete $httpProvider.defaults.headers.common['X-Requested-With'];
});

app.config(function(uiGmapGoogleMapApiProvider){
    uiGmapGoogleMapApiProvider.configure({
        //    key: 'your api key',
        v: '3.20', //defaults to latest 3.X anyhow
        libraries: 'weather,geometry,visualization'
    });
})

var servicioBase = 'http://watch.imt.mx/public_html/index.php/';
app.constant('ngServicio', {
    apiServicioBase: servicioBase
});
