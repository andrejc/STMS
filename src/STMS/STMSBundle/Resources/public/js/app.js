(function() {
    var app = angular.module('stms', ['ui.bootstrap', 'angular.filter']).config(function($interpolateProvider){
        $interpolateProvider.startSymbol('{[').endSymbol(']}');
    });

    app.controller('STMSController', function($scope, $http, $modal, $filter) {
        $scope.user = {};
        $scope.tasks = {};

        $http.get('/app_dev.php/user/getData').success(function (data) {
            $scope.user = data;
        }).error(function (data) {
                console.log('Error: ' + data);
            });

        $http.get('/app_dev.php/task/list', {
            transformResponse: transformResponse
        }).success(function (data) {
                $scope.tasks = data;
            })
            .error(function (data) {
                console.log('Error: ' + data);
            });

        function transformResponse(data) {
            var tasks = angular.fromJson(data);

            angular.forEach(tasks, function(task) {
                task.date = new Date(task.date);
            });

            return tasks;
        }

        $scope.displayAddDialog = function () {
            var dialogInstance = $modal.open({
                templateUrl: 'taskDialogContent.html',
                controller: 'AddOrEditDialogController',
                size: 'sm',
                resolve: {
                    task: function () {
                        return null;
                    }
                }
            });

            dialogInstance.result.then(function (newTask) {
                $scope.tasks.push(newTask);
            });
        };

        $scope.displayEditDialog = function (task) {
            var dialogInstance = $modal.open({
                templateUrl: 'taskDialogContent.html',
                controller: 'AddOrEditDialogController',
                size: 'sm',
                resolve: {
                    task: function () {
                        return angular.copy(task);
                    }
                }
            });

            dialogInstance.result.then(function (updatedTask) {
                angular.copy(updatedTask, task);
            });
        };

        $scope.displaySettingsDialog = function (user) {
            var dialogInstance = $modal.open({
                templateUrl: 'settingsDialogContent.html',
                controller: 'SettingsDialogController',
                size: 'sm',
                resolve: {
                    user: function () {
                        return angular.copy(user);
                    }
                }
            });

            dialogInstance.result.then(function (updatedUser) {
                angular.copy(updatedUser, user);
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

    app.controller('AddOrEditDialogController', function($scope, $http, $modalInstance, $filter, task) {
        if(task == null) {
            $scope.requestType = 'add';
            $scope.curTask = {};
            $scope.curTask.date = new Date();
        }
        else {
            $scope.requestType = 'edit';
            $scope.curTask = task;
        }

        $scope.submitTask = function () {
            $http({
                method: $scope.requestType == 'add' ? 'POST' : 'PUT',
                url: $scope.requestType == 'add' ? '/app_dev.php/task/add' : '/app_dev.php/task/edit/' + task.id,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                data: $scope.curTask,
                transformRequest: transformRequest
            }).success(function (data) {
                if(data.result == "success") {
                    if($scope.requestType == 'add') {
                        $scope.curTask.id = data.taskId;
                    }

                    $modalInstance.close($scope.curTask);
                }
                else {
                    $modalInstance.dismiss();
                }
            });
        };

        $scope.cancelTask = function () {
            $modalInstance.dismiss();
        };

        function transformRequest(task) {
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

    app.controller('SettingsDialogController', function($scope, $http, $modalInstance, $filter, user) {
        $scope.user = user;

        $scope.submitSettings = function () {
            $http({
                method: 'PUT',
                url: '/app_dev.php/user/setPreferredHours/' + $scope.user.preferredWorkingHoursPerDay,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function (data) {
                if(data.result == "success") {
                    $modalInstance.close($scope.user);
                }
                else {
                    $modalInstance.dismiss();
                }
            });
        };

        $scope.cancelSettings = function () {
            $modalInstance.dismiss();
        };
    });

    app.filter('totalDuration', function() {
        return function(tasks) {
            var totalDuration = 0;
            for (var i = 0; i < tasks.length; i++) {
                totalDuration += parseInt(tasks[i].minutes);
            };

            if(totalDuration < 60) {
                return totalDuration + " minutes";
            }
            else {
                var hourString =  totalDuration / 60 > 1 ? " hours" : " hour";
                var result = Math.floor(totalDuration / 60) + hourString;

                if(totalDuration % 60 != 0) {
                    result += " " + totalDuration % 60 + " minutes";
                }

                return result;
            }

        };
    });
})();