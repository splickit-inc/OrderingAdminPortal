angular.module('adminPortal.customerService').controller('FutureOrdersCtrl', FutureOrdersCtrl);


function FutureOrdersCtrl(SweetAlert, UtilityService, $http, Order, $location) {
    var vm = this;

    vm.processing = false;

    vm.fieldNames = {
        order_id: {columnName: 'Order ID', class: 'fix-table-wrap'},
        first_name: {columnName: 'First', class: 'text-wrap-word-break'},
        last_name: {columnName: 'Last', class: 'text-wrap-word-break'},
        merchant_id: {columnName: 'Merchant ID'},
        name: {columnName: 'Merchant Name', class: 'text-wrap-word-break'},
        address1: {columnName: 'Address'},
        order_qty: {columnName: 'Qty'},
        grand_total: {columnName: 'Total'},
        cash: {columnName: 'In Store Payment'},
        order_type_complete: {columnName: 'Order Type'},
        order_dt_tm: {columnName: 'Order Date'},
        pickup_dt_tm: {columnName: 'Due Date'}
    };

    vm.searchParams = {
        search_text: ''
    };
    vm.recently_visited_orders = [];

    vm.viewOrder = viewOrder;
    vm.refresh = refresh;
    vm.searchOrders = searchOrders;
    vm.hasSearchResult = hasSearchResult;

    load();

    function load() {
        vm.processing = true;
        $http.get('/future_orders/recently_visited').success(function (response) {
            vm.recently_visited_orders = response;
        }).error(function (response, status) {
            console.log(response);
        });
    }

    function refresh() {
        getRequest('');
    }

    function getRequest(text) {
        var new_params = {
            search_text: text
        };
        vm.searchParams = UtilityService.mergeObject(vm.searchParams, new_params);
    }

    function viewOrder(order) {
        $http.post('/order/set_current', {order_id: order.order_id}).success(function (response) {
            Order.current_order = order;
            $location.path('/customer_service/order');
        });
    }

    function searchOrders() {
        getRequest(vm.search_text);
    }

    function hasSearchResult() {
        if (vm.processing || vm.orders.length > 0) {
            return "hide-recently-visited-panel";
        }
        return "";
    }
}
