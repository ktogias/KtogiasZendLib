/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

/* global angular */
angular.module('values', ['ui.bootstrap','ngAnimate', 'ngFileUpload', 'validation.match', 'ui.select'])
.run(['$window', '$templateCache', function($window, $templateCache){
    var serverVars = $window.serverVars['KtogiasAngularValues\\Controller\\IndexController\\index'];
    if (serverVars.templates) {
        for (var i in serverVars.templates) {
            $templateCache.put(i, serverVars.templates[i]);
        }
    }
}])
.filter('grUppercase', ['$filter', function($filter){
    /**
     * 
     * @param {Object} item
     * @returns {unresolved}
     */
    return function(item){
        if (typeof item === 'string'){
            return $filter('uppercase')(item)
                .replace('Ά', 'Α')
                .replace('Έ', 'Ε')
                .replace('Ύ', 'Υ')
                .replace('Ί', 'Ι')
                .replace('Ό', 'Ο')
                .replace('Ή','Η')
                .replace('Ώ', 'Ω')
                .replace('ς', 'Σ')
                ;
        }
        else {
            return item;
        }
    };
}])
.directive('value',['$window', '$parse', function($window, $parse){
    var serverVars = $window.serverVars['KtogiasAngularValues\\Controller\\IndexController\\index'];
    return {
        restrict: 'E',
        scope: {
            label: '@', 
            value: '@',
            valuesMap: '=',
            valueType: '@',
            dateFormat: '@',
            dateSourceFormat: '@',
            valueSuffix: '@',
            fileTarget: '@',
            fileLinkLabel: '@',
            editable: '@',
            editPattern: '@',
            editMinlength: '@',
            editMaxlength: '@',
            editPlaceholder: '@',
            editCapitalize: '@',
            editSaveKey: '@',
            editSaveAction:'=',
            moderated: '@',
            pendingEdit: '=',
            capitalizeFirstLetter: '@',
            editModelOptions: '=',
            editDateOptions: '=',
            editFilePattern: '=',
            editFileAccept: '=',
            editFileMaxSize: '@',
            editFileUploadUrl: '@',
            editFileUploadData: '=',
            editFileUploadedDownloadUrl: '@',
            editFileUploadedLabel: '@',
            editDefault: '=',
            editForm: '=',
            editControlName: '@',
            editCollection: '=',
            editKey: '@',
            editRequired: '=',
            editMatch: '=',
            editCustomErrors: '=',
            editDisabled: '=',
            inline: '=',
            hideLabel: '=',
            groupClass: '@',
            labelClass: '@',
            valueClass: '@',
            controlClass: '@',
            buttonClass: '@',
            uiSelectTagging: '=',
            uiSelectLimit: '@',
            editFileTypeDescription: '@'
        },
        templateUrl: 'values/value.phtml',
        replace: true,
        controllerAs:'valueCtrl',
        controller: ['$scope', '$filter', '$timeout', '$rootScope', 'dateFilter', 'uibDateParser', 'Upload', function($scope, $filter, $timeout, $rootScope , dateFilter, dateParser, Upload) {   
            var controller = this;
            
            controller.angular = angular;
            
            if ($rootScope.lang){
                $scope.lang = $rootScope.lang;
            }
            else {
                $scope.lang = 'el';
            }
            
            if ($scope.valueType === 'date'){
                if ($scope.dateFormat){
                    controller.dateFormat = $scope.dateFormat;
                }
                else {
                    controller.dateFormat = 'dd/MM/yyyy HH:mm:ss';
                }
                if ($scope.dateSourceFormat){
                    controller.dateSourceFormat = $scope.dateSourceFormat;
                }
                else {
                    controller.dateSourceFormat = 'yyyy-MM-dd HH:mm:ss';
                }
                if ($scope.editDateOptions && $scope.editDateOptions.maxDate === 'NOW'){
                    $scope.editDateOptions.maxDate = new Date();
                }
                controller.editDateOptions = $scope.editDateOptions;
            }
            
            if ($scope.editDefault){
                controller.edit = true;
            }
            
            controller.computeAge = function(dateString){
                var today = new Date();
                var birthDate = new Date(dateString);
                var age = today.getFullYear() - birthDate.getFullYear();
                var m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) 
                {
                    age--;
                }
                return age;
            };
            
            controller.cfirst = function(value){
                return value.charAt(0).toUpperCase() + value.slice(1);
            };
            
            controller.formatValue = function(value){
                var fvalue = value;
                if ($scope.valueType === 'date'){
                    if (controller.dateFormat === 'years-from' || controller.dateFormat === 'age'){
                        fvalue = controller.computeAge(fvalue);
                    }
                    else {
                        fvalue = dateFilter(dateParser.parse(fvalue, controller.dateSourceFormat), controller.dateFormat);
                    }
                }
                if ($scope.capitalizeFirstLetter){
                    fvalue = controller.cfirst(fvalue);
                }
                
                if ($scope.valueSuffix){
                   fvalue += ' '+$scope.valueSuffix;
                }
                return fvalue;
            };
            
            controller.uiSelectTagging = function(value){
                if ($scope.uiSelectTagging){
                    if ($scope.editCapitalize === 'true'){
                        value = $filter('grUppercase')(value);
                    }
                    for (var i in $scope.valuesMap){
                        if ($filter('grUppercase')(value) === $filter('grUppercase')($scope.valuesMap[i]['value'])){
                            return $scope.valuesMap[i];
                        }
                    }
                    return {key: value, value: value};
                }
                return null;
            };
            
            $scope.$watch('value', function(newValue, oldValue) {
                controller.orignalValue = newValue;
                if ($scope.valuesMap){
                    controller.val = null;
                    for (var i in $scope.valuesMap){
                        if ($scope.valuesMap[i].key === newValue){
                            controller.val = $scope.valuesMap[i];
                            break;
                        }
                    }
                }
                else {
                    controller.val = {value: newValue};
                }
                if ($scope.valueType === 'file'){
                    controller.val.type = 'file';
                    controller.val.label = $scope.fileLinkLabel?$scope.fileLinkLabel:$scope.label;
                    controller.val.target = $scope.fileTarget;
                    controller.val.fileType = $scope.fileType;
                }
                else if (controller.val){
                    controller.val.value = controller.formatValue(controller.val.value);
                }
            });
            
            if ($scope.editable){
                if ($scope.moderated === 'true'){
                    $scope.$watch('pendingEdit', function(newValue, oldValue) {
                       if (newValue){
                           $scope.editable = false;
                       } 
                    });
                }
                $scope.$watch('valueCtrl.edit', function(newValue, oldValue) {
                    if (newValue){
                        if ($scope.valueType === 'date'){
                            controller.editvalue = dateParser.parse(controller.orignalValue, controller.dateSourceFormat);
                        }
                        else if ($scope.valueType === 'file'){
                            if (controller.editValueForm && controller.editControlName){
                                controller.editValueForm[controller.editControlName].$setPristine();
                                controller.editValueForm[controller.editControlName].$setUntouched();
                            }
                            controller.fileUploaded = false;
                            if (controller.val){
                                controller.editvalue = controller.val.value;
                            }
                        }
                        else if ($scope.valueType === 'map' || $scope.valueType === 'ui-select-map'){
                            if (controller.val){
                                controller.editvalue = controller.val.key;
                            }
                        }
                        else if ($scope.valueType === 'ui-select-multiple-map'){
                            if (controller.val && controller.val.value !== ''){
                                if ($scope.uiSelectLimit === '1'){                                
                                    controller.editvalue = [controller.val];
                                }
                                else {
                                    controller.editvalue = controller.val;
                                }
                            }                       
                        }
                        else if (controller.val){
                            controller.editvalue = controller.val.value;
                        }
                    }
                    else {
                        delete controller.editvalue;
                    }
                    controller.editFailure = false;
                    delete controller.editFailureMessage;
                });
                
                $scope.$watch('editPattern', function(newValue){
                    if (newValue){
                        controller.editPattern = new RegExp(newValue);
                    }
                    else {
                        delete controller.editPattern;
                    }
                });
                $scope.$watch('editMatch', function(newValue){
                    if (newValue){
                        controller.editMatch = newValue;
                        if (controller.editvalue && controller.editValueForm && controller.editControlName){
                            controller.editValueForm[controller.editControlName].$setDirty();
                            controller.editValueForm[controller.editControlName].$pristine = false;
                        }
                    }
                    else {
                       delete controller.editMatch;
                    }
                });
                if ($scope.editCapitalize === 'true'){
                    $scope.$watch('valueCtrl.editvalue', function(newValue){
                        controller.editvalue = $filter('grUppercase')(newValue);
                    });
                }
                if ($scope.editCollection && $scope.editKey){
                    $scope.$watch('valueCtrl.editvalue', function(newValue){
                        if (typeof newValue !== 'undefined'){   
                            if ($scope.valueType === 'date'){
                                $scope.editCollection[$scope.editKey] = dateFilter(newValue, controller.dateSourceFormat);
                            }
                            else {
                                $scope.editCollection[$scope.editKey] = newValue;
                            }
                        }
                    });
                    $scope.$watch('editCollection[editKey]', function(newValue){
                        if ($scope.valueType === 'date'){
                            controller.editvalue = dateParser.parse(newValue, controller.dateSourceFormat);
                        }
                        else {
                            controller.editvalue = controller.formatValue(newValue);
                        }
                    });
                }
                 if ($scope.valueType === 'file'){
                    $scope.$watch('valueCtrl.editvalue', function(newValue){
                        if (typeof newValue === 'string'){
                            if (controller.editValueForm){
                                controller.editValueForm.$setPristine();
                            }
                        }
                    });
                }
                
                controller.save = function(){
                    if (typeof $scope.editSaveAction === 'function'){
                        controller.saving = true;
                        controller.savevalue = controller.editvalue;
                        if ($scope.valueType === 'date'){
                            controller.savevalue = dateFilter(controller.savevalue, controller.dateSourceFormat);
                        }
                        else if ($scope.valueType === 'ui-select-map'){
                            controller.savevalue = controller.savevalue.key;
                        }
                        else if ($scope.valueType === 'ui-select-multiple-map'){
                            if ($scope.uiSelectLimit === '1'){
                                controller.savevalue = controller.savevalue[0].key;
                            }
                            else {
                                controller.savevalue = controller.savevalue.key;
                            }
                        }
                        $scope.editSaveAction($scope.editSaveKey, controller.savevalue, 
                        function(){
                            controller.edit = false;
                            controller.editSuccess = true;
                            controller.editFailure = false;
                            $timeout(function(){
                                controller.editSuccess = false;
                            }, 3000);
                        },function(message){
                            controller.editSuccess = false;
                            controller.editFailure = true;
                            if (message){
                                controller.editFailureMessage = message;
                            }
                        }, function(){
                            controller.saving = false;
                        });
                    }
                    else {
                        controller.editSuccess = false;
                        controller.editFailure = true;
                    }
                };
                
                controller.removeFile = function(){
                    controller.editvalue = null;
                    controller.file = null;
                    controller.fileUploaded = false;
                    if (controller.editValueForm && controller.editValueForm[controller.editControlName]){
                        controller.editValueForm[controller.editControlName].$setDirty();
                    }
                    
                };
                
                controller.uploadFile = function(file){
                    var element = $('form[name="'+controller.editValueForm.$name+'"]:visible div.drop-box').first();
                    if (controller.isDuplicateFile(file)){
                        controller.editValueForm[controller.editControlName].$setValidity('duplicate', false);
                    }
                    else {
                        controller.editValueForm[controller.editControlName].$setValidity('duplicate', true);
                    }
                    controller.editValueForm[controller.editControlName].$setValidity('upload', true);
                    if (angular.equals(controller.editValueForm[controller.editControlName].$error, {}) && file !== null){
                        var data = {file: file};
                        for (var i in $scope.editFileUploadData){
                            data[i] = $scope.editFileUploadData[i];
                        }
                        $scope.uploading = true;
                        Upload.upload({
                            url: $scope.editFileUploadUrl,
                            data: data
                        }).then(function (resp) {
                            controller.editValueForm[controller.editControlName].$setValidity('upload', true);
                            controller.editValueForm[controller.editControlName].progress = null;
                            controller.fileUploaded = true;
                            var tmpEditUrl = $scope.editFileUploadedDownloadUrl;
                            var tmpLabel = $scope.editFileUploadedLabel;
                            var tmpEditValue = {};
                            for (var i in resp.data){
                                if(typeof resp.data[i] === 'string'){
                                    tmpEditUrl = tmpEditUrl.replace('[['+i+']]', resp.data[i]);
                                    tmpLabel = tmpLabel.replace('[['+i+']]', resp.data[i]);
                                    tmpEditValue[i] = resp.data[i];
                                }
                            }
                            tmpEditValue.type = 'file';
                            tmpEditValue.value = tmpEditUrl;
                            tmpEditValue.label = tmpLabel;
                            controller.editvalue = tmpEditValue;
                            //controller.uploadedfiles.push(file);
                        }, function (resp) {
                            controller.editValueForm[controller.editControlName].$setValidity('upload', false);
                            controller.editValueForm[controller.editControlName].progress = 0;
                            controller.fileUploaded = false;
                            delete controller.file;
                        }, function (evt) {
                            var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                            controller.editValueForm[controller.editControlName].progress = progressPercentage;
                            controller.editValueForm[controller.editControlName].progress_width = element.width()*progressPercentage/100;
                            $scope.uploading = false;
                        });
                    }
                };
                
                controller.isDuplicateFile = function(file){
                    return false;
                };
                
                $scope.$on('$includeContentLoaded',function(event, src){
                    if ($scope.editForm){
                        controller.editValueForm = $scope.editForm;
                        if ($scope.editControlName){
                            controller.editControlName = $scope.editControlName;
                        }
                        else {
                            controller.editControlName = null;
                        }
                    }
                    else {
                        controller.editValueForm = event.targetScope.editValueForm;
                        if ($scope.valueType === 'file'){
                            controller.editControlName = 'file';
                        }
                        else {
                            controller.editControlName = null;
                        }
                    }
                });
            }
            
            
            
        }]
    };
}]);


