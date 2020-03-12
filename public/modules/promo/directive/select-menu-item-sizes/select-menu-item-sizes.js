(function () {
    'use strict';
    angular.module('adminPortal.promo').directive('selectMenuItemSizes', function () {

        var controller = function ($scope, Menu, UtilityService) {
            $scope.menu = Menu;

            $scope.addBogoItemSize = addBogoItemSize;
            $scope.removeBogoItemSize = removeBogoItemSize;

            function addBogoItemSize(item_size, i) {
                $scope.menu.selected_menu_type_items_sizes.push(item_size);
                $scope.menu.unselected_menu_type_items_sizes.splice(i, 1);
                $scope.selected_menu_type_items_sizes = UtilityService.sortArrayByPropertyAlpha($scope.selected_menu_type_items_sizes, 'item_name');
            }

            function removeBogoItemSize(item_size, i) {
                $scope.menu.unselected_menu_type_items_sizes.push(item_size);
                $scope.menu.selected_menu_type_items_sizes.splice(i, 1);
                $scope.menu.unselected_menu_type_items_sizes = UtilityService.sortArrayByPropertyAlpha($scope.menu.unselected_menu_type_items_sizes, 'item_name');
            }
        };

        return {
            restrict: 'EA',
            replace: true,
            scope: {
                closeMenuObjectSelect: '&'
            },
            templateUrl: 'modules/promo/directive/select-menu-item-sizes/select-menu-item-sizes.html',
            controller: controller,
            link: function (scope, element, attrs, ctrl, Menu, UtilityService) {
                // scope.setBogoItems();
            }
        };
    });
})();