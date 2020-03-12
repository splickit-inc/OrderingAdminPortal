angular.module('shared').directive('lookupSelector', function() {
    var controller = function ($scope, BrandLookup) {
        $scope.brand_lookup = BrandLookup;

        $scope.setLookupValues = function() {
            BrandLookup.setLookupValues();
        }
    };

    return {
        restrict: 'E',
        replace: true,
        scope: {

        },
        templateUrl: 'modules/shared/directive/lookup-selector/lookup-selector.html',
        controller: controller,
        link: function(scope, element, attrs, fn) {


        }
    };
});
