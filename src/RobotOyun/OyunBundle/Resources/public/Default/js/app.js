angular.module('myApp', ['ngRoute', 'ngTouch', 'snap']).config(function ($interpolateProvider) {
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
})

    .config(['$routeProvider', '$locationProvider', function ($routeProvider, $locationProvider) {
        $locationProvider.hashPrefix('!');
        $routeProvider
            .when('/', {templateUrl: 'home.html', controller: 'HomeCtrl'})
            .when('/game/:gameId', {templateUrl: 'game.html', controller: 'GameCtrl'})
            .when('/games/:catId/:catName', {templateUrl: 'categoryGames.html', controller: 'categoryGamesCtrl'})
            .when('/search', {templateUrl: 'search.html', controller: 'SearchCtrl'})
            .otherwise({redirectTo: '/'});
    }])

    .directive('myAdSense', [function () {
        return {
            restrict: 'A',
            transclude: true,
            replace: true,
            template: '<div ng-transclude></div>',
            link: function ($scope, element, attrs) {
            }
        }
    }])

    .controller('HomeCtrl', ['$scope', '$http', function ($scope, $http) {
        $scope.games = games;
        $scope.loading = false;
        $scope.disable = false;

        if(games.length == 0)
            $scope.disable = true;

        $scope.loadmore = function () {
            $scope.loading = true;
            var len = games.length;
            $http({method: 'GET', url: site + '/?len=' + len})
                .success(function (data, status, headers, config) {
                    angular.forEach(data, function (d) {
                        games.push(d);
                    });
                    $scope.loading = false;
                    if(data.length < 6){
                        $scope.disable = true;
                    }
                })
                .error(function (data, status, headers, config) {
                    console.log(status)
                    $scope.loading = false;
                });
        }
    }])

    .controller('MainCtrl', ['$scope', function ($scope) {
        $scope.snapOpts = {
            disable: 'right'
        };
    }])


    .controller('GameCtrl', ['$scope', '$routeParams', '$filter', '$http', function ($scope, $routeParams, $filter, $http) {
        var urlkey = $routeParams.gameId;
        var filt = $filter('filter')(games, urlkey);
        $scope.game = filt[0];
        if (!$scope.game) {
            $http({method: 'GET', url: site + '/?id=' + urlkey})
                .success(function (data, status, headers, config) {
                    $scope.game = data;
                })
                .error(function (data, status, headers, config) {
                    alert('this game does not exist')
                });
        }
    }])

    .controller('SearchCtrl', ['$scope', '$timeout', '$http', function ($scope, $timeout, $http) {
        $scope.games = [];
        var timer;

//check typing speed
        var checkTime = function () {
            $timeout(function () {
                var now = new Date().getTime();
                if (now - timer > 200) {
                    $http({method: 'GET', url: site + '//search?term=' + $scope.searchData})
                        .success(function (data, status, headers, config) {
                            $scope.games = data;
                        })
                        .error(function (data, status, headers, config) {
                            console.log(status)
                        });

                }
            }, 200);
        }

//searchData
        $scope.$watch('searchData', function (newVal, oldVal) {
            $scope.showpopular = true;
            if (newVal != oldVal && newVal.length > 2) {
                timer = new Date().getTime();
                checkTime();
            }
        });
    }])

    .controller('categoryGamesCtrl', ['$scope', '$routeParams', '$filter', '$http', function ($scope, $routeParams, $filter, $http) {
        var catid = $routeParams.catId;
        $scope.category = $routeParams.catName;
        $scope.categoryGames = [];
        $scope.loadingLoadMoreCategory = false;
        $scope.disableLoadMoreCategory = false;

        $http({method: 'GET', url: site + '/?id=' + catid})
            .success(function (data, status, headers, config) {
                $scope.categoryGames = data;

                if(data == 0)
                    $scope.disableLoadMoreCategory = true;
            })
            .error(function (data, status, headers, config) {
                alert('this game does not exist')
            });


        $scope.loadmore = function () {
            $scope.loadingLoadMoreCategory = true;
            var len = $scope.categoryGames.length;
            $http({method: 'GET', url: site + '/?len=' + len + "&id=" + catid})
                .success(function (data, status, headers, config) {
                    angular.forEach(data, function (d) {
                        $scope.categoryGames.push(d);
                    });
                    $scope.loadingLoadMoreCategory = false;

                    if(data.length < 6){
                        $scope.disableLoadMoreCategory = true;
                    }
                })
                .error(function (data, status, headers, config) {
                    console.log(status);
                    $scope.loadingLoadMoreCategory = false;
                });

        }
    }]);




