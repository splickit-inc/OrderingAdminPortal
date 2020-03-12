angular.module('shared').directive('modalMenuPicker', function (Menu) {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            body: '=modalBody',
            footer: '=modalFooter',
            callbackbuttonleft: '&ngClickLeftButton',
            callbackbuttonright: '&ngClickRightButton',
            handler: '=modalName',
            selected_values: '=?modalSelectedValues',
            selectable: '=modalSelectableValues',
            search_function: '=?modalSearchFunction',
            autoLoad: '=?modalAutoLoad'
        },
        templateUrl: 'modules/shared/directive/modal-menu-picker/modal-menu-picker.html',
        transclude: true,
        link: function (scope, element, attrs, fn) {
            scope.search_processing = false;
            scope.selectable = [];
            scope.search_text = "";
            scope.select_options = ['pickup', 'delivery', 'both'];
            scope.current_menu_type = scope.select_options[0];

            if (!scope.selected_values || scope.selected_values === undefined) {
                scope.selected_values = [];
            }

            if (!scope.search_function || scope.search_function === undefined) {
                scope.search_function = Menu.getMenuList;
            }

            scope.search = function () {
                scope.search_processing = true;
                scope.search_function(scope.search_text).then(function (response) {
                    scope.selectable = response;
                    scope.search_processing = false;
                });
            };

            scope.add = function (new_item, menu_type) {
                new_item['pivot'] = {};
                new_item['pivot'].merchant_menu_type = menu_type;
                if (scope.selected_values.length > 0 && (!!lookupIfExistWithType(new_item) || isNotTheSameMenuID(new_item))) {
                    return;
                }
                lookupDuplicatesWhenTypeBoth(new_item);
                removeFromArray(scope.selectable, new_item);
                scope.selected_values.push(new_item);
            };

            scope.remove = function (item) {
                if (!existItemWithAttribute(scope.selectable, 'menu_id', item)) {
                    scope.selectable.push(item);
                }
                removeFromArray(scope.selected_values, item);

            };

            function isNotTheSameMenuID(current_item) {
                var item_finded;

                item_finded = scope.selected_values.find(function (element) {
                    return element.menu_id === current_item.menu_id;
                });
                if (!!item_finded) {
                    return false;
                }
                return true;
            }

            function lookupIfExistWithType(current_item) {
                return scope.selected_values.find(function (element) {
                    return (element.menu_id === current_item.menu_id);
                });
            }

            function lookupDuplicatesWhenTypeBoth(current_item) {
                if (!!current_item['pivot'] && !!current_item['pivot'].merchant_menu_type && current_item['pivot'].merchant_menu_type === 'both') {
                    var item_found;
                    do {
                        item_found = scope.selected_values.find(function (element) {
                            return element.menu_id === current_item.menu_id && element['pivot'].merchant_menu_type !== 'both';
                        });
                        if (!!item_found) {
                            removeFromArray(scope.selected_values, item_found);
                        }
                        console.log(item_found);
                    } while (!!item_found);
                }
            }

            function existOnArray(array, value) {
                return array.includes(value);
            }

            function removeFromArray(array, item) {
                var index = array.indexOf(item);
                array.splice(index, 1);
            }

            function existItemWithAttribute(array, attribute, item) {
                var exist = false;

                array.forEach(function (element) {
                    if (!!attribute && !!element[attribute] && !!item[attribute]) {
                        if (element[attribute] === item[attribute]) {
                            exist = true;
                        }
                    }
                    else {
                        if (existOnArray(array, item)) {
                            exist = true;
                        }
                    }
                });

                return exist;
            }
            if (!!scope.autoLoad) {
                scope.search();
            }
        }
    };
});
