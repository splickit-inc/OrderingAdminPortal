angular.module('shared').directive('currencyFormat', function($filter){
    return {
        scope: {
            amount  : '='
        },
        require: 'ngModel',
        link: function(scope, el, attrs, ngModelCtrl){
            if (typeof scope.amount == 'undefined') {
                scope.amount = '0';
            }
            el.val($filter('currency')(scope.amount));

            el.bind('focus', function(){
                el.val(scope.amount);
            });

            el.bind('input', function(){
                scope.amount = el.val();
                scope.$apply();
            });

            el.bind('blur', function(){
                el.val($filter('currency')(scope.amount));
            });

            scope.$watch(attrs['ngModel'], function (v) {
                if (isNaN(scope.amount)) {
                    scope.amount = 0;
                }
                el.val($filter('currency')(scope.amount));
            });

            function fromUser(text) {
                var transformedInput = text.replace(/[^\d+(\.\d{1,2})?$]/g, '');
                if(transformedInput !== text) {
                    ngModelCtrl.$setViewValue(transformedInput);
                    ngModelCtrl.$render();
                }
                return transformedInput;  // or return Number(transformedInput)
            }
            ngModelCtrl.$parsers.push(fromUser);
        }
    }
});