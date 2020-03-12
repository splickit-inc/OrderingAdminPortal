angular.module('adminPortal.merchant').controller('MerchantSearchCtrl', MerchantSearchCtrl);

function MerchantSearchCtrl($http, $scope, SearchResults, $location, UtilityService, Merchant, $translate) {
    $scope.search_results = SearchResults;

    $scope.formatPhone = function (phone_no) {
        return UtilityService.formatPhone(phone_no);
    }

    $scope.viewMerchant = function (merchant) {
        Merchant.setCurrentMerchant(merchant.merchant_id).then(function (response) {
            $location.path('/merchant/general_info');
        });
    }
}