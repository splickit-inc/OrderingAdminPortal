angular.module('adminPortal.menu', ['ui.bootstrap', 'ngRoute', 'ngAnimate', 'ngCsv']);

angular.module('adminPortal.menu').config(function ($routeProvider) {
    $routeProvider.when('/menus', {
        templateUrl: 'modules/menu/menu-list/menu-list.html',
        controller: "MenuListCtrl",
        controllerAs: "vm",
        section: 'menu'
    });
    $routeProvider.when('/menu/sections', {
        templateUrl: 'modules/menu/menu-sections/menu-sections.html',
        controller: "MenuSectionsCtrl",
        controllerAs: 'sec_mod',
        section: 'menu'
    });
    $routeProvider.when('/menu/items', {
        templateUrl: 'modules/menu/menu-item-list/menu-item-list.html',
        controller: "MenuItemListCtrl",
        controllerAs: 'im',
        section: 'menu'
    });
    $routeProvider.when('/menu/item', {
        templateUrl: 'modules/menu/menu-item/menu-item.html',
        controller: "MenuItemCtrl",
        controllerAs: 'item',
        section: 'menu'
    });
    $routeProvider.when('/menu/modifier_item', {
        templateUrl: 'modules/menu/menu-modifier-item/menu-modifier-item.html',
        controller: "MenuModifierItemCtrl",
        controllerAs: 'mi',
        section: 'menu'
    });
    $routeProvider.when('/menu/edit', {
        templateUrl: 'modules/menu/menu-edit/menu-edit.html',
        controller: "MenuEditCtrl",
        controllerAs: 'mm',
        section: 'menu'
    });
    $routeProvider.when('/merchant/:merchant_id/medit', {
        templateUrl: 'modules/menu/menu-edit/menu-edit.html',
        controller: "MenuEditCtrl",
        controllerAs: 'mm',
        section: 'menu'
    });
    $routeProvider.when('/menu/:menu_id/medit', {
        templateUrl: 'modules/menu/menu-edit/menu-edit.html',
        controller: "MenuEditCtrl",
        controllerAs: 'mm',
        section: 'menu'
    });
    $routeProvider.when('/menu/category_upsells', {
        templateUrl: 'modules/menu/menu-upsell-list/menu-upsell-list.html',
        controller: "MenuUpsellListCtrl",
        controllerAs: 'upsells',
        section: 'menu'
    });
    $routeProvider.when('/menu/category_upsell/create', {
        templateUrl: 'modules/menu/menu-upsell-create/menu-upsell-create.html',
        controller: "MenuUpsellCreateCtrl",
        controllerAs: 'create_upsell',
        section: 'menu'
    });

    $routeProvider.when('/menu/cart_upsells', {
        templateUrl: 'modules/menu/menu-upsells-cart/menu-upsells-cart.html',
        controller: "MenuCartUpsellListCtrl",
        controllerAs: 'vm',
        section: 'menu'
    });
    $routeProvider.when('/menu/cart_upsells/create', {
        templateUrl: 'modules/menu/menu-upsell-cart-create/menu-upsell-cart-create.html',
        controller: "MenuCartUpsellCreateCtrl",
        controllerAs: 'vm',
        section: 'menu'
    });
    $routeProvider.when('/menu/nutrition', {
        templateUrl: 'modules/menu/menu-nutrition-grid/menu-nutrition-grid.html',
        controller: "MenuNutritionGridCtrl",
        controllerAs: 'ng',
        section: 'menu'
    });
    $routeProvider.when('/menu/menu_section', {
        templateUrl: 'modules/menu/menu-section/menu-section.html',
        controller: "MenuSectionCtrl",
        controllerAs: 'ms',
        section: 'menu'
    });
    $routeProvider.when('/menu/export', {
        templateUrl: 'modules/menu/menu-overview/menu-overview.html',
        controller: "MenuOverviewReportCtrl",
        controllerAs: 'mo',
        section: 'menu'
    });
    /* Add New Routes Above */

});

