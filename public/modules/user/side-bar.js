angular.module('adminPortal.user').controller('SideNavUserController', function ($scope, $location, $http) {
    $scope.active={};
    if($location.path() === "/user/create")
    {
        $scope.active.user_create = true;
    }
    if($location.path() === "/users/manage")
    {
        $scope.active.user_manage = true;
    }

    $scope.set_hover = function (name) {
        $scope.active = {};
        $scope.active[name] = true;
    };

    $scope.open = function (name) {

    };
});