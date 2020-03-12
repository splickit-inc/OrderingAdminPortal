angular.module('adminPortal.valueAddedReseller', ['ui.bootstrap', 'ngRoute', 'ngAnimate']);

angular.module('adminPortal.valueAddedReseller').config(function ($routeProvider) {

    $routeProvider.when('/vars/manage', {
        templateUrl: 'modules/value-added-reseller/vars-manage/vars-manage.html',
        sideBar: 'value-added-reseller'
    });
    $routeProvider.when('/var/create', {
        templateUrl: 'modules/value-added-reseller/var-create/var-create.html',
        controller: 'VarCreateCtrl',
        sideBar: 'value-added-reseller'
    });
    /* Add New Routes Above */

});

