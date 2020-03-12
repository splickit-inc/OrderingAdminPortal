/**
 * Created by boneill on 12/19/17.
 */
/**
 * Created by diego.rodriguez on 10/9/17.
 */
angular
    .module('shared')
    .directive('ngEnter', ngEnter);

function ngEnter() {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, element, attrs) {
            element.bind("keydown keypress", function (event) {
                if (event.which === 13) {
                    scope.$apply(function () {
                        scope.$eval(attrs.ngEnter);
                    });

                    event.preventDefault();
                }
            });
        }
    }
}