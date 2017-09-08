/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

/* global angular */
angular.module('auth', ['alerts'])
/*.factory('authenticateService', ['$rootScope', '$http', '$location', 'alertsService', function($rootScope, $http, $location, alertsService){
    function setIdentity(identity){
        $rootScope.identity = identity;
        $rootScope.$broadcast('authenticated');
    }
    
    return function(username, password){
        $http.post('/ktogias-angular-auth/json/authenticate', 
            {username: username, password: password})
            .then(function(response){
                if (response.data.authenticated){
                    alertsService.add('success', 'Επιτυχής ταυτοποίηση.', '10000');
                    setIdentity(response.data.identity);
                    $location.path(response.data.home);
                }
                else {
                    setIdentity(null);
                    alertsService.add('warning', 'Η ταυτοποίηση δεν ήταν επιτυχής. Δοκιμάστε ξανά.', '10000');
                }
            }, function(response){
                alertsService.add('danger', 'Σφάλμα κατά την ταυτοποίηση.', '10000');
            });
    };
}])*/
.factory('unauthenticateService', ['$rootScope', '$http', '$location', '$window', 'alertsService', function($rootScope, $http, $location, $window, alertsService){
        return function(success, failure){
            $http.post('/ktogias-angular-auth/json/unauthenticate').then(function(response){
                if(response.data.unauthenticated === true){
                    $rootScope.$broadcast('unauthenticated');
                    if (typeof success === 'function'){
                        success(response.data);
                    }
                    //alertsService.add('success', 'Έχετε αποσυνδεθεί.', '10000');
                }
                else {
                    if (typeof failure === 'function'){
                        failure(response.data);
                    }
                    //alertsService.add('warning', 'Σφάλμα κατά την αποσύνδεση.', '10000');
                }
            }, function(response){
                if (typeof failure === 'function'){
                    failure(response);
                }
                alertsService.add('danger', 'Σφάλμα κατά την αποσύνδεση.', '10000');
            });
            $window.serverVars['KtogiasAngularAuth\\Controller\\IndexController\\index'].identity = null;
            $rootScope.identity = null;
            $location.path('/');
        };
}])
.factory('identityService',['$rootScope', '$http', '$window', 'alertsService', function($rootScope, $http, $window, alertsService){
        return function(success, failure, reload){
            if ($rootScope.identity && !reload){
                if (typeof success === 'function'){
                    success($rootScope.identity);
                }
                return;
            }
            if ($window.serverVars['KtogiasAngularAuth\\Controller\\IndexController\\index'].identity === null || reload){
                if ($window.serverVars['KtogiasAngularAuth\\Controller\\IndexController\\index'].identity){
                    $window.serverVars['KtogiasAngularAuth\\Controller\\IndexController\\index'].identity = null;
                }
                $http.get('/ktogias-angular-auth/json/identity').then(function(response){
                        $rootScope.identity = response.data.identity;
                        $rootScope.$broadcast('identityService\\identityReloaded', $rootScope.identity);
                        if (typeof success === 'function'){
                            success($rootScope.identity);
                        }
                    }, function(response){
                        $rootScope.identity = null;
                        if (response.status !== 401){
                            //console.log(response);
                            alertsService.add('danger', 'Σφάλμα κατά τον έλεγχο ταυτοποίησης.');
                        }
                        if (typeof failure === 'function'){
                            failure(response);
                        }
                    });
            }
            else {
                $rootScope.identity = $window.serverVars['KtogiasAngularAuth\\Controller\\IndexController\\index'].identity;
                if (typeof success === 'function'){
                    success($rootScope.identity);
                }
            }
        };
}])
.run(['$timeout', '$window','identityService', 'alertsService', 'spinnerService', function($timeout, $window, identityService, alertsService, spinnerService){
    var refresh = function(){
        $timeout(function(){
            identityService(function(){
                refresh();
            }, function(response){
                if (response.status === 401){
                    try {
                        spinnerService.show('mainAreaSpinner');
                    }
                    catch (ex){}
                    alertsService.add('warning', 'Παρακαλούμε συνδεθείτε για να συνεχίσετε.');
                    $timeout(function(){
                        $window.location.href = '/';
                    }, 10000);
                }
            }, true);
        }, 180000);
    };
    refresh();
}])
;

