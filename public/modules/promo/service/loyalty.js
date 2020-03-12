angular.module('adminPortal.promo').factory('Loyalty', function ($http) {

    var loyalty = {};

    loyalty.uploadLogo = function (file, brand_id) {
        return $http({
            method: 'POST',
            url: '/loyalty/upload/logo',
            headers: {
                'Content-Type': undefined
            },
            data: {
                brand_id: brand_id,
                file: file
            },
            transformRequest: function (data, headersGetter) {
                var formData = new FormData();
                angular.forEach(data, function (value, key) {
                    formData.append(key, value);
                });
                return formData;
            }
        });
    };

    loyalty.setLoyaltyStatus = function (status, brand_id) {
        if (status === true) {
            status = 'Y';
        }
        else {
            status = 'N';
        }
        return $http.post('/loyalty/status', {loyalty: status, brand_id: brand_id});
    };

    loyalty.getBrands = function () {
        return $http.get('/brands');
    };

    loyalty.getBrandConfiguration = function (brand_id) {
        return $http.get('/brands/' + brand_id + '/loyalty');
    };

    loyalty.setBrandConfiguration = function (brand_id, data) {
        return $http.post('/brands/' + brand_id + '/loyalty', data);
    };

    loyalty.setBonusPointsDays = function (brand_id, data) {
        return $http.post('/brands/' + brand_id + '/bonus_points_day', data);
    };

    loyalty.deleteBonusPointsDays = function (brand_id, bonus_id) {
        return $http.delete('/brands/' + brand_id + '/bonus_points_day/' + bonus_id);
    };
    return loyalty;
});
