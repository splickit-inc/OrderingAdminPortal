angular.module('shared').directive('merchantSelector', function () {
    var controller = function ($scope, UtilityService, EmbeddedMerchantSearch) {

        $scope.merchant_search_service = EmbeddedMerchantSearch;

        $scope.merchant_search_service.selectable_merchants = [];
        $scope.merchant_search_service.selected_merchants = [];

        $scope.propagate_options = [
            {
                code: 'master_only',
                desc: 'Master Menu Only'
            },
            {
                code: 'All',
                desc: 'All Merchants'
            },
            {
                code: 'subset',
                desc: 'Some Merchants'
            },
            {
                code: 'group',
                desc: 'Some Merchants'
            }
        ];

        $scope.selectMerchantOpacityFifty = function () {
            if ($scope.merchant_search_service.propagate_type === 'subset') {
                return '';
            }
            else {
                return 'fifty-opacity';
            }
        };

        $scope.propertyName = 'name';
        $scope.reverse = false;
        $scope.sortBy = function (propertyName) {
            $scope.reverse = ($scope.propertyName === propertyName) ? !$scope.reverse : false;
            $scope.propertyName = propertyName;
        };

        $scope.$watch('autoSearch', function () {
            if (!!$scope.autoSearch && $scope.autoSearch === true) {
                $scope.merchant_search_service.search_url = 'merchant_search';
                $scope.merchant_search_service.merchantSearch();
            }
        });
    };

    return {
        restrict: 'EA',
        replace: true,
        scope: {
            closeMerchantSelect: '&',
            allOption: '&',
            masterOnly: '&',
            propationOptions: '=',
            autoSearch: '=?'
        },
        templateUrl: 'modules/shared/directive/merchant-selector/merchant-selector.html',
        controller: controller,
        link: function (scope, element, attrs, fn) {


        }
    };
});
