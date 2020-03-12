angular.module('adminPortal.menu').directive('propagationSelect', function () {
    var controller = function ($scope, UtilityService, EmbeddedMerchantSearch, MerchantGroup) {

        $scope.merchant_search_service = EmbeddedMerchantSearch;
        $scope.group_select_service = MerchantGroup;

        $scope.merchant_search_service.selectable_merchants = [];
        $scope.merchant_search_service.selected_merchants = [];
        $scope.error = false;
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
                desc: 'Merchant Group'
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

        $scope.closeMerchantSelectValidator = function (event) {
            if ($scope.merchant_search_service.propagate_type === 'subset') {
                if ($scope.merchant_search_service.selected_merchants.length === 0) {
                    $scope.error = true;
                    event.stopPropagation();
                    return;
                }
            }
            if ($scope.merchant_search_service.propagate_type === 'group') {
                if (!$scope.group_select_service.selected_merchant_group) {
                    $scope.error = true;
                    event.stopPropagation();
                    return;
                }
            }
            $scope.error = false;
            $scope.closeMerchantSelect();
        };

        if (!!$scope.autoSearch) {
            $scope.merchant_search_service.search_text = "";
            $scope.merchant_search_service.merchantSearch();
            $scope.group_select_service.search_text = "";
            $scope.group_select_service.merchantGroupSearch();
        }

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
            closeMerchantSelect: '&',
            allOption: '&',
            masterOnly: '&',
            propationOptions: '=',
            autoSearch: '=?'
        },
        templateUrl: 'modules/menu/directive/propagation-select/propagation-select.html',
        controller: controller,
        link: function (scope, element, attrs, fn) {


        }
    };
});
