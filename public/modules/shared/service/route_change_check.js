angular.module('shared').factory('RouteChangeCheck', function($rootScope, UtilityService){

    var addCheck = function($scope, forms, vm_name){

        var removeListener = $rootScope.$on('$routeChangeStart',
            function (event, toState, toParams, fromState, fromParams) {
                var forms_pristine = true;
                var dirty_forms = [];
                for (var i = 0; i < forms.length; i++) {
                    if($scope[vm_name][forms[i].name].$dirty) {
                        forms_pristine = false;
                        dirty_forms.push(forms[i].desc);
                    }
                }

                if (forms_pristine) { return; }

                var confirm_message;

                if (dirty_forms.length == 1) {
                    confirm_message = "You have unsaved changes to the form "+dirty_forms[0]+". Do you want to continue without saving?";
                }
                else {
                    var dirty_form_list = UtilityService.convertArrayToGrammarList(dirty_forms);
                    confirm_message = "You have unsaved changes to the forms "+dirty_form_list+". Do you want to continue without saving?";
                }

                var can_continue = confirm(confirm_message);

                if(can_continue)
                {
                    return;
                }
                event.preventDefault();
            });

        $scope.$on("$destroy", removeListener);
    };
    return { checkFormOnStateChange : addCheck };
});