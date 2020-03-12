angular.module('adminPortal.user').factory('UserReset', function ($http) {

    var service = {};

    service.postEmailToReset = function (email) {
        return $http.post('/password/email', {email: email}).success(function (response) {
            return response;
        });
    };

    service.postResetParams = function (email, password, password_confirmation, token) {
        return $http.post('password/reset', {email: email, password: password,password_confirmation: password_confirmation, token: token}).success(function (response) {
            return response;
        });
    };
    return service;
});