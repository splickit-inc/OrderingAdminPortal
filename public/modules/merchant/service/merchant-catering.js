angular.module('adminPortal.merchant').factory('MerchantCatering', function ($http) {
    var service = {};

    service.getCateringConfiguration = function () {
        return $http.get('/merchant/catering');
    };

    service.saveCateringConfiguration = function (data) {
        return $http.post('/merchant/catering', data);
    };
    return service;
});
