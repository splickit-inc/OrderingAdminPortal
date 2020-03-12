angular.module('adminPortal.customerService').controller('CustomerServiceCtrl', CustomerServiceCtrl);

function CustomerServiceCtrl($http, UtilityService, $location, Order, $localStorage) {
    var vm = this;

    vm.search = {};
    vm.search.past_seven_days = false;
    vm.search.text = '';
    vm.search.processing = false;
    vm.search.text = "";

    vm.result_orders = Order.getSearchResults();
    vm.result_orders = [];
    vm.recently_visited_orders = [];
    vm.total_orders_search_count = 0;

    vm.first_search = false;
    vm.loading = true;
    vm.loading_offset_dataset = false;

    vm.searchParams = undefined;
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
        status: {columnName: 'Order Status'},
        order_dt_tm: {columnName: 'Order Date'}
    };

    vm.searchOrders = searchOrders;
    vm.viewOrder = viewOrder;
    vm.fiftyOpacityOffset = fiftyOpacityOffset;
    vm.hasSearchResult = hasSearchResult;

    load();

    function load() {
        $http.get('/customer_service/index').success(function (response) {
            vm.recently_visited_orders = response.recently_visited_orders;
            vm.last_25_orders = response.last_25_orders;
            vm.loading = false;
        });

        if (!!$localStorage.customer_service && !!$localStorage.customer_service.previous_search) {
            vm.searchParams = $localStorage.customer_service.previous_search;
            vm.search.text = vm.searchParams.search_text;
            vm.search.past_seven_days = vm.searchParams.past_seven_days;
            vm.result_orders = [];
            vm.search.result_orders = true;
            vm.result_merchants = [];
            vm.search.initial_search = true;
            vm.first_search = true;
        }
    }

    function searchOrders() {
        delete $localStorage.customer_service;
        if (vm.search.text === "") {
            vm.first_search = false;
            vm.result_orders = [];
            return;
        }
        vm.result_orders = [];
        vm.search.result_orders = true;
        vm.result_merchants = [];

        if (vm.search_form.$valid) {
            vm.first_search = true;
            vm.search.processing = true;
            vm.search.initial_search = true;
            var new_params = {
                search_text: vm.search.text,
                past_seven_days: vm.search.past_seven_days
            };

            vm.searchParams = UtilityService.mergeObject(vm.searchParams, new_params);
            $localStorage.customer_service = {};
            $localStorage.customer_service.previous_search = vm.searchParams;
        }
    }

    function fiftyOpacityOffset() {
        if (vm.loading_offset_dataset) {
            return 'fifty-opacity';
        }
        else {
            return '';
        }
    }


    function viewOrder(order) {
        $http.post('/order/set_current', {order_id: order.order_id}).success(function (response) {
            Order.current_order = order;
            $location.path('/customer_service/order');
        });
    }

    function hasSearchResult() {
        if (vm.search.processing || vm.result_orders.length > 0) {
            return "hide-recently-visited-panel";
        }
        return "";
    }
}
