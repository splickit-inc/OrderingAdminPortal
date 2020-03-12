angular.module('adminPortal.menu').directive('editAllowedModifiers', function () {
    var controller = function ($scope, UtilityService, MenuItem) {
        $scope.menu_item = MenuItem;

    };

    return {
        restrict: 'EA',
        replace: true,
        scope: {
            closeMerchantSelect: '&',
            propationOptions: '=',
            autoSearch: '=?',
            submitAllowedModifierChanges:'&'
        },
        templateUrl: 'modules/menu/directive/edit-allowed-modifiers/edit-allowed-modifiers.html',
        controller: controller,
        link: function (scope, element, attrs, fn) {


        }
    };
});
