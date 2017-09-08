/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

angular.module('ktogiasresetpassword', ['alerts'])
.run(['$window', '$templateCache', function($window, $templateCache){
    var serverVars = $window.serverVars['KtogiasLogin\\Controller\\KtogiasLoginAngularController\\ktogias-reset-password-module'];
    if (serverVars.templates) {
        for (var i in serverVars.templates) {
            $templateCache.put(i, serverVars.templates[i]);
        }

    }
}])
.factory('ktogiasresetpasswordService', ['$window', '$http', '$rootScope' ,function($window, $http, $rootScope){
        var serverVars = $window.serverVars['KtogiasLogin\\Controller\\KtogiasLoginAngularController\\ktogias-reset-password-module'];
        function resetPassword(password, success, failure){
            $http.post('/login/ktogias-reset-password-json/reset', {
                password: password, 
                user: serverVars.user,
                lang: $rootScope.lang
            })
            .then(function(response){
                if (response.status === 200){
                    if (response.data.success){
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
            resetPassword: resetPassword
        }; 
}])
.controller('ktogiasresetpasswordController', ['$scope', '$window', '$location','ktogiasresetpasswordService', 'alertsService', function($scope, $window, $location, ktogiasresetpasswordService, alertsService){
    var serverVars = $window.serverVars['KtogiasLogin\\Controller\\KtogiasLoginAngularController\\ktogias-reset-password-module'];
    var controller = this;
    var watchers = [];
    
    controller.errorMessages = {
        NOT_FOUND: 'Ο λογαριασμός δεν βρέθηκε.',
        NOT_ACTIVE: 'Ο λογαριασμός είναι απενεργοποιημένος.',
        EXPIRED: 'Ο σύνδεσμος έχει λήξει.'
    };
    
    controller.user = serverVars.user;
    
    controller.passwordStrengthSuggestions = [];
       
    if (serverVars.error_code){
        controller.error = serverVars.error_code;
        if (controller.error){
            alertsService.add('danger', controller.errorMessages[controller.error]);
        }
    }
    
    controller.clear = function(){
        controller.passwordRepeat = '';
        controller.password = '';
        controller.passwordStrengthSuggestions = [];
        controller.passwordStrengthWarning = '';

        $scope.resetForm.$setPristine();
        $scope.resetForm.$setUntouched();
    };
    
    controller.resetPassword = function(){
        ktogiasresetpasswordService.resetPassword(controller.password, function(data){
            controller.success = true;
            controller.successMessages = data.messages;
        }, function(data){
            for(var i in data.messages){
                alertsService.add('danger',data.messages[i], 10000);
            }
            controller.clear();
        });
    };
    
    controller.goToLoginForm = function(){
        $location.path('/');
    };
    
    controller.checkPaswordStrength = function(){
        var suggestions = {
            'Use a few words, avoid common phrases': 'Χρησιμοποιήστε μερικές λέξεις. Αποφύγετε συνήθεις εκφράσεις.',
            'No need for symbols, digits, or uppercase letters': 'Δεν είναι απαραίτητο να χρησιμοποιήσετε σύμβολα, ψηφία ή κεφαλαία γράμματα',
            'Add another word or two. Uncommon words are better.': 'Προσθέστε ακόμα μία ή δύο λέξεις. Οι σπάνιες λέξεις είναι καλύτερες.',
            'Use a longer keyboard pattern with more turns': 'Χρησιμοποιήστε ένα μεγαλύτερο μοτίβο πλήκτρων με περισσότερες στροφές.',
            'Avoid repeated words and characters': 'Αποφύγετε την επανάληψη λέξεων ή χαρακτήρων.',
            'Avoid sequences': 'Αποφύγετε τις ακολουθίες χαρακτήρων.',
            'Avoid recent years': 'Αποφύγετε πρόσφατες χρονολογίες.',
            'Avoid years that are associated with you': 'Αποφύγετε χρονολογίες που σχετίζονται σε εσάς.',
            'Avoid dates and years that are associated with you': 'Αποφύγετε ημερομηνίες ή χρονολογίες που σχετίζονται με εσάς.',
            'Capitalization doesn\'t help very much': 'Η χρήση κεφαλαίων για το πρώτο γράμμα δεν βοηθάει ιδιαίτερα.',
            'All-uppercase is almost as easy to guess as all-lowercase': 'Είναι το ίδιο εύκολο να μαντέψει κάποιος μια λέξη με όλα τα γραμματα κεφαλαία όσο και με μικρά.',
            'Reversed words aren\'t much harder to guess' : 'Δεν είναι δυσκολότερο να μαντέψει κάποιος ανεστραμμένες λέξεις.',
            'Predictable substitutions like "@" instead of "a" don\'t help very much' : 'Προβλέψιμες αντικαταστάσεις όπως "@" αντί για "a" δεν βοηθάνε ιδιαίτερα.'
        };
        var warnings = {
            'Straight rows of keys are easy to guess': 'Είναι εύκολο να μαντέψει κάποιος ευθείες σειρές από διαδοχικά πλήκτρα.',
            'Short keyboard patterns are easy to guess': 'Είναι εύκολο να μαντέψει κάποιος μικρού μήκους μοτίβα πλήκτρων.',
            'Repeats like "aaa" are easy to guess': 'Είναι εύκολο να μαντέψει κάποιος επαναλήψεις όπως "aaa".',
            'Repeats like "abcabcabc" are only slightly harder to guess than "abc"': 'Είναι ελάχιστα πιο δύσκολο να μαντέψει κάποιος επαναλήψεις του τύπου "abcabcabc" από ότι αυτες του τύπου "abc".',
            'Sequences like abc or 6543 are easy to guess': 'Είναι εύκολο να μαντέψει κάποιος ακολουθίες χαρακτήρων όπως "abc" ή "6543".',
            'Recent years are easy to guess': 'Είναι εύκολο να μαντέψει κάποιος πρόσφατες χρονολογίες.',
            'Dates are often easy to guess': 'Είναι συνήθως εύκολο να μαντέψει κάποιος ημερομηνίες.',
            'This is a top-10 common password': 'Αυτός είναι ένας από τους 10 πιο συνηθισμένους κωδικούς.',
            'This is a top-100 common password': 'Αυτός είναι ένας από τους 100 πιο συνηθισμένους κωδικούς.',
            'This is a very common password': 'Αυτός είναι ένας πολύ συνηθισμένους κωδικός.',
            'This is similar to a commonly used password': 'Αυτός ο κωδικός μοιάζει πολύ με έναν συνηθισμένο κωδικό.',
            'A word by itself is easy to guess': 'Είναι εύκολο να μαντέψει κάποιο μια μόνο λέξη.',
            'Names and surnames by themselves are easy to guess': 'Είναι εύκολο να μαντέψει κανείς μόνο ονόματα ή επώνυμα.',
            'Common names and surnames are easy to guess': 'Είναι εύκολο να μαντέψει κανείς συνηθισμένα ονόματα και επώνυμα.'
        };
        
        var result = zxcvbn(controller.password);
        controller.passwordStrengthSuggestions = [];
        controller.passwordStrengthWarning = '';
        if (result.score >= serverVars.config['min-allowed-password-score']){
            $scope.resetForm.password.$setValidity('passwordStrength', true);
        }
        else {
            $scope.resetForm.password.$setValidity('passwordStrength', false);
            if (result.feedback.warning in warnings){
                controller.passwordStrengthWarning = warnings[result.feedback.warning];
            }
            else {
                controller.passwordStrengthWarning = result.feedback.warning;
            }
            for (var i in result.feedback.suggestions){
                if (result.feedback.suggestions[i] in suggestions 
                        && controller.passwordStrengthSuggestions.indexOf(suggestions[result.feedback.suggestions[i]]) === -1){
                    controller.passwordStrengthSuggestions.push(suggestions[result.feedback.suggestions[i]]);
                }
                else if (controller.passwordStrengthSuggestions.indexOf(result.feedback.suggestions[i]) === -1){
                    controller.passwordStrengthSuggestions.push(result.feedback.suggestions[i]);
                }
            }
        }
    };
    
    watchers.push(
        $scope.$watch('controller.password', function(newVal, oldVal) {
            if (newVal){
                controller.checkPaswordStrength();
            }
        })
    );
    
    watchers.push(
        $scope.$on('$destroy', function(){
           for (var w in watchers){
               watchers[w]();
           }
        })
    );
}])
;