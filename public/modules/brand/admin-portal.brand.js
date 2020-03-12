angular.module('adminPortal.brand', ['ui.bootstrap', 'ngRoute', 'ngAnimate']);

angular.module('adminPortal.brand').config(function ($routeProvider) {
    $routeProvider.when('/brand/create', {
        templateUrl: 'modules/brand/brand-create/brand-create.html',
        controller: "BrandCreateCtrl",
        controllerAs: "brand"
    });
    $routeProvider.when('/brands/', {
        templateUrl: 'modules/brand/brand-list/brand-list.html',
        controller: "BrandListCtrl",
        controllerAs: "vm"
    });
    $routeProvider.when('/brands/merchant_groups', {
        templateUrl: 'modules/brand/merchant-groups/merchant-groups.html',
        controller: 'MerchantGroupsCtrl',
        controllerAs: 'mg'
    });
    $routeProvider.when('/brands/merchant_groups_create', {
        templateUrl: 'modules/brand/merchant-groups-create/merchant-groups-create.html',
        controller: 'MerchantGroupsCreateCtrl',
        controllerAs: 'mgc'
    });
    $routeProvider.when('/brands/merchant_groups_edit', {
        templateUrl: 'modules/brand/merchant-groups-edit/merchant-groups-edit.html',
        controller: 'MerchantGroupsEditCtrl',
        controllerAs: 'mge'
    });
    $routeProvider.when('/brand', {
        templateUrl: 'modules/brand/brand-edit/brand-edit.html',
        controller: "BrandEditCtrl",
        controllerAs: "vm"
    });
});