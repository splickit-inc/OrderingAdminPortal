angular.module('adminPortal.merchant').controller('MerchantOperatorOrderingOnOffCtrl',function(Merchant, $http, SweetAlert, $scope) {
    var vm = this;

    vm.ordering_on = false;
    vm.delivery = false;
    vm.last_call_status = {};


    load();

    vm.changeOrderingOn = changeOrderingOn;
    vm.changeOrderingOnConfirm = changeOrderingOnConfirm;
    vm.changeDelivery = changeDelivery;
    vm.changeDeliveryConfirm = changeDeliveryConfirm;

    function load() {
        $http.get('merchant/operating_on_off').then(function(response) {
            vm.ordering_on = response.data.ordering_on;
            vm.delivery = response.data.delivery;
            vm.last_call_status = response.data.device_call_fail;
        });
    }

    function changeOrderingOnConfirm() {
        var ordering_text = vm.ordering_on ? 'On':'Off';

        if (vm.last_call_status.status && ordering_text == 'On') {
            SweetAlert.swal({
                    title: "Warning.",
                    text: "Your ordering has been shut off due to loss of connection with your printer. The last time your printer had a connection was "+vm.last_call_status.last_call+". Are you sure you want to turn ordering back on?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Confirm"
                },
                function (isConfirm) {
                    if (isConfirm) {
                        changeOrderingOn();
                    }
                    else {
                        vm.ordering_on = !vm.ordering_on;
                    }
                });
        }
        else {
            SweetAlert.swal({
                    title: "Warning.",
                    text: "Are you sure you want to turn ordering  " + ordering_text + "?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Confirm"
                },
                function (isConfirm) {
                    if (isConfirm) {
                        changeOrderingOn();
                    }
                    else {
                        vm.ordering_on = !vm.ordering_on;
                    }
                });
        }

    }

    function changeDeliveryConfirm() {
        var delivery_text = vm.delivery ? 'On':'Off';

        SweetAlert.swal({
                title: "Warning.",
                text: "Are you sure you want to turn delivery  " + delivery_text + "?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function (isConfirm) {
                if (isConfirm) {
                    changeDelivery();
                }
                else {
                    vm.ordering_on = !vm.ordering_on;
                }
            });
    }

    function changeOrderingOn() {
        $http.post('merchant/operating_on_off/ordering', {ordering: vm.ordering_on}).then(function(response) {

        });
    }

    function changeDelivery() {
        $http.post('merchant/operating_on_off/delivery', {delivery: vm.delivery}).then(function(response) {

        });
    }

    $scope.$on('current_merchant:updated', function (event, data) {
        load();
    });

});