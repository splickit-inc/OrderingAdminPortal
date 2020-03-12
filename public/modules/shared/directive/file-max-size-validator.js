/**
 * Created by diego.rodriguez on 9/8/17.
 */
angular
    .module('shared')
    .directive('validSize', checkFileSizeIsValid);

function checkFileSizeIsValid() {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, element, attr, ngModel) {
            // fetch the call address from directives 'checkIfAvailable' attribute
            ngModel.$validators.invalidSize = function (value) {
                var files = element[0].files;
                var maxSize = parseInt(attr.validSize);
                for (var i = 0; i < files.length; i++) {
                    if (files[i].size > maxSize) {
                        return false;
                    }
                }
                return true;
            }
        }
    }
}