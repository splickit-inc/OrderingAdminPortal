angular.module('adminPortal.user').controller('UserLoginCtrl', UserLoginCtrl);

function UserLoginCtrl($scope, Users, $location, $localStorage, Cookie) {
    $scope.login_failed = false;
    $scope.login_processing = false;

    $localStorage.$reset();
    Cookie.deleteCookie('user_data');

    $scope.logonAttempt = function () {
        $scope.login_processing = true;
        $scope.login_failed = false;
        Users.logOn($scope.login).then(function (response) {
            if (response.data.status === 1) {
                Users.permissions = response.data.permissions;
                Users.operator_merchant_count = response.data.operator_merchant_count;
                var visibility = response.data.user.visibility;
                Users.getUserSessionInfo().then(function (response) {
                    if (visibility === 'operator') {
                        $location.path("operator/home");
                    }
                    else {
                        window.location = "/";
                    }
                });
            }
            else {
                $scope.login_failed = true;
                $scope.login_processing = false;
            }
        }).catch(function (response) {
            $scope.login_failed = true;
            $scope.login_processing = false;
        });
    }
}