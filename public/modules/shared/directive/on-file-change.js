/**
 * Created by diego.rodriguez on 10/9/17.
 */
angular
    .module('shared')
    .directive('onFileChange', onFileChanged);

function onFileChanged() {
    return {
        priority: 0,
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, element, attrs, ngModel) {
            var onChangeFunc = scope.$eval(attrs.onFileChange);
            element.bind('change', function () {
                scope.safeApply(function () {
                    var files = element[0].files;
                    var fileArray = [];
                    for (var i = 0; i < files.length; i++) {
                        fileArray.push(files[i]);
                    }
                    ngModel.$setViewValue(fileArray);
                    ngModel.$render();
                    if (!!files.length) { //IE11 patch
                        onChangeFunc();
                    }
                });
            });
        }
    }
}