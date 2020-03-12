(function () {
    'use strict';
    angular.module('adminPortal.promo').directive('selectMenuSizes', function () {

        var controller = function ($scope, Menu, UtilityService) {
            $scope.menu = Menu;
            $scope.selected_menu_type_sizes = [];

            $scope.addBogoSectionSize = addBogoSectionSize;
            $scope.removeBogoSectionSize = removeBogoSectionSize;

            function addBogoSectionSize(section_size, i) {
                $scope.menu.selected_menu_type_sizes.push(section_size);
                $scope.menu.unselected_menu_type_sizes.splice(i, 1);
                $scope.selected_menu_type_sizes = UtilityService.sortArrayByPropertyAlpha($scope.selected_menu_type_sizes, 'menu_type_name');
            }

            function removeBogoSectionSize(section_size, i) {
                $scope.menu.unselected_menu_type_sizes.push(section_size);
                $scope.menu.selected_menu_type_sizes.splice(i, 1);
                $scope.menu.unselected_menu_type_sizes = UtilityService.sortArrayByPropertyAlpha($scope.menu.unselected_menu_type_sizes, 'menu_type_name');
            }
        };

        return {
            restrict: 'EA',
            replace: true,
            scope: {
                closeMenuObjectSelect: '&'
            },
            templateUrl: 'modules/promo/directive/select-menu-sizes/select-menu-sizes.html',
            controller: controller,
            link: function (scope, element, attrs, ctrl, Menu, UtilityService) {
            }
        };
    });
})();