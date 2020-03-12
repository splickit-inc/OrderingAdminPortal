/**
 * Created by diego.rodriguez on 10/26/17.
 */
angular.module('shared').controller('NavigationRouteController', NavigationRouteController);

function NavigationRouteController($scope) {
    $scope.$on("$routeChangeStart", function (event, next, current) {
        $scope.sideBarTemplate = next.sideBar || 'merchant';
        $scope.navBarTemplate = next.navBar || 'default';
        $scope.sectionClass = next.section || 'accounts';
    });

    $scope.getSideBarSource = function (sideBarTemplate) {
        var fileName = 'side-bar.html';
        if (!sideBarTemplate) {
            sideBarTemplate = 'merchant';
        }
        if (sideBarTemplate === 'none' || sideBarTemplate === 'blank') {
            fileName = 'side-bar/' + sideBarTemplate + '.html';
            sideBarTemplate = 'shared';
        }
        return 'modules/' + sideBarTemplate + '/' + fileName;
    };

    $scope.getNavBarSource = function (navBarTemplate) {
        var fileName = 'nav-bar.html';
        if (!navBarTemplate) {
            navBarTemplate = 'default';
        }
        if (navBarTemplate === 'none' || navBarTemplate === 'default') {
            fileName = 'nav-bar/' + navBarTemplate + '.html';
            navBarTemplate = 'shared';
        }
        return 'modules/' + navBarTemplate + '/' + fileName;
    };

    $scope.getSectionClass = function () {
        return $scope.sectionClass + '-section';
    };
}