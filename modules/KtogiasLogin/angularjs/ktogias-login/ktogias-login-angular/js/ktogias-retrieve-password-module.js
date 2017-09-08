/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

angular.module('ktogiasretrievepassword', ['alerts'])
.run(['$window', '$templateCache', function($window, $templateCache){
    var serverVars = $window.serverVars['KtogiasLogin\\Controller\\KtogiasLoginAngularController\\ktogias-retrieve-password-module'];
    if (serverVars.templates) {
        for (var i in serverVars.templates) {
            $templateCache.put(i, serverVars.templates[i]);
        }

    }
}])
.factory('ktogiasretrievepasswordService', ['$window', '$http', '$rootScope', function($window, $http, $rootScope){
        var serverVars = $window.serverVars['KtogiasLogin\\Controller\\KtogiasLoginAngularController\\ktogias-retrieve-password-module'];
        function retrieve(usernameOrEmail, success, failure){
            $http.post('/login/ktogias-retrieve-password-json/retrieve', {
                usernameOrEmail: usernameOrEmail,
                lang: $rootScope.lang
            })
            .then(function(response){
                if (response.status === 200){
                    if (response.data.emailSent){
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
            retrieve: retrieve
        }; 
}])
.controller('ktogiasretrievepasswordController', ['$scope', '$window', '$location','ktogiasretrievepasswordService', 'alertsService', function($scope, $window, $location, ktogiasretrievepasswordService, alertsService){
    var serverVars = $window.serverVars['KtogiasLogin\\Controller\\KtogiasLoginAngularController\\ktogias-retrieve-password-module'];
    var controller = this;
    var watchers = [];
    
    controller.clear = function(){
        controller.usernameOrEmail = '';
        $scope.retrieveForm.$setPristine();
        $scope.retrieveForm.$setUntouched();
    };
    
    controller.retrieve = function(){
        ktogiasretrievepasswordService.retrieve(controller.usernameOrEmail, function(data){
            controller.successMessages = data.messages;
        }, function(data){
            for(var i in data.messages){
                alertsService.add('danger',data.messages[i], 10000);
            }
            if (!data.messages){
                alertsService.add('danger','Σφάλμα εφαρμογής!', 10000);
            }
            controller.clear();
        });
    };
    
    controller.goToLoginForm = function(){
        $location.path('/');
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