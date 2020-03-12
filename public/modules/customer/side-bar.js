angular.module('adminPortal.customer').controller('SideNavCustomerController', function ($scope, $location, $http) {
    $scope.active={};
    if($location.path() === "/customer/create")
    {
        $scope.active.customer_service = true;
    }
    if($location.path() === "/customer/sales_report")
    {
        $scope.active.report = true;
    }

    $scope.set_hover = function (name) {
        $scope.active = {};
        $scope.active[name] = true;
    };

    $scope.open = function (name) {

    };
});