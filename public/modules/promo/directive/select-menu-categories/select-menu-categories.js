(function () {
    'use strict';
    angular.module('adminPortal.promo').directive('selectMenuCategories', function () {

        var controller = function ($scope, Menu, UtilityService) {
            $scope.menu = Menu;

            $scope.addCategory = addCategory;
            $scope.removeCategory = removeCategory;

            function addCategory(item_size, i) {
                $scope.menu.selected_menu_categories.push(item_size);
                $scope.menu.unselected_menu_categories.splice(i, 1);
                $scope.menu.selected_menu_type_categories = UtilityService.sortArrayByPropertyAlpha($scope.selected_menu_categories, 'type_id_name');
            }

            function removeCategory(item_size, i) {
                $scope.menu.unselected_menu_categories.push(item_size);
                $scope.menu.selected_menu_categories.splice(i, 1);
                $scope.menu.unselected_menu_categories = UtilityService.sortArrayByPropertyAlpha($scope.menu.unselected_menu_categories, 'type_id_name');
            }
        };

        return {
            restrict: 'EA',
            replace: true,
            scope: {
                closeMenuObjectSelect: '&'
            },
            templateUrl: 'modules/promo/directive/select-menu-categories/select-menu-categories.html',
            controller: controller,
            link: function (scope, element, attrs, ctrl, Menu, UtilityService) {
                // scope.setBogoItems();
            }
        };
    });
})();