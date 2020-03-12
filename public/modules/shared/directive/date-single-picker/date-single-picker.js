(function () {
    'use strict';
    angular.module('shared').directive('dateSinglePicker', function () {

        var controller = function ($scope) {
            $scope.current_date = moment().format('MM/DD/YYYY');
            $scope.startDate = moment().toDate();
            $scope.dateFormat = $scope.dateFormat || 'MM/DD/YYYY';
            $scope.iconOptions = {
                time: 'fa fa-clock-o',
                date: 'fa fa-calendar',
                up: 'fa fa-plus',
                down: 'fa fa-minus',
                next: 'fa fa-chevron-right',
                previous: 'fa fa-chevron-left'
            };
            //$scope.maxPeriodRange = 90; // 90 days

            function initDatePicker() {
                $(function () {
                    $('#set_date').datetimepicker({
                        format: $scope.dateFormat,
                        icons: $scope.iconOptions
                    });
                    $("#set_date").on("dp.change", function (e) {
                        $scope.startDate = e.date;
                        $scope.onDateChanged({
                            startDate: $scope.startDate
                        });
                    });

                });
            }

            initDatePicker();

            $scope.onDateChange = function () {
                $scope.onDateChanged({startDate:  $scope.startDate});
            }

            $scope.onDateChange();

        };

        return {
            restrict: 'EA',
            replace: true,
            scope: {
                onDateChanged: '&',
                new_hours_date: '@'
            },
            templateUrl: 'modules/shared/directive/date-single-picker/date-single-picker.html',
            controller: controller,
            link: function (scope, element, attrs, ctrl) {

            }
        };
    });
})();