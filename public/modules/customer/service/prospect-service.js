angular.module('adminPortal.customer').factory('Prospects', function ($http, $q) {

    var service = {};

    service.createMerchant = function (leadGuid, customerPlan) {
        var url = '/prospects/' + leadGuid + '/merchants';
        var createMerchant = $http.post(url, customerPlan).success(function (data) {
            return data;
        });
        return createMerchant;
    }

    service.setupCustomer = function (leadGuid, customerInfo) {
        var url = '/prospects/' + leadGuid + '/customer';
        var setup = $http.post(url, customerInfo).success(function (data) {
            return data;
        });
        return setup;
    }

    service.uploadMenuFiles = function (leadGuid, files) {
        var url = '/prospects/' + leadGuid + '/menu_files';
        var fd = new FormData();
        for (var i = 0; i < files.length; i++) {
            fd.append("menu_files[]", files[i]);
        }
        var upload = $http.post(url, fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        }).success(function (data) {
            return data;
        });
        return upload;
    }
    return service;
});