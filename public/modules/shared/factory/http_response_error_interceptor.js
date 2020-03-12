// register the interceptor as a service
angular.module('shared').factory('httpResponseErrorInterceptor', function ($q, Cookie, $location, $localStorage, $window) {
    //This will avoid the redirection for certain routes
    var external_paths = [
        'password'
    ];
    return {
        'responseError': function (rejection) {
            //This will avoid the redirection for certain routes
            var current_path = $location.path();
            if (external_paths.some(function (v) {
                return current_path.indexOf(v) >= 0;
            })) {
                return $q.reject(rejection);
            }

            if (rejection.status === 401 && !!rejection.data && !!rejection.data.error && rejection.data.error === "Unauthenticated.") {
                $localStorage.$reset();
                Cookie.deleteCookie('user_data');
                $location.path('/login');
            }
            return $q.reject(rejection);
        }
    };
});

angular.module('adminPortal').config(function ($httpProvider) {
    $httpProvider.interceptors.push('httpResponseErrorInterceptor');
});
