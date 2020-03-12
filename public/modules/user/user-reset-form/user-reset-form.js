angular.module('adminPortal.user').controller('UserResetFormCtrl',function(UserReset, $routeParams, SweetAlert, $location){
    var vm = this;


    vm.email = "";
    vm.password = "";
    vm.submit_errors = false;
    vm.processing = false;
    vm.errors = {};
    vm.token = $routeParams.token;
    vm.method = $routeParams.method;
    vm.password_confirmation = "";
    vm.submitResetPassword = submitResetPassword;

    function submitResetPassword() {
        if (vm.form.$valid === false || !vm.email) {
            vm.submit_errors = true;
            return;
        }
        vm.processing = true;
        vm.errors = {};
        UserReset.postResetParams(vm.email, vm.password, vm.password_confirmation, vm.token).success(function (result) {
            vm.processing = false;
            vm.submit_errors = false;
            if(vm.method === 'create')
            {
                result = 'Your account is ready to use.';
            }
            SweetAlert.swal({
                    title: "Success.",
                    html: result,
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#488214",
                    confirmButtonText: "OK"
                },
                function () {
                    $location.path("/");
                });
        }).catch(function (response) {
            vm.errors = response.data;
            vm.processing = false;
            vm.submit_errors = false;
        });
    }
});