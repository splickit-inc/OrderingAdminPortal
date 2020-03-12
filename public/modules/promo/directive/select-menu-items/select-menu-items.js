(function () {
    'use strict';
    angular.module('adminPortal.promo').directive('selectMenuItems', function () {

        var controller = function ($scope, Menu, UtilityService) {
            $scope.menu = Menu;
            $scope.selected_menu_type_items = [];
            $scope.search_text = '';

            $scope.addBogoSectionItem = addBogoSectionItem;
            $scope.removeBogoSectionItem = removeBogoSectionItem;

            function addBogoSectionItem(section_item, i) {
                $scope.menu.selected_menu_type_items.push(section_item);
                $scope.menu.unselected_menu_type_items.splice(i, 1);
                $scope.selected_menu_type_items = UtilityService.sortArrayByPropertyAlpha($scope.selected_menu_type_items, 'menu_type_name');
            }

            function removeBogoSectionItem(section_item, i) {
                $scope.menu.unselected_menu_type_items.push(section_item);
                $scope.menu.selected_menu_type_items.splice(i, 1);
                $scope.menu.unselected_menu_type_items = UtilityService.sortArrayByPropertyAlpha($scope.menu.unselected_menu_type_items, 'menu_type_name');
            }

            $scope.setBogoItemsCloseForm = function () {
                $scope.setBogoItems({selected_menu_type_items: $scope.menu.selected_menu_type_items});
            }

            $scope.itemFilter = function(section_item) {
                if ($scope.search_text.length < 3) {
                    return true;
                }
                var filter_text_reg_exp = new RegExp($scope.search_text.toUpperCase(), 'g');

                if (section_item.item_name.toUpperCase().match(filter_text_reg_exp) || section_item.item_id == $scope.search_text) {
                    return true;
                }
                else {
                    return false;
                }
            }
        };

        return {
            restrict: 'EA',
            replace: true,
            scope: {
                closeMenuObjectSelect: '&'
            },
            templateUrl: 'modules/promo/directive/select-menu-items/select-menu-items.html',
            controller: controller,
            link: function (scope, element, attrs, ctrl, Menu, UtilityService) {
                // scope.setBogoItems();
            }
        };
    });
})();