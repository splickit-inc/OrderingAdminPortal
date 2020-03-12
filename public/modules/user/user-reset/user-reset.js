angular.module('adminPortal.user').controller('UserResetCtrl', function (UserReset, SweetAlert, $location) {
    var vm = this;


    vm.email = "";
    vm.submit_errors = false;
    vm.processing = false;
    vm.errors = {};
    vm.submitResetPassword = submitResetPassword;

    function submitResetPassword() {
        if (vm.form.$valid === false || !vm.email) {
            vm.submit_errors = true;
            return;
        }
        vm.processing = true;
        vm.errors = {};
        UserReset.postEmailToReset(vm.email).success(function (result) {
            vm.processing = false;
            vm.submit_errors = false;
            SweetAlert.swal({
                    title: "The request was accepted.",
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
            vm.errors = response.data.errors;
            vm.processing = false;
            vm.submit_errors = false;
        });
    }
});