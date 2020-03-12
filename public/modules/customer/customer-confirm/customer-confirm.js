angular.module('adminPortal.customer').controller('CustomerConfirmCtrl', CustomerConfirmCtrl);

function CustomerConfirmCtrl($scope,$rootScope, $window, $routeParams, SweetAlert, Leads, Prospects, UtilityService) {
    var vm = this;
    vm.initData = initData;

    vm.customerPlan = {}; // patch offer id until they realize how to do it
    vm.form = {};
    vm.processing = true;
    vm.validGuid = false;
    vm.months = [];
    vm.years = [];
    vm.guid = null;

    vm.create = create;

    initData();

    ///// Methods
    function initData() {
        vm.guid = $routeParams.guid;
        validateGuid();
        fillTimeData();
    }

    function fillTimeData() {
        for (var i = 1; i < 13; i++) {
            vm.months.push(i);
        }
        var d = new Date();
        var n = d.getFullYear();
        for (i = n - 1; i < n + 16; i++) {
            vm.years.push(i);
        }
    }

    function validateGuid() {
        Leads.getLead(vm.guid).then(function (response) {
            vm.processing = false;
            if (!!response.data.first_form_completion) {
                showError("This link is no longer valid. The customer was already processed!");
                return;
            }
            vm.validGuid = true;
        }).catch(function () {
            vm.processing = false;
            showError("Invalid customer id. Please verify you are accessing the link that was" +
                " provided in the email!");
        });
    }

    function create() {
        vm.customerPlan.submit = true;
        hideKeyboard();
        if (!vm.form.$valid) {
            showError('Please properly complete the form before submitting.');
            return;
        }
        vm.processing = true;
        vm.stripeGenerateToken().then(function (result) {
            vm.customerPlan.token = result.token;
            Prospects.createMerchant(vm.guid, vm.customerPlan).then(function (response) {
                $rootScope.safeApply(function () {
                    vm.processing = false;
                });
                var amount = response.data.charge.amount;
                amount = amount/100;
                SweetAlert.swal({
                        title: "Success!",
                        html: "Thank you for signing up for our online services. You paid: $"+amount+". Further instructions were" +
                        " sent to your provided email address.",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#488214",
                        confirmButtonText: "OK"
                    },
                    function () {
                        $window.location.href = 'http://yourbiz.com/';
                    });

            }).catch(function (response) {
                console.log(response);
                vm.processing = false;
                var errors = "An unexpected error occurred! Please try again later.";
                if (response.status == 422) {
                    errors = UtilityService.formatErrors(response.data.errors);
                }
                if(response.status == 402)
                {
                    SweetAlert.swal({
                        title: "Your payment was declined",
                        html: response.data.message,
                        type: "error",
                        showCancelButton: false,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "OK"
                    });
                    return;
                }
                showError(errors);
            });
        }).catch(function (error) {
            $rootScope.safeApply(function () {
                vm.processing = false;
            });
            showError(error.message);
        });
    }

    function hideKeyboard(){
        var inviInput = $('#inviInput')[0];
        inviInput.focus();
        inviInput.blur();
    }

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
}
