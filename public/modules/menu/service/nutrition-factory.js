angular.module('adminPortal.menu').factory('Nutrition', function ($http, Cookie, $q, Lookup, UtilityService) {

    var service = {};

    service.menu_types = [];

    service.loadNutrition = function() {
        return $http.get('menu/nutrition').then(function(response) {
            service.menu_types = response.data;
        });
    }

    service.updateOfferingNutrition = function(data) {
        return $http.post('menu/nutrition', data).then(function(response) {
            service.data = response.data;
        });
    }

    return service;
});
