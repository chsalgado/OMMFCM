'use strict';
app.factory('servicioIncidentes', ['$http', function ($http) {

    var servicioIncidentesFactory = {};

    var _crearIncidente = function () {

        return $http.get(serviceBase + 'api/players?teamId=' + teamId + '&page=1&take=30').then(function (results) {
            return results;
        });
    };

    servicioIncidentesFactory = _crearIncidente;

    return servicioIncidentesFactory;

}]);
