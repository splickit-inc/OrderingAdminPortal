angular.module('adminPortal.merchant').factory('StatementService', function ($http, $window) {
    var service = {};

    service.getAllStatements = function () {
        return $http.get('/statements');
    };

    service.exportStatement = function (params) {
        var url = '/statements/export';
        return $http.get(url, {
            params: params
        });
    };

    service.exportReportStatement = function (params) {
        var url = '/reports/statements/export';
        return $http.get(url, {
            params: params
        });
    };

    return service;
});
