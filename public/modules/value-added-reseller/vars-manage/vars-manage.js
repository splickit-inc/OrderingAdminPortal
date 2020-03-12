angular.module('adminPortal.valueAddedReseller').controller('VarsManageCtrl', VarsManageCtrl);

function VarsManageCtrl($scope, Users, SweetAlert, Var) {

    $scope.vars = [];

    //Load the Users Table
    Var.getAllVars().then(function (response) {
        $scope.vars = response.data;
    });

    $scope.deleteVar = function (value_added_reseller, index) {
        SweetAlert.swal({
                title: "Are you sure you want to remove the value added reseller " + value_added_reseller.name + "?",
                // text: "This will update the live, production version of your site that real customers see.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Remove"
            },
            function (isConfirm) {
                if (isConfirm) {
                    deleteVar(value_added_reseller, index);
                }
            });
    }

    function deleteVar(value_added_reseller, index) {
        Var.delete(value_added_reseller.id).then(function (response) {
            $scope.vars.splice(index, 1);
        });
    }

}