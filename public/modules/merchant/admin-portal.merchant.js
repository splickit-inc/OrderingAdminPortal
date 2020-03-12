angular.module('adminPortal.merchant', ['ui.bootstrap', 'ngRoute', 'ngAnimate']);

angular.module('adminPortal.merchant').config(function ($routeProvider) {
    $routeProvider.when('/', {redirectTo: '/merchants'});
    $routeProvider.when('/merchants', {
        templateUrl: 'modules/merchant/merchant-list/merchant-list.html',
        controller: "MerchantListCtrl",
        controllerAs: "merchant"
    });
    $routeProvider.when('/merchant/create', {
        templateUrl: 'modules/merchant/merchant-create/merchant-create.html',
        controller: "MerchantCreateCtrl"
    });
    $routeProvider.when('/merchant/general_info', {
        templateUrl: 'modules/merchant/merchant-info/merchant-info.html',
        controller: "MerchantInfoCtrl",
        controllerAs: 'info'
    });
    $routeProvider.when('/merchant/contact', {
        templateUrl: 'modules/merchant/merchant-contact/merchant-contact.html',
        controller: "MerchantContactCtrl",
        controllerAs: 'contact'
    });
    $routeProvider.when('/merchant/hours', {
        templateUrl: 'modules/merchant/merchant-hours/merchant-hours.html',
        controller: "MerchantHoursCtrl",
        controllerAs: 'vm_hour'
    });
    $routeProvider.when('/merchant/tax', {
        templateUrl: 'modules/merchant/merchant-tax/merchant-tax.html',
        controller: "MerchantTaxCtrl",
        controllerAs: 'tax'
    });
    $routeProvider.when('/merchant/delivery', {
        templateUrl: 'modules/merchant/merchant-delivery/merchant-delivery.html',
        controller: "MerchantDeliveryCtrl",
        controllerAs: 'delivery'
    });
    $routeProvider.when('/merchant/ordering', {
        templateUrl: 'modules/merchant/merchant-ordering/merchant-ordering.html',
        controller: "MerchantOrderingCtrl",
        controllerAs: 'ordering'
    });
    $routeProvider.when('/merchant/order/send', {
        templateUrl: 'modules/merchant/merchant-order-send/merchant-order-send.html',
        controller: "MerchantOrderSendCtrl"
    });
    $routeProvider.when('/merchant/search', {
        templateUrl: 'modules/merchant/merchant-search/merchant-search.html',
        controller: "MerchantSearchCtrl"
    });
    $routeProvider.when('/merchant/operator_ordering_on_off', {
        templateUrl: 'modules/merchant/merchant-ordering-on-off/merchant-ordering-on-off.html',
        controller: "MerchantOperatorOrderingOnOffCtrl",
        controllerAs: 'op_on_off'
    });
    $routeProvider.when('/merchant/catering', {
        templateUrl: 'modules/merchant/merchant-catering/merchant-catering.html',
        controller: 'MerchantCateringCtrl',
        controllerAs: 'vm'
    });
    $routeProvider.when('/merchant/statements', {
        templateUrl: 'modules/merchant/merchant-statements/merchant-statements.html',
        controller: "MerchantStatementsCtrl",
        controllerAs: 'vm',
        css: 'modules/merchant/merchant-statements/merchant-statements.css'
    });
    $routeProvider.when('/merchant/payment', {
        templateUrl: 'modules/merchant/payment-services/payment-services.html',
        controller: 'PaymentServicesCtrl',
        controllerAs: 'vm'
    });
    /* Add New Routes Above */
});

