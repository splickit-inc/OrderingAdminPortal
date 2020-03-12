angular.module('adminPortal.reports', ['ui.bootstrap', 'ngRoute', 'ngAnimate']);

angular.module('adminPortal.reports').config(function ($routeProvider) {
    $routeProvider.when('/reports', {
        templateUrl: 'modules/reports/reports/reports.html',
        controller: "ReportsCtrl",
        controllerAs: 'rpt',
        section: 'reports'
    });
    /* Add New Routes Above */
});

