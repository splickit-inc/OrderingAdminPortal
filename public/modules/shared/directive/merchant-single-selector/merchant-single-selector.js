angular.module('shared').directive('merchantSingleSelector', function() {

    var controller = function ($scope, UtilityService, $http, Menu) {
        $scope.search = {};
        $scope.search_results = [];
        $scope.search.processing = false;

        $scope.merchantSearch = function() {
            $scope.search.processing = true;
            if(!!$scope.searchFunction)
            {
               $scope.searchFunction($scope.search.text).success(function (result) {

                   $scope.search_results = result;
                   $scope.search.processing = false;
               });
               return;
            }
            $http.post('/merchant_search', {search_text: $scope.search.text}).success(function (response) {
                $scope.search_results = response;
                $scope.search.processing = false;
            });
        };

        $scope.setMerchant = function(merchant) {
            Menu.quick_edit_merchant_id = merchant.merchant_id;
            Menu.quick_edit_merchant = merchant;
            $scope.closeMerchantSelect();
        };

        if($scope.autoSubmit)
        {
            if ($scope.autoSearchVariable) {
                $scope.merchantSearch($scope.autoSearchVariable);
            }
            else {
                $scope.merchantSearch();
            }
        }
    };

    return {
        restrict: 'E',
        replace: true,
        scope: {
            closeMerchantSelect: '&',
            selectedMerchantId: '=',
            //This parameter are optional
            searchFunction: '=?',
            autoSubmit: '=?',
            autoSearchVariable: '=?'
        },
        templateUrl: 'modules/shared/directive/merchant-single-selector/merchant-single-selector.html',
        controller: controller,
        link: function(scope, element, attrs, fn) {

        }
    };
});
