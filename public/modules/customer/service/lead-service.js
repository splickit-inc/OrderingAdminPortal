angular.module('adminPortal.customer').factory('Leads', function ($http, $q) {

    var service = {};

    service.createLead = function (new_lead) {
        var create_lead = $http.post('/leads', new_lead).success(function (data) {
            return data;
        });
        return create_lead;
    }
    service.serviceTypes = function () {
        var services = $http.get('/leads/service_types').success(function (data) {
            return data;
        });
        return services;
    }
    service.paymentTypes = function () {
        var payments = $http.get('/leads/payment_types').success(function (data) {
            return data;
        });
        return payments;
    }

    service.getLead = function (guid) {
        var exists = $http.get('/leads/' + guid).success(function (data) {
            return data;
        });
        return exists;
    }

    service.getLeads = function (fromDate, toDate) {
        var url = '/leads';
        if (!!fromDate && !!toDate) {
            url += '?created_at=' + fromDate + ',' + toDate
        }
        var exists = $http.get(url).success(function (data) {
            return data;
        });
        return exists;
    }
    return service;
});