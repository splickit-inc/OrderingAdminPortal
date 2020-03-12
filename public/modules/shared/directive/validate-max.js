angular.module('shared').directive('validateMax', function() {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, element, attrs, ctrl, ngModel) {
            var maxValueValidation = function () {
                var max = parseFloat(attrs.max);
                if (max >= parseFloat(ctrl.$modelValue)) {
                    ctrl.$setValidity('invalidMaxValue', true);
                }
                else {
                    ctrl.$setValidity('invalidMaxValue', false);
                }
            };

            element.bind('change', maxValueValidation);
        }
    };
});