/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

/* global angular */
angular.module('usermenu', ['ui.bootstrap','ngAnimate', 'alerts'])
.factory('usermenuService', ['$http', '$window', 'alertsService', function($http, $window, alertsService){
     var serverVars = $window.serverVars['KtogiasAngularUserMenu\\Controller\\IndexController\\index'];
     return function(success, failure, reload){
        if (serverVars !== null && !reload){
            success(serverVars);
        }
        else {
            $http.get('/ktogias-angular-user-menu/json')
                .then(function(response){
                    success(response.data);
                },function(response){
                    alertsService.add('danger', 'Σφάλμα κατά τη λήψη του μενού χρήστη.', 10000);
                    failure(response);
                });
        }
     };
}])
.directive('usermenu',['$window', function($window){
    return {
        restrict: 'E',
        template: '<div ng-include="\'user-menu-template\'"></div>',
        replace: true,
        controllerAs:'usermenuCtrl',
        controller: ['$rootScope', '$scope', '$templateCache', 'usermenuService', 'identityService', function($rootScope, $scope, $templateCache,  usermenuService, identityService) {
            function initMenu(reload){
                identityService(function(identity){
                     usermenuService(function(userMenu){
                        $templateCache.put('user-menu-template', userMenu.templates['user-menu']);
                        $scope.username = identity.username;
                        $scope.fullname = identity.firstname+' '+identity.lastname;
                        $scope.position = userMenu.menu.position;
                        $scope.menuItems = userMenu.menu.items;
                        $scope.status = {
                            isopen: false
                        };
                    }, function(){
                        $templateCache.put('user-menu-template', '');
                        $scope.menuItems = null;
                        $window.serverVars['KtogiasAngularUserMenu\\Controller\\IndexController\\index'] = null;
                    });
                },function(){
                    $templateCache.put('user-menu-template', '');
                    $scope.menuItems = null;
                    $window.serverVars['KtogiasAngularUserMenu\\Controller\\IndexController\\index'] = null;
                }, reload);
            }
  
            initMenu();
            
            $rootScope.$on('authenticated', function () {
                initMenu(true);
            });
            
            $rootScope.$on('unauthenticated', function () {
                initMenu(true);
            });
            
            $rootScope.$on('identityService\\identityReloaded', function () {
                initMenu();
            });
        }]
    };
}]);

