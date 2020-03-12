(function () {
    'use strict';
    angular.module('adminPortal.promo').directive('selectMenuSections', function () {

        var controller = function ($scope, Menu, UtilityService) {
            $scope.menu = Menu;

            $scope.addBogoSection = addBogoSection;
            $scope.removeBogoSection = removeBogoSection;

            function addBogoSection(section_item, i) {
                $scope.menu.selected_menu_types.push(section_item);
                $scope.menu.unselected_menu_types.splice(i, 1);
                $scope.selected_menu_types = UtilityService.sortArrayByPropertyAlpha($scope.menu.selected_menu_types, 'menu_type_name');
            }

            function removeBogoSection(section_item, i) {
                $scope.menu.unselected_menu_types.push(section_item);
                $scope.menu.selected_menu_types.splice(i, 1);
                $scope.menu.unselected_menu_types = UtilityService.sortArrayByPropertyAlpha($scope.menu.unselected_menu_types, 'menu_type_name');
            }
        };

        return {
            restrict: 'EA',
            replace: true,
            scope: {
                closeMenuObjectSelect: '&'
            },
            templateUrl: 'modules/promo/directive/select-menu-sections/select-menu-sections.html',
            controller: controller,
            link: function (scope, element, attrs, ctrl, Menu, UtilityService) {
                // scope.setBogoItems();
            }
        };
    });
})();