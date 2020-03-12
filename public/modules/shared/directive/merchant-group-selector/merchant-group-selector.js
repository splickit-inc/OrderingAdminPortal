angular.module('shared').directive('merchantGroupSelector', function() {
    var controller = function ($scope, $http, MerchantGroup) {
        $scope.group_select_service = MerchantGroup;
        MerchantGroup.merchantGroupSearch();

        $scope.propertyName = 'name';
        $scope.reverse = false;
        $scope.sortBy = function (propertyName) {
            $scope.reverse = ($scope.propertyName === propertyName) ? !$scope.reverse : false;
            $scope.propertyName = propertyName;
        };
    };

    return {
        restrict: 'EA',
        replace: true,
        scope: {
            closeMerchantGroupSelect: '&',
        },
        templateUrl: 'modules/shared/directive/merchant-group-selector/merchant-group-selector.html',
        controller: controller,
        link: function(scope, element, attrs, fn) {


        }
    };
});
