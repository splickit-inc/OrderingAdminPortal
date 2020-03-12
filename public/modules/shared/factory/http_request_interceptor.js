/**
 * Created by diego.rodriguez on 10/27/17.
 */
angular.module('shared').factory('HttpRequestInterceptor', function (Cookie) {
    return {
        request: function (config) {
            var cookieData = Cookie.getCookie('user_data');
            if (!cookieData) {
                return config;
            }
            var userData = JSON.parse(cookieData);
            var authToken = userData.token;
            if (!!authToken) {
                config.headers['Authorization'] = 'Bearer ' + authToken;
            }
            return config;
        }
    };
});


angular.module('adminPortal').config(function ($httpProvider) {
    $httpProvider.interceptors.push('HttpRequestInterceptor');
});