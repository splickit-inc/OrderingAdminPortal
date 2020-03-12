angular.module('adminPortal.valueAddedReseller').controller('VarCreateCtrl', VarCreateCtrl);

function VarCreateCtrl($scope, Users, $timeout, Var) {

    $scope.validation = 1;
    $scope.email_exist = false;

    $scope.new_value_added_reseller = {};

    $scope.success_new_var = "";

    $scope.roles = [];
    $scope.user_brands = [];

    $scope.createNewValueAddedReseller = function () {

        $scope.new_value_added_reseller.submit = true;

        if ($scope.create_value_added_reseller_form.$valid) {
            $scope.new_value_added_reseller.processing = true;

            Var.create($scope.new_value_added_reseller, $scope.new_value_added_reseller).then(function (response) {
                $scope.new_value_added_reseller.success = true;
                $scope.new_value_added_reseller.processing = false;
                $scope.success_new_var = $scope.new_value_added_reseller.name;

                $scope.create_value_added_reseller_form.name.$faded = false;
                $scope.create_value_added_reseller_form.description.$faded = false;

                $scope.new_user = {};
                $scope.submit = false;

                $timeout(function () {
                    $scope.new_value_added_reseller.success = false;
                }, 3500);
            });
        }
    }

}