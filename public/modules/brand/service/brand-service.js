angular.module('adminPortal.brand').factory('Brands', function ($http) {

    var service = {};
    service.currentBrandSelected = undefined;

    service.getBrandMerchants = function (id) {
        var merchants = $http.get('/brands/' + id + '/merchants').success(function (data) {
            return data;
        });
        return merchants;
    };

    service.getAllBrands = function () {
        return $http.get('/brands');
    };

    service.searchBrands = function (search_value) {
        return $http.post('/brands', {
            fields: ["brand_id", "brand_name", "support_email"],
            value: search_value
        });
    };

    service.getBrandsWithFirstLetter = function (letter) {
        return $http.get('/brands/' + letter);
    };

    service.editBrand = function (brand_id, data) {
        return $http.post('/brand/' + brand_id, data);
    };

    service.getBrand = function (brand_id) {
        return $http.get('/brand/' + brand_id);
    };
    service.getCurrentBrand = function () {
        return $http.get('/brand');
    };
    return service;
});
