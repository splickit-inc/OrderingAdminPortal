angular.module('adminPortal.promo', ['ui.bootstrap', 'ngRoute', 'ngAnimate']);

angular.module('adminPortal.promo').config(function ($routeProvider) {
    $routeProvider.when('/promo/create', {
        templateUrl: 'modules/promo/promo-create/promo-create.html',
        controller: "PromoCreateCtrl",
        controllerAs: 'new_promo',
        section: 'marketing'
    });
    $routeProvider.when('/promo/edit', {
        templateUrl: 'modules/promo/promo-edit/promo-edit.html',
        controller: "PromoEditCtrl",
        controllerAs: 'edit_promo',
        section: 'marketing'
    });
    $routeProvider.when('/promos', {
        templateUrl: 'modules/promo/promo-list/promo-list.html',
        controller: "PromoListCtrl",
        controllerAs: 'promo',
        section: 'marketing'
    });
    $routeProvider.when('/promos/loyalty',{
        templateUrl: 'modules/promo/loyalty-configuration/loyalty-configuration.html',
        controller: "LoyaltyConfigurationCtrl",
        controllerAs: 'vm',
        section: 'marketing'
    });
});

