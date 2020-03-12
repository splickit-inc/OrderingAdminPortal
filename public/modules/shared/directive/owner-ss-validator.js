(function () {
    'use strict';

    angular
        .module('shared')
        .directive('ownerSsValidator', function () {

            return {
                restrict: 'A',
                require: 'ngModel',
                link: function (scope, element, attrs, ngModelCtrl) {
                    function validateInputSS(text) {
                        if (text) {
                            if (text.replace(/\D+/g, '').length > 9) {
                                var invalidLengthInput = text.slice(0, -1);
                                ngModelCtrl.$setViewValue(invalidLengthInput);
                                ngModelCtrl.$render();
                                return invalidLengthInput;
                            }
                            var validTransformedInput = text.replace(/\D+/g, '').replace(/(\d\d\d)(\d\d)(\d+)/, '$1-$2-$3');
                            ngModelCtrl.$setViewValue(validTransformedInput);
                            ngModelCtrl.$render();
                            return validTransformedInput;
                        }
                        return false;
                    }

                    ngModelCtrl.$parsers.push(validateInputSS);

                }
            };
        });
})();