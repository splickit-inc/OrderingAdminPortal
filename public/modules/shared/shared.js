angular.module('shared', ['ui.bootstrap', 'ngRoute', 'ngAnimate']);

angular.module('shared').config(function ($routeProvider) {


}).directive('match', function ($parse) {
    return {
        require: 'ngModel',
        link: function (scope, elem, attrs, ctrl) {
            scope.$watch(function () {
                return $parse(attrs.match)(scope) === ctrl.$modelValue;
            }, function (currentValue) {
                ctrl.$setValidity('mismatch', currentValue);
            });
        }
    }
}).directive('ngFocus', [function () {
    var FOCUS_CLASS = "ng-focused";
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, element, attrs, ctrl) {
            ctrl.$focused = false;
            element.bind('focus', function (evt) {
                element.addClass(FOCUS_CLASS);
                scope.$apply(function () {
                    ctrl.$focused = true;
                });
            }).bind('blur', function (evt) {
                element.removeClass(FOCUS_CLASS);
                scope.$apply(function () {
                    ctrl.$focused = false;
                });
            });
        }
    }
}]).directive('ngFade', [function () {
    var FADE_CLASS = "ng-faded";
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, element, attrs, ctrl) {
            ctrl.$focused = false;
            element.bind('focus', function (evt) {
                element.addClass(FADE_CLASS);
                scope.$apply(function () {
                    ctrl.$faded = false;
                });
            }).bind('blur', function (evt) {
                element.removeClass(FADE_CLASS);
                scope.$apply(function () {
                    ctrl.$faded = true;
                });
            }).bind('pristine', function (evt) {
                element.addClass(FADE_CLASS);
                scope.$apply(function () {
                    ctrl.$faded = false;
                });
            });
        }
    }
}]).directive("hideOnScroll", function ($animate, $document) {
    return function (scope, element, attrs) {
        var onScroll = function () {
            scope.$apply(function () {
                if ($document.scrollTop() + 165 > element.offset().top) {
                    $animate.addClass(element, "fade");
                } else {
                    $animate.removeClass(element, "fade");
                }
            });
        };

        $document.bind('scroll', onScroll);

        scope.$on('$destroy', function () {
            $document.unbind('scroll', onScroll);
        });
    };
}).directive('preventEnterFireing', function () {
    return {
        link: function (scope, element, attrs) {
            element.keypress(function (e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    return;
                }
            });
        }
    }
}).directive('focusMe', function ($timeout, $parse) {
    return {
        //scope: true,   // optionally create a child scope
        link: function (scope, element, attrs) {
            var model = $parse(attrs.focusMe);
            scope.$watch(model, function (value) {

                if (value === true) {
                    $timeout(function () {
                        element[0].focus();
                    });
                }
            });
            // to address @blesh's comment, set attribute value to 'false'
            // on blur event:
            element.bind('blur', function () {

                scope.$apply(model.assign(scope, false));
            });
        }
    };
}).animation(".fade", function () {
    return {
        addClass: function (element, className) {
            //TweenMax.to(element, 1, {opacity: 0});
        },
        removeClass: function (element, className) {
            //TweenMax.to(element, 1, {opacity: 1});
        }
    };
});