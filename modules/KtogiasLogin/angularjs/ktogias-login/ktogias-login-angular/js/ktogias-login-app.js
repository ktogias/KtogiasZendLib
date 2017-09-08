/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

/* global angular */

angular.module('app', [
    'ngRoute'
    , 'ngSanitize'
    , 'alerts'
    , 'ktogiasAngularLocale'
    , 'ktogiaslogin'
    , 'ktogiasretrievepassword'
    , 'ktogiasresetpassword'
    , 'ktogiasactivateaccount'
])
.config(['$routeProvider', function($routeProvider) { 
  $routeProvider
    .when('/', {
        templateUrl: 'ktogias-login/login-form.phtml'
    })
    .when('/retrieve', {
        templateUrl: 'ktogias-retrieve-password/retrieve-password-form.phtml'
    })
    .when('/reset-password', {
        templateUrl: 'ktogias-reset-password/reset-password-form.phtml'
    })
    .when('/activate-account', {
        templateUrl: 'ktogias-activate-account/activate-account-form.phtml'
    })
    .otherwise({
      redirectTo:'/'
    });
}])
.config(['$httpProvider', function($httpProvider) {
    $httpProvider.defaults.withCredentials = true;
}])
;
