(function () {
    'use strict';

    angular
        .module('shared')
        .directive('federalTaxIdValidator', function () {

            return {
                restrict: 'A',
                require: 'ngModel',
                link: function (scope, element, attrs, ngModelCtrl) {
                    function validateInputTax(text) {
                        if (text) {
                            if (text.replace(/\D+/g, '').length > 9) {
                                var invalidLengthInputTax = text.slice(0, -1);
                                ngModelCtrl.$setViewValue(invalidLengthInputTax);
                                ngModelCtrl.$render();
                                return invalidLengthInputTax;
                            }
                            var validTransformedInputTax = text.replace(/\D+/g, '').replace(/(\d\d)(\d+)/, '$1-$2');
                            ngModelCtrl.$setViewValue(validTransformedInputTax);
                            ngModelCtrl.$render();
                            return validTransformedInputTax;
                        }
                        return false;
                    }

                    ngModelCtrl.$parsers.push(validateInputTax);
                }
            };
        });
})();