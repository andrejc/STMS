(function() {
    var app = angular.module('stms', []).config(function($interpolateProvider){
        $interpolateProvider.startSymbol('{[').endSymbol(']}');
    });

    app.controller('STMSController', function($scope, $http) {
            $http.get('/app_dev.php/task/list')
                .success(function(data) {
                    $scope.tasks = data;
                    console.log(data);
                })
                .error(function(data) {
                    console.log('Error: ' + data);
                });
        }
    );

})();