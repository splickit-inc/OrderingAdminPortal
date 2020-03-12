angular.module('adminPortal.valueAddedReseller').controller('SideNavValueAddedResellerController', function ($scope, $location, $http) {
    $scope.active={};
    if($location.path() === "/vars/manage")
    {
        $scope.active.manage = true;
    }
    if($location.path() === "/var/create")
    {
        $scope.active.create = true;
    }

    $scope.set_hover = function (name) {
        $scope.active = {};
        $scope.active[name] = true;
    };

    $scope.open = function (name) {

    };
});