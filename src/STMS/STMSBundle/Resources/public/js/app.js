(function() {
    var app = angular.module('stms', ['ui.bootstrap']).config(function($interpolateProvider){
        $interpolateProvider.startSymbol('{[').endSymbol(']}');
    });

    app.controller('STMSController', function($scope, $http, $modal, $filter) {
        $scope.tasks = {};

        $http.get('/app_dev.php/task/list')
            .success(function (data) {
                $scope.tasks = data;
            })
            .error(function (data) {
                console.log('Error: ' + data);
            });

        $scope.displayAddDialog = function () {
            var dialogInstance = $modal.open({
                templateUrl: 'addDialogContent.html',
                controller: 'AddDialogController',
                size: 'sm',
                resolve: {
                    items: function () {
                        return $scope.tasks;
                    }
                }
            });

            dialogInstance.result.then(function (newTask) {
                $scope.tasks.push(newTask);
            });
        };

        $scope.deleteTask = function(task) {
            $http.delete('/app_dev.php/task/delete/' + task.id)
                .success(function(data) {
                    if(data.result == "success") {
                        $scope.tasks.splice($scope.tasks.indexOf(task), 1);
                    }
                    else {
                        // TODO: Display error
                    }
                });
        };
    });

    app.controller('AddDialogController', function($scope, $http, $modalInstance, $filter, items) {
        $scope.newTask = {};
        $scope.newTask.date = new Date();

        $scope.submitTask = function () {
            $http({
                method: 'POST',
                url: '/app_dev.php/task/add',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                data: $scope.newTask,
                transformRequest: transformAddRequest
            }).success(function (data) {
                if(data.result == "success") {
                    $scope.newTask.id = data.taskId;
                    $modalInstance.close($scope.newTask);
                }
                else {
                    $modalInstance.dismiss();
                }
            });
        };

        $scope.cancelTask = function () {
            $modalInstance.dismiss();
        };

        function transformAddRequest(task) {
            var request = [];
            for(var val in task) {
                var value = task[val];

                if(value instanceof Date) {
                    value = $filter('date')(value, 'yyyy-MM-dd');
                }

                request.push(encodeURIComponent(val) + "=" + encodeURIComponent(value));
            }

            return request.join("&");
        }


    });

})();