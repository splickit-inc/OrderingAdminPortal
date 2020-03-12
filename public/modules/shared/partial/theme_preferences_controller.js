angular.module('adminPortal')
    .controller('ThemePreferencesController', function ($scope, Cookie,$rootScope) {
        var SKIN_COOKIE = 'skin_preferences';
        $rootScope.theme = "dark";
        $scope.selectedTheme = null;

        // create the list of layout files
        $scope.layouts = [
            {name: 'Light Theme', url: 'light'},
            {name: 'Dark Theme', url: 'dark'}
        ];

        $scope.$watch(function() { return Cookie.getCookie(SKIN_COOKIE); }, function(newValue) {
            $rootScope.theme = Cookie.getCookie(SKIN_COOKIE);
        });

        $scope.changeTheme = function() {
            $scope.safeApply(function () {
                $rootScope.theme = $scope.selectedTheme;
                Cookie.setCookie(SKIN_COOKIE, $rootScope.theme, 365);
            });
        };

        function loadCurrentStylePreference() {
            var skinPreference = Cookie.getCookie(SKIN_COOKIE);
            if (!!skinPreference) {
                $rootScope.theme = skinPreference;
                $scope.selectedTheme = skinPreference;
            }
            else {
                $rootScope.theme = "dark";
                Cookie.setCookie(SKIN_COOKIE, $rootScope.theme, 365);
            }
        }

        this.$onInit = function () {
            loadCurrentStylePreference();
        };

    });