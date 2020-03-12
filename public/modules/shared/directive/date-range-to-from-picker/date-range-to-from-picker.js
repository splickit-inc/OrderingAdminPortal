(function () {
    'use strict';
    angular.module('shared').directive('dateRangeToFromPicker', function () {

        var controller = function ($scope) {
            $scope.startDate = undefined;
            $scope.endDate = undefined;
            $scope.dateOptions = {};
            $scope.dateFormat = $scope.dateFormat || 'MM/DD/YYYY';
            $scope.iconOptions = {
                time: 'fa fa-clock-o',
                date: 'fa fa-calendar',
                up: 'fa fa-plus',
                down: 'fa fa-minus',
                next: 'fa fa-chevron-right',
                previous: 'fa fa-chevron-left'
            };
            $scope.maxPeriodRange = 90; // 90 days

            function initDatePickers() {
                $(function () {
                    $('#from_date').datetimepicker({
                        format: $scope.dateFormat,
                        icons: $scope.iconOptions
                    });
                    $('#to_date').datetimepicker({
                        format: $scope.dateFormat,
                        icons: $scope.iconOptions,
                        useCurrent: false //Important! See issue #1075
                    });
                    $("#from_date").on("dp.change", function (e) {
                        $scope.startDate = e.date;
                        $scope.onPeriodChanged({
                            startDate: $scope.startDate,
                            endDate: $scope.endDate
                        });
                    });
                    $("#to_date").on("dp.change", function (e) {
                        $scope.endDate = e.date;
                        $scope.onPeriodChanged({
                            startDate: $scope.startDate,
                            endDate: $scope.endDate
                        });
                    });
                });
            }

            initDatePickers();


            $scope.onPeriodChange = function () {
                $scope.onPeriodChanged({startDate: $scope.startDate, endDate: $scope.endDate});
            }

            $scope.onPeriodChange();

            $scope.dateOptions.setDates = function(startDate, endDate) {

                if (typeof startDate != 'undefined') {
                    var start_date = new Date(startDate);
                    var end_date = new Date(endDate);

                    $('#from_date').data("DateTimePicker").date(start_date);
                    $('#to_date').data("DateTimePicker").date(end_date);
                }

                $(function () {
                    $('#from_date').datetimepicker({
                        format: $scope.dateFormat,
                        icons: $scope.iconOptions
                    });
                    $('#to_date').datetimepicker({
                        format: $scope.dateFormat,
                        icons: $scope.iconOptions,
                        useCurrent: false //Important! See issue #1075
                    });
                    $("#from_date").on("dp.change", function (e) {
                        $scope.startDate = e.date;
                        $scope.onPeriodChanged({
                            startDate: $scope.startDate,
                            endDate: $scope.endDate
                        });
                    });
                    $("#to_date").on("dp.change", function (e) {
                        $scope.endDate = e.date;
                        $scope.onPeriodChanged({
                            startDate: $scope.startDate,
                            endDate: $scope.endDate
                        });
                    });
                });

            }
        };

        return {
            restrict: 'EA',
            replace: true,
            scope: {
                onPeriodChanged: '&',
                dateOptions: '=',
                dateFormat: '@',
                savedStartDate: '=',
                savedEndDate: '=',
                loadedDates: '='
            },
            templateUrl: 'modules/shared/directive/date-range-to-from-picker/date-range-to-from-picker.html',
            controller: controller,
            link: function (scope, element, attrs, ctrl) {

            }
        };
    });
})();