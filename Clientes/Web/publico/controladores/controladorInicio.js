app.controller('controladorInicio',['$scope', '$interval', function ($scope, $interval) {

     var duracion = 1600, pasos = 3, paso = 1;

    $scope.contador = paso;

    var start = $interval(function () {
        if ($scope.contador < pasos) {
            $scope.contador += paso;
        }
        else {
            $scope.contador = pasos;
         }
    }, duracion);

    $scope.intervalo = 3000;
    
    $scope.agruparSlides = false;
    
    $scope.slides = [];
    
    $scope.iniciarSlides = function() {
        for(var i=0; i<3; i++){
            $scope.slides.push({
              imagen: 'images/home_wallpaper' + (i+1) + '.jpg'
            });
        }
    };
    
    $scope.iniciarSlides();

}]);