angular.module('adminPortal.customerService').controller('CustomerServiceOrderCtrl', CustomerServiceOrderCtrl);

function CustomerServiceOrderCtrl(Order, $timeout, UtilityService, $http, $location, Merchant, SweetAlert) {
    var vm = this;
    vm.current_order = {};

    vm.resend_order = {};
    // vm.reassign_order = {};
    vm.change_status_order = {};
    vm.refund_order = {};

    vm.change_status_message = {};
    vm.merchant_message_history = [];

    vm.order_detail = {};

    vm.order_detail_loading = true;
    vm.refund_error = false;
    vm.refund_error_message = '';
    vm.merchant_message_response = '';

    load();

    vm.resendOrder = resendOrder;
    // vm.reassignOrder = reassignOrder;
    vm.refundOrder = refundOrder;
    vm.changeOrderStatus = changeOrderStatus;
    vm.getOrderDetail = getOrderDetail;
    vm.formatPhone = formatPhone;
    vm.viewUser = viewUser;
    vm.viewMerchant = viewMerchant;
    vm.viewMerchantMessageResponse = viewMerchantMessageResponse;

    vm.statuses = [{
        id: "E",
        name: "Executed"
    },
        {
            id: "C",
            name: "Cancelled"
        },
        {
            id: "G",
            name: "G Status"
        },
        {
            id: "T",
            name: "T Status"
        },
        {
            id: "N",
            name: "N Status"
        },
        {
            id: "Y",
            name: "Y Status"
        }];

    function load() {
        Order.index().then(function (response) {
            vm.current_order = response.data.order;
            vm.merchant_message_history = response.data.merchant_message_history;
            vm.refund_order.refund_amount = vm.current_order.grand_total;
        });
    }

    function resetForm() {
        vm.resend_order = {};
        vm.resend_order_form.$setPristine();
        vm.reassign_order = {};
        vm.reassign_order_form.$setPristine();
        vm.refund_order = {};
        vm.refund_order_form.$setPristine();
        vm.change_status_order = {};
        vm.change_order_status_form.$setPristine();
    }

    function resendOrder() {
        vm.resend_order.submit = true;
        if (vm.resend_order_form.$valid) {
            Order.post('resend_order', {new_destination_address: vm.resend_order.new_destination_address}).then(function () {
                $("#resend-order-modal").modal('toggle');
                vm.resend_order.success = true;
                $timeout(resetForm, 3500);
            });
        }
    }

    // function reassignOrder() {
    //     vm.reassign_order.submit = true;
    //     if (vm.reassign_order_form.$valid) {
    //         Order.post('reassign_order', {new_merchant_id: vm.reassign_order.merchant_id}).then(function () {
    //             $("#reassign-order-modal").modal('toggle');
    //             vm.reassign_order.success = true;
    //             $timeout(resetForm, 3500);
    //         });
    //
    //     }
    // }

    function refundOrder() {
        vm.refund_order.user_id = vm.current_order.user_id;
        vm.refund_order.submit = true;
        if (vm.refund_order_form.$valid) {
            vm.refund_error = false;
            Order.refundOrder(vm.refund_order).then(function(response) {
                if (response.data.error) {
                    vm.refund_error = true;
                    vm.refund_error_message = response.data.smaw_response.error_message;
                }
                else {
                    $("#refund-order-modal").modal('toggle');
                    vm.refund_order.success = true;
                    vm.current_order.refund_note = vm.refund_order.note;

                    SweetAlert.swal({
                            title: "Order has been refunded!",
                            type: "success",
                            showCancelButton: false,
                            confirmButtonColor: "#488214",
                            confirmButtonText: "OK"
                        });

                    $timeout(resetForm, 3500);
                }
            });
        }
    }

    function changeOrderStatus() {
        vm.change_status_order.submit = true;
        if (vm.change_order_status_form.$valid) {
            Order.post('change_order_status', {new_status: vm.change_status_order.new_status}).then(function (response) {
                vm.change_status_message = response.data.message;
                $("#send-now-modal").modal('toggle');
                vm.change_status_order.success = true;
                $timeout(resetForm, 3500);
            });
        }
    }

    function getOrderDetail() {
        if (vm.order_detail_loading) {
            Order.get('order_detail').then(function (response) {
                vm.order_detail = response.data;
                vm.order_detail_loading = false;
            });
        }
    }

    function formatPhone(phone_no) {
        return UtilityService.formatPhone(phone_no);
    }

    function viewUser(user) {
        $http.post('/customer_service/set_current_user', {user_id: user.user_id}).success(function (response) {
            $location.path('/customer_service/user');
        });
    }

    function viewMerchant(merchant) {
        Merchant.setCurrentMerchant(merchant.merchant_id).then(function (response) {
            $location.path('/merchant/general_info');
        });
    }

    function viewMerchantMessageResponse(response) {
        vm.merchant_message_response = response;
        $("#merchant-message-response-modal").modal('show');
    }
}
