/**
 * Created by diego.rodriguez on 9/8/17.
 */
angular
    .module('shared')
    .directive('maxFiles', checkMaxFiles);

function checkMaxFiles() {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, element, attr, ngModel) {
            // fetch the call address from directives 'checkIfAvailable' attribute
            ngModel.$validators.maxFiles = function (value) {
                var size = element[0].files.length;
                var max = parseInt(attr.maxFiles);
                return size <= max;
            }
        }
    }
}