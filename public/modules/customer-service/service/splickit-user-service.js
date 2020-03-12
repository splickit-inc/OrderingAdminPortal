angular.module('adminPortal.customerService').factory('SplickitUser', function ($http) {

    var service = {};

    service.search_result_users = [];

    service.getSearchResults = function () {
        return service.search_result_users;
    }

    return service;
});