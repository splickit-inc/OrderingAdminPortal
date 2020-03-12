angular.module('shared').directive('modalPicker', function () {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            selection_title: '=modalSelectionTitle',
            selected_title: '=modalSelectedTitle',
            header: '=modalHeader',
            body: '=modalBody',
            footer: '=modalFooter',
            callbackbuttonleft: '&ngClickLeftButton',
            callbackbuttonright: '&ngClickRightButton',
            handler: '=modalName',
            maximum_selection_permitted: '=modalMaximumSelectionPermitted',
            selected_values: '=modalSelectedValues',
            selectable: '=?modalSelectableValues',
            row_id: '=modalRowId',
            search_function: '=modalSearchFunction',
            table_headers: '=modalTableHeaders',
            table_fields: '=modalTableFields',
            disable_search_field: '=?disableSearchField',
            init_value_search: '=?initValueSearch'
        },
        templateUrl: 'modules/shared/directive/modal-picker/modal-picker.html',
        transclude: true,
        link: function (scope, element, attrs, fn) {
            scope.search_processing = false;
            scope.selectable = [];

            if (!!scope.disable_search_field && !! scope.init_value_search && !!scope.search_function) {
                scope.search_processing = true;
                scope.search_function(scope.init_value_search).success(function (response) {
                    scope.selectable = response;
                    scope.search_processing = false;
                });
            }

            if (!scope.selected_values || scope.selected_values === undefined) {
                scope.selected_values = [];
            }
            scope.search_text = "";

            scope.search = function () {
                scope.search_processing = true;
                scope.search_function(scope.search_text).success(function (response) {
                    scope.selectable = response;
                    scope.search_processing = false;
                });
            };

            scope.add = function (item) {
                if (!!scope.maximum_selection_permitted &&
                    isNumeric(scope.maximum_selection_permitted) &&
                    scope.selected_values.length >= scope.maximum_selection_permitted ||
                    existItemWithAttribute(scope.selected_values, scope.row_id, item)) {
                    return;
                }

                removeFromArray(scope.selectable, item);
                scope.selected_values.push(item);
            };

            scope.remove = function (item) {
                if (!existItemWithAttribute(scope.selectable, scope.row_id, item)) {
                    scope.selectable.push(item);
                }
                removeFromArray(scope.selected_values, item);

            };

            function isNumeric(n) {
                return !isNaN(parseFloat(n)) && isFinite(n);
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
        }
    };
});
