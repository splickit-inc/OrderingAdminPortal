(function () {
    'use strict';

    angular
        .module('shared')
        .directive('bankRoutingValidator', function () {

            return {
                restrict: 'A',
                require: 'ngModel',
                link: function (scope, element, attrs, ngModelCtrl) {
                    function validateInputRouting(text) {
                        if (text) {
                            if (text.replace(/\D+/g, '').length > 9) {
                                var invalidLengthInputRouting = text.slice(0, -1);
                                ngModelCtrl.$setViewValue(invalidLengthInputRouting);
                                ngModelCtrl.$render();
                                return invalidLengthInputRouting;
                            }
                            var validTransformedInputRouting = text.replace(/\D+/g, '');
                            ngModelCtrl.$setViewValue(validTransformedInputRouting);
                            ngModelCtrl.$render();
                            return validTransformedInputRouting;
                        }
                        return false;
                    }

                    ngModelCtrl.$parsers.push(validateInputRouting);

                }
            };
        });
})();