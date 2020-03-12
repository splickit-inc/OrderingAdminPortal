/**
 * Created by diego.rodriguez on 9/8/17.
 */
angular
    .module('shared')
    .directive('validType', checkFileTypeIsValid);

function checkFileTypeIsValid() {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, element, attr, ngModel) {
            // fetch the call address from directives 'checkIfAvailable' attribute
            ngModel.$validators.invalidType = function (value) {
                var files = element[0].files;
                var validTypes = element[0].accept.split(',');
                for (var i = 0; i < files.length; i++) {
                    if (validTypes.indexOf(files[i].type) == -1) {
                        return false;
                    }
                }
                return true;
            }
        }
    }
}