angular.module('adminPortal.operator', ['ui.bootstrap','ngRoute','ngAnimate']);

angular.module('adminPortal.operator').config(function($routeProvider) {

    /* Add New Routes Above */
    $routeProvider.when('/operator/order_management', {
        templateUrl: 'modules/operator/order-management/operator-order-management.html',
        controller: "OrderManagementCtrl",
        controllerAs: "om",
        section: 'operator'
    }).when('/operator/home', {
        templateUrl: 'modules/operator/operator-home/operator-home.html',
        controller: "OperatorHomeCtrl",
        controllerAs: "oh",
        section: 'operator'
    });

});