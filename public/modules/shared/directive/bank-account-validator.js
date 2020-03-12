(function () {
    'use strict';

    angular
        .module('shared')
        .directive('bankAccountValidator', function () {

            return {
                restrict: 'A',
                require: 'ngModel',
                link: function (scope, element, attrs, ngModelCtrl) {
                    function validateInputAccount(text) {
                        if (text) {
                            if (text.replace(/\D+/g, '').length > 20) {
                                var invalidLengthInputAccount = text.slice(0, -1);
                                ngModelCtrl.$setViewValue(invalidLengthInputAccount);
                                ngModelCtrl.$render();
                                return invalidLengthInputAccount;
                            }
                            var validTransformedInputAccount = text.replace(/\D+/g, '');
                            ngModelCtrl.$setViewValue(validTransformedInputAccount);
                            ngModelCtrl.$render();
                            return validTransformedInputAccount;
                        }
                        return false;
                    }

                    ngModelCtrl.$parsers.push(validateInputAccount);

                }
            };
        });
})();