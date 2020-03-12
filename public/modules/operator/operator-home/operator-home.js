angular.module('adminPortal.operator').controller('OperatorHomeCtrl', OperatorHomeCtrl);

function OperatorHomeCtrl($http, Order, $timeout, Users, $location, UtilityService, Operator, $scope) {
    var vm = this;

    vm.current_day = {};
    vm.new_returning_data = [];
    vm.new_returning_labels = [];

    vm.weekly_sales_data = [];
    vm.weekly_sales_labels = [];

    vm.daily_summary_data = [];
    vm.refund_amount_max = 0;

    vm.refund_order = {};
    vm.refund_order.employee_name = Users.getUserName();

    vm.loading_page = true;

    vm.showDailyOrders = showDailyOrders;
    vm.openRefundOrder = openRefundOrder;
    vm.refundOrder = refundOrder;

    var refund_order_index;
    var order_day_index;

    function resetForm() {
        vm.refund_order = {};
        vm.refund_order_form.$setPristine();
        vm.refund_order.employee_name = Users.getUserName();

        vm.daily_summary_data[order_day_index]['orders'][refund_order_index]['refund_success'] = false;
    }

    function load() {
        if (!Users.hasPermission('home_nav')) {
            $location.path('operator/order_management');
        }

        vm.loading_orders = true;
        $http.get('/operator/home_reporting').success(function (response) {
            vm.loading_page = false;

            vm.current_day = response.current_day_data;
            console.log(response.device_types);
            vm.device_type_data = response.device_types.chart_values;
            vm.device_type_labels = response.device_types.labels;

            vm.weekly_sales_data = response.weekday_sales.data;
            vm.weekly_sales_labels = response.weekday_sales.labels;

            vm.daily_summary_data = response.daily_data;
        });
    }

    function showDailyOrders(index, day_date) {
        var data = {day_date: day_date};

        if (!vm.daily_summary_data[index]['orders_loaded']) {
            $http.post('/operator/daily_summary_orders', data).success(function (response) {
                vm.daily_summary_data[index]['orders'] = response;
                vm.daily_summary_data[index]['show_orders'] = true;
                vm.daily_summary_data[index]['orders_loaded'] = true;
            });
        }
        else {
            vm.daily_summary_data[index]['show_orders'] = !vm.daily_summary_data[index]['show_orders'];
        }
    }

    function openRefundOrder(order, order_day_index_dom, refund_order_index_dom) {
        refund_order_index = refund_order_index_dom;
        order_day_index = order_day_index_dom;

        vm.current_order = order;
        vm.refund_order.refund_amount = order.grand_total;
        vm.refund_amount_max = order.grand_total;

        $http.post('/operator/set_refund_order', vm.current_order).success(function (response) {
            $("#refund-order-modal").modal('toggle');
        });
    }

    function refundOrder() {
        vm.refund_order.user_id = vm.current_order.user_id;
        vm.refund_order.submit = true;
        vm.refund_order.order_id = vm.current_order.order_id;
        if (vm.refund_order_form.$valid) {
            Order.refundOrder(vm.refund_order).then(function (response) {
                if (response.data.error) {
                    vm.refund_error = true;
                    vm.refund_error_message = response.data.smaw_response.error_message;
                }
                else {
                    $("#refund-order-modal").modal('toggle');
                    vm.refund_order.success = true;
                    $timeout(resetForm, 3500);
                }
            });
        }
    }

    load();

    /**
     * Get order Details when the user click on an order
     *
     */
    vm.order_detail_loading = false;
    vm.order_detail = {};
    vm.viewOrder = function (order) {
        vm.order_detail_loading = true;
        $("#order-detail-modal").modal('show');
        Order.setCurrentOrder(order.order_id).success(function (response) {
            Order.current_order = order;
            Order.get('order_detail').success(function (order) {
                vm.order_detail = order;
                vm.order_detail_loading = false;
            }).catch(function (error) {
                vm.order_detail_loading = false;
            });
        }).catch(function (error) {
            vm.order_detail_loading = false;
        });
    };

    vm.formatPhone = function (phone_no) {
        return UtilityService.formatPhone(phone_no);
    };

    $scope.$on('current_merchant:updated', function (event, data) {
        load();
    });
}
