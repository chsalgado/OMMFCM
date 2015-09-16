var app = angular.module('appPublica', ['ngRoute']);

app.config(function ($routeProvider) {
    $routeProvider.when("/inicio", {
        controller: "controladorInicio",
        templateUrl: "/vistas/vistaInicio.html"
    });

    $routeProvider.when("/", {
        controller: "controladorInicio",
        templateUrl: "/vistas/vistaInicio.html"
    });

    $routeProvider.when("/reportar", {
        controller: "",
        templateUrl: "/vistas/vistaReportar.html"
    });

    $routeProvider.otherwise({ redirectTo: "/inicio" });
});
