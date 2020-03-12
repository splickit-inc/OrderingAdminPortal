angular.module('adminPortal.customer', ['ui.bootstrap', 'ngRoute', 'ngAnimate', 'angular-pdf-thumbnail']);

angular.module('adminPortal.customer').config(function ($routeProvider) {

    $routeProvider.when('/customer/create', {
        templateUrl: 'modules/customer/customer-create/customer-create.html',
        controller: "CustomerCreateCtrl",
        controllerAs: "vm",
        sideBar: 'customer'
    });
    $routeProvider.when('/customer/sales_report', {
        templateUrl: 'modules/customer/customer-sales-report/customer-sales-report.html',
        controller: "CustomerSalesReportCtrl",
        controllerAs: "vm",
        sideBar: 'customer'
    });
    $routeProvider.when('/customer/:guid/confirm', {
        templateUrl: 'modules/customer/customer-confirm/customer-confirm.html',
        controller: "CustomerConfirmCtrl",
        controllerAs: "vm",
        navBar: 'none',
        sideBar: 'none',
        publicAccess: true
    });
    $routeProvider.when('/customer/:guid/setup', {
        templateUrl: 'modules/customer/customer-setup/customer-setup.html',
        controller: "CustomerSetupCtrl",
        controllerAs: "vm",
        navBar: 'none',
        sideBar: 'none',
        publicAccess: true
    });
    /* Add New Routes Above */
});

