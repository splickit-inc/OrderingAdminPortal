angular.module('adminPortal.merchant').factory('PaymentService', function ($http) {

    var paymentService = {};

    paymentService.getPaymentInformation = function () {
        return $http.get('/merchant/payment');
    };

    paymentService.submitBusinessInformation = function (data) {
        return $http.post('/merchant/payment/business_information', data);
    };

    paymentService.submitOwnerInformation = function (data) {
        return $http.post('/merchant/payment/owner_information', data);
    };

    paymentService.uploadOwnerDocumentation = function (licenseFile, voidedCheckFile) {
        return $http({
            method: 'POST',
            url: '/merchant/payment/owner/upload',
            headers: {
                'Content-Type': undefined
            },
            data: {
                licenseFile: licenseFile,
                voidedCheckFile: voidedCheckFile
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

    return paymentService;
});
