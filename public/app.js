angular.module('adminPortal', [
    'ui.bootstrap',
    'ngRoute',
    'ngAnimate',
    'angular-loading-bar',
    'angularCSS',
    'ui.utils.masks',
    'textAngular',
    'ngStorage',
    'adminPortal.merchant',
    'adminPortal.menu',
    'adminPortal.promo',
    'adminPortal.customerService',
    'adminPortal.webSkin',
    'adminPortal.brand',
    'adminPortal.reports',
    'adminPortal.customer',
    'adminPortal.user',
    'adminPortal.valueAddedReseller',
    'shared',
    'toggle-switch',
    'ngFileUpload',
    'uiCropper',
    'mp.colorPicker',
    'dndLists',
    'ngAnimate',
    'chart.js',
    'ngSanitize',
    'angucomplete-alt',
    'angularUtils.directives.dirPagination',
    'adminPortal.operator',
    'adminPortal.operator',
    'pascalprecht.translate',
    'fixed.table.header',
    'angular-web-notification',
    'daterangepicker',
    'angular.css.injector',
    'ngCsv'
]);

angular.module('adminPortal').config(function ($routeProvider, cfpLoadingBarProvider, $translateProvider, $localStorageProvider) {
    /* Add New Routes Above */
    $routeProvider.otherwise({redirectTo: '/'});
    /* Config loading bar */
    cfpLoadingBarProvider.latencyThreshold = 300;

    /*  angular translate provides a number of mechanisms for preventing a number of possible exploits  */
    $translateProvider.useSanitizeValueStrategy('escape');

    $translateProvider.useStaticFilesLoader({
        prefix: './assets/languages/',
        suffix: '.json'
    });

    $translateProvider.preferredLanguage('en_US');
    /* jshint ignore:start */
    var mySerializer = function (value) {
        try {
            return window.btoa(angular.toJson(value));
        } catch (e) {
            return undefined;
        }

    };

    var myDeserializer = function (value) {
        try {
            return angular.fromJson(window.atob(value));
        } catch (e) {
            console.log(e);
            return undefined;
        }
    };

    $localStorageProvider.setSerializer(mySerializer);
    $localStorageProvider.setDeserializer(myDeserializer);
    /* jshint ignore:end */
});

angular.module('adminPortal').run(function ($rootScope) {

    $rootScope.safeApply = function (fn) {
        var phase = $rootScope.$$phase;
        if (phase === '$apply' || phase === '$digest') {
            if (fn && (typeof(fn) === 'function')) {
                fn();
            }
        } else {
            this.$apply(fn);
        }
    };

});

angular.module('adminPortal').run(function ($rootScope, $location, Users, $route) {
    $rootScope.$on('$routeChangeStart', function (event, next, current) {
        var isPublic = next.publicAccess || false;
        if (isPublic && Users.isLoggedIn() &&
            next.templateUrl === "template/login.html") {
            next.sideBar = 'home';
            $location.path('/');
        }
        if (!isPublic && !Users.isLoggedIn()) {
            event.preventDefault();
            next.sideBar = 'none';
            $location.path('/login');
        }
    });
});
angular.module('adminPortal').run(function ($rootScope) {
    //polyfill for IE11
    if (!String.prototype.includes) {
        String.prototype.includes = function (search, start) {
            if (typeof start !== 'number') {
                start = 0;
            }

            if (start + search.length > this.length) {
                return false;
            } else {
                return this.indexOf(search, start) !== -1;
            }
        };
    }
});
