angular.module('adminPortal.merchant').controller('MerchantNavBarCtrl', MerchantNavBarCtrl);

function MerchantNavBarCtrl($scope, $location, $http, Merchant, Users) {
    $scope.merchant = null;
    $scope.percent_complete = null;
    $scope.users = Users;

    Users.getUserSessionInfo().then(function() {
        if (Users.hasPermission('op_merch_select')) {
            $scope.selectOperatorMerchant();
        }
    });


    $scope.$on('current_merchant:updated', function (event, data) {
        $scope.merchant = data;
    });

    $scope.$on('percent_complete:updated', function (event, data) {
        $scope.percent_complete = data;
    });

    Merchant.getCurrentMerchant();
    Merchant.getProgressComplete();

    $scope.showMerchant = function () {
        return $location.path().includes("merchant") && !!$scope.merchant;
    }

    $scope.selectOperatorMerchant = function() {
        $("#merchant-operator-select-modal").modal('toggle');
    }
}