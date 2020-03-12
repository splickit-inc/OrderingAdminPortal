/**
 * Created by diego.rodriguez on 9/8/17.
 */
angular
    .module('shared')
    .directive('validPhone', checkPhoneValid);

function checkPhoneValid() {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, element, attr, ngModel) {
            // fetch the call address from directives 'checkIfAvailable' attribute
            ngModel.$validators.invalidPhone = function (value) {
                if (!!value) {
                    var formatted = value.replace(/[^0-9]/g, '');
                    return formatted.length == 10;
                }
                return false;
            };
        }
    }
}