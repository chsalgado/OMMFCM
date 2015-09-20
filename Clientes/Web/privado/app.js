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

	$routeProvider.otherwise({redirectTo: "/resumen"});

	$httpProvider.defaults.useXDomain = true;
  delete $httpProvider.defaults.headers.common['X-Requested-With'];
});

var servicioBase = 'http://localhost/OMMFCM/Api/OMMFCM/public/';



//Routing debugging code

app.run(['$rootScope',  function($rootScope) {
      // see what's going on when the route tries to change
      $rootScope.$on('$routeChangeStart', function(event, next, current) {
          // next is an object that is the route that we are starting to go to
          // current is an object that is the route where we are currently
          var currentPath = current.originalPath;
          var nextPath = next.originalPath;

          console.log('Starting to leave %s to go to %s', currentPath, nextPath);
      });
}]);