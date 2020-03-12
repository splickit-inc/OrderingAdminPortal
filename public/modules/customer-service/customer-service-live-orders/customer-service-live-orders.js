angular.module('adminPortal.customerService').controller('CustomerServiceLiveOrdersCtrl', CustomerServiceLiveOrdersCtrl);

function CustomerServiceLiveOrdersCtrl(Order, $scope, $http, $location) {
    var vm = this;

    vm.orders = [];
    vm.loading = false;
    var timeout;

    vm.viewOrder = viewOrder;

    function reloadLiveOrders() {
        vm.loading = true;
        Order.get('live_orders').then(function (response) {
            var d = new Date();
            vm.orders = response.data;
            vm.loading = false;
            timeout = setTimeout(reloadLiveOrders, 10000);
        });
    }

    function viewOrder(order) {
        $http.post('/order/set_current', {order_id: order.real_order_id}).success(function (response) {
            Order.current_order = order;
            Order.current_order.order_id = order.real_order_id;
            $location.path('/customer_service/order');
        });
    }

    $scope.$on("$destroy", function(){
        clearTimeout(timeout);
    });

    reloadLiveOrders();
}