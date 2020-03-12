angular.module('shared').factory('Lookup', function ($http) {

    var service = {};

    service.singleLookup = function (lookup_type) {
        var lookup = $http.get('/lookup/' + lookup_type);

        lookup.then(function (data) {
            return data;
        });
        return lookup;
    };

    service.multipleLookup = function (lookups) {
        var multiLookups = $http.post('/multi_lookup', lookups);

        multiLookups.then(function (data) {
            return data;
        });
        return multiLookups;
    };

    service.yesNoTrueFalseConversion = function (value) {
        if (value === "Y") {
            return true;
        }
        else {
            return false;
        }
    };

    service.zeroOneTrueFalseConversion = function (value) {
        if (parseInt(value) > 0) {
            return true;
        }
        else {
            return false;
        }
    };

    return service;
});
