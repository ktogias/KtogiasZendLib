/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

angular.module('ktogiaslogin', ['alerts', 'validation.match'])
.run(['$window', '$templateCache', function($window, $templateCache){
    var serverVars = $window.serverVars['KtogiasLogin\\Controller\\KtogiasLoginAngularController\\ktogias-login-module'];
    if (serverVars.templates) {
        for (var i in serverVars.templates) {
            $templateCache.put(i, serverVars.templates[i]);
        }

    }
}])
.factory('ktogiasloginService', ['$window', '$http' ,function($window, $http){
        var serverVars = $window.serverVars['KtogiasLogin\\Controller\\KtogiasLoginAngularController\\ktogias-login-module'];
        function login(username, password, success, failure){
            $http.post('/login/ktogias-login-json/login', {username: username, password: password})
            .then(function(response){
                if (response.status === 200){
                    if (response.data.authenticated){
                        if (typeof success === 'function'){
                            success(response.data);
                        }
                    }
                    else {
                         //console.log(response);
                        if (typeof failure === 'function'){
                            failure(response.data);
                        }
                    }
                }
                else {
                     //console.log(response);
                    alert('ΣΦΑΛΜΑ ΔΙΑΚΟΜΙΣΤΗ ('+response.data.error+') ('+response.data.message+')');
                }
                
            },function(response){
                //console.log(response);
                alert('ΣΦΑΛΜΑ ΕΠΙΚΟΙΝΩΝΙΑΣ ('+response.status+') ('+JSON.stringify(response.data)+')');
            });
        }
        return {
            login: login
        }; 
}])
.controller('ktogiasloginController', ['$scope', '$window', '$location','ktogiasloginService', 'alertsService', function($scope, $window, $location, ktogiasloginService, alertsService){
    var serverVars = $window.serverVars['KtogiasLogin\\Controller\\KtogiasLoginAngularController\\ktogias-login-module'];
    var controller = this;
    var watchers = [];
    
    alertsService.clear();
    
    controller.clear = function(){
        controller.username = '';
        controller.password = '';
        $scope.loginForm.$setPristine();
        $scope.loginForm.$setUntouched();
    };
    
    controller.login = function(){
        ktogiasloginService.login(controller.username, controller.password, function(){
            $window.location.href = serverVars.afterLoginRedirect;
        }, function(data){
            alertsService.add('danger',data.message, 10000);
            controller.clear();
        });
    };
    
    controller.goToRetrievePasswordForm = function(){
        $location.path('/retrieve');
    };
    
    watchers.push(
        $scope.$on('$destroy', function(){
           for (var w in watchers){
               watchers[w]();
           }
        })
    );
}])
;