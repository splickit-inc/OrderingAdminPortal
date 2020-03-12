angular.module('adminPortal.brand').factory('BrandLookup', function ($http) {

    var service = {};

    service.selected_lookups = [];
    service.possible_lookups = [];



    return service;
});
