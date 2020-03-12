angular.module('shared').directive('merchantOperatorSelector', function() {

    var controller = function ($scope, UtilityService, $http, Merchant, Operator, Users) {
        $scope.search = {};
        $scope.search_results = [];
        $scope.search.processing = false;
        $scope.operator_factory = Operator;

        $scope.merchantSearch = function() {
            $scope.search.processing = true;
            $http.post('/merchant_search', {search_text: $scope.search.text}).success(function (response) {
                $scope.search_results = response;
                $scope.search.processing = false;
            });
        }

        $scope.setMerchant = function(merchant) {
            Merchant.setCurrentMerchant(merchant.merchant_id);
            Users.operator_merchant = merchant;
            $("#merchant-operator-select-modal").modal('toggle');
        }

        Operator.getMultiOperatorMerchants();
    };

    return {
        restrict: 'E',
        replace: true,
        scope: {
            closeMerchantSelect: '&',
            selectedMerchantId: '='
        },
        templateUrl: 'modules/shared/directive/merchant-operator-selector/merchant-operator-selector.html',
        controller: controller,
        link: function(scope, element, attrs, fn) {


        }
    };
});
