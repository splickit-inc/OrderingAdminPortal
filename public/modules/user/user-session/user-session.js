/**
 * Created by diego.rodriguez on 10/27/17.
 */
(function () {
    'use strict';

    angular
        .module('adminPortal.user')
        .controller('SessionController', SessionController);

    function SessionController($scope, $location, SweetAlert, UtilityService, Users, Cookie, $localStorage) {

        $scope.isLoggedIn = Users.isLoggedIn();

        $scope.user_service = Users;
        $scope.isUserOperator = isUserOperator;

        $scope.logOut = function () {
            Users.logOut().then(function (response) {
                $scope.isLoggedIn = false;
                $localStorage.$reset();
                $location.path('/');
            }).catch(function (response) {
                $location.path('/');
            });
        };

        function showError(errorMsg) {
            SweetAlert.swal({
                title: "Error",
                html: errorMsg,
                type: "error",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "OK"
            });
        }

        function isUserOperator() {
            return Users.visibility === 'operator';
        }
    }
})();
