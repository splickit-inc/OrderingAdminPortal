angular.module('adminPortal.menu').controller('MenuSearchCtrl', MenuSearchCtrl);

function MenuSearchCtrl($scope, Menu) {

    $scope.menu_search_text = "";
    $scope.current_merchant_id = "";
    $scope.processing = false;

    $scope.current_search = 'none';

    $scope.$on('$routeChangeStart', function (event, next, current) {
        if (!!next.$$route && next.$$route.originalPath.includes("menu")) {
            $scope.current_search = 'menu';
        }
        else {
            $scope.current_search = 'none';
        }
    });

    $scope.updateMenuSearch = function () {
        Menu.search_text = $scope.menu_search_text;
    }
}