angular.module('adminPortal.customerService', ['ui.bootstrap', 'ngRoute', 'ngAnimate']);

angular.module('adminPortal.customerService').config(function ($routeProvider) {
    $routeProvider.when('/customer_service', {
        templateUrl: 'modules/customer-service/customer-service/customer-service.html',
        controller: "CustomerServiceCtrl",
        controllerAs: "customer_service",
        section: 'customer-service'
    });
    $routeProvider.when('/customer_service/order', {
        templateUrl: 'modules/customer-service/customer-service-order/customer-service-order.html',
        controller: "CustomerServiceOrderCtrl",
        controllerAs: "order",
        section: 'customer-service'
    });
    $routeProvider.when('/customer_service/user', {
        templateUrl: 'modules/customer-service/customer-service-user/customer-service-user.html',
        controller: "CustomerServiceUserCtrl",
        controllerAs: "cs_user",
        section: 'customer-service'
    });
    $routeProvider.when('/customer_service/live_orders', {
        templateUrl: 'modules/customer-service/customer-service-live-orders/customer-service-live-orders.html',
        controller: "CustomerServiceLiveOrdersCtrl",
        controllerAs: "live_orders",
        section: 'customer-service'
    });
    $routeProvider.when('/customer_service/users', {
        templateUrl: 'modules/customer-service/customer-service-user-list/customer-service-user-list.html',
        controller: "CustomerServiceUserListCtrl",
        controllerAs: "user",
        section: 'customer-service'
    });
    $routeProvider.when('/customer_service/tbd', {
        templateUrl: 'modules/customer-service/customer-service-tbd/customer-service-tbd.html',
        controller: "CustomerServiceTbdCtrl",
        controllerAs: "vm"
    });
    $routeProvider.when('/customer_service/future_orders', {
        templateUrl: 'modules/customer-service/future-orders/future-orders.html',
        controller: "FutureOrdersCtrl",
        controllerAs: "vm"
    });
    /* Add New Routes Above */
});

