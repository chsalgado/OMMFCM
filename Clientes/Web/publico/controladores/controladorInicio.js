app.controller('controladorInicio',['$scope', '$interval', function ($scope, $interval) {

     var duration = 1600, steps = 3, step = 1;

     $scope.customAttributeValue = step;

    var start = $interval(function () {
        if ($scope.customAttributeValue < steps) {
            $scope.customAttributeValue += step;
        }
        else {
            $scope.customAttributeValue = step;
         }
    }, duration);

    $scope.myInterval = 3000;
    
    $scope.noWrapSlides = false;
    
    $scope.slides = [];
    
    $scope.initSlides = function() {
        for(var i=0; i<3; i++){
            $scope.slides.push({
              image: 'images/home_wallpaper' + (i+1) + '.jpg'
            });
        }
    };
    
    $scope.initSlides();

}]);