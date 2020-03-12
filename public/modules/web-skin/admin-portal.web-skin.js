angular.module('adminPortal.webSkin', ['ui.bootstrap', 'ngRoute', 'ngAnimate']);

angular.module('adminPortal.webSkin').config(function ($routeProvider) {
    $routeProvider.when('/web_skin/create', {
        templateUrl: 'modules/web-skin/web-skin-create/web-skin-create.html',
        controller: "WebSkinCreateCtrl",
        controllerAs: 'creative',
        section: 'sites'
    });
    $routeProvider.when('/web_skin/configuration', {
        templateUrl: 'modules/web-skin/web-skin-configuration/web-skin-configuration.html',
        controller: "WebSkinConfigurationCtrl",
        controllerAs: 'skin_config',
        section: 'sites'
    });
    /* Add New Routes Above */

});

