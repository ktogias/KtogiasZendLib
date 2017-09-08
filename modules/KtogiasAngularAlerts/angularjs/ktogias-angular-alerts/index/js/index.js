/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

/* global angular */
angular.module('alerts', ['ui.bootstrap','ngAnimate'])
.factory('alertsService', ['$translate', function($translate){
    
    var alerts = [];
    
    function add(type, msg, timeout) {
        $translate(msg).then(function(msg){
            return alerts.push({
                type: type,
                msg: msg,
                timeout: timeout,
                close: function() {
                    return closeAlert(this);
                }
            });
        }, function(){
            return alerts.push({
                type: type,
                msg: msg,
                timeout: timeout,
                close: function() {
                    return closeAlert(this);
                }
            });
        });
    }
    
    function closeAlert(alert) {
        return closeAlertIdx(alerts.indexOf(alert));
    }

    function closeAlertIdx(index) {
        return alerts.splice(index, 1);
    }

    function clear(){
        for (var i in alerts){
            closeAlertIdx(i);
        }
        alerts.splice(0, alerts.length);
    }

    function get() {
        return alerts;
    }
    
    var service = {
        add: add,
        closeAlert: closeAlert,
        closeAlertIdx: closeAlertIdx,
        clear: clear,
        get: get
    };
    
    return service;
}])
.directive('alerts',['$window', function($window){
    var serverVars = $window.serverVars['KtogiasAngularAlerts\\Controller\\IndexController\\index'];
    return {
        restrict: 'E',
        scope: {},
        template: serverVars.templates.alerts,
        replace: true,
        controllerAs:'alertsCtrl',
        controller: ['$scope', 'alertsService', function($scope, alertsService) {
            $scope.showSpan = true;
            $scope.alerts = alertsService.get();
            $scope.alertTemplate = serverVars.templates.alert;
        }]
    };
}]);


