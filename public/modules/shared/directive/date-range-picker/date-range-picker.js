(function () {
    'use strict';
    angular.module('shared').directive('dateRangePickerCustom', function () {

        var controller = function ($scope) {
            $scope.startDate = moment();
            $scope.endDate = moment();
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
            $scope.timePeriods = [{
                desc: 'Today',
                key: 'today',
                func: today
            }, {
                desc: 'Yesterday',
                key: 'yesterday',
                func: yesterday
            }, {
                desc: 'This Week',
                key: 'current_week',
                func: currentWeek
            }, {
                desc: 'Last Week',
                key: 'previous_week',
                func: previousWeek
            }, {
                desc: 'Month to Date',
                key: 'current_month',
                func: currentMonth
            }, {
                desc: 'Year to Date',
                key: 'current_year',
                func: currentYear
            }, {
                desc: 'Custom',
                key: 'custom',
                func: customPeriod
            }];

            $scope.selectedPeriod = $scope.timePeriods[0];

            function setMaxSelectableDate(minDate) {
                var max = moment().add(1, 'day');
                if (!!minDate) {
                    max = moment.min(minDate.clone().add($scope.maxPeriodRange, 'days'), max);
                }
                $('#to_date').data("DateTimePicker").minDate(minDate).maxDate(max);
            }

            function setMinSelectableDate(maxDate) {
                var min = moment().subtract($scope.maxPeriodRange, 'days');
                if (!!maxDate) {
                    min = maxDate.clone().subtract($scope.maxPeriodRange, 'days');
                }

                $('#from_date').data("DateTimePicker").minDate(min).maxDate(maxDate);
            }

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
                        setMaxSelectableDate($scope.startDate);
                        $scope.onPeriodChanged({
                            startDate: $scope.startDate,
                            endDate: $scope.endDate
                        });
                    });
                    $("#to_date").on("dp.change", function (e) {
                        $scope.endDate = e.date;
                        setMinSelectableDate($scope.endDate);
                        $scope.onPeriodChanged({
                            startDate: $scope.startDate,
                            endDate: $scope.endDate
                        });
                    });
                    setMinSelectableDate();
                    setMaxSelectableDate();
                });
            }

            initDatePickers();

            //#region Period filters

            function today() {
                $scope.startDate = moment();
                $scope.endDate = moment();
            }

            function yesterday() {
                $scope.startDate = moment().subtract(1, 'day');
                $scope.endDate = moment().subtract(1, 'day');
            }

            function currentWeek() {
                $scope.startDate = moment().startOf('week');
                $scope.endDate = moment();
            }

            function previousWeek() {
                $scope.startDate = moment().startOf('week').subtract(1, 'week');
                $scope.endDate = moment().startOf('week').subtract(1, 'day');
            }

            function currentMonth() {
                $scope.startDate = moment().startOf('month');
                $scope.endDate = moment();
            }

            function currentYear() {
                $scope.startDate = moment().startOf('year');
                $scope.endDate = moment();
            }

            function customPeriod() {
                today();
                $('#from_date').data("DateTimePicker").date($scope.startDate);
                $('#to_date').data("DateTimePicker").date($scope.endDate);
            }

            //#endregion

            $scope.onPeriodChange = function () {
                if (!$scope.selectedPeriod) {
                    return;
                }
                $scope.selectedPeriod.func();
                if ($scope.selectedPeriod.key !== 'custom') {
                    $scope.onPeriodChanged({
                        startDate: $scope.startDate,
                        endDate: $scope.endDate,
                        selectedPeriod: $scope.selectedPeriod.key
                    });
                }
            };
            $scope.onPeriodChange();
        };

        return {
            restrict: 'EA',
            replace: true,
            scope: {
                onPeriodChanged: '&',
                dateFormat: '@',
                maxPeriodRangeConfiguration: '=?',
                rangeDisableList: '=?'
            },
            templateUrl: 'modules/shared/directive/date-range-picker/date-range-picker.html',
            controller: controller,
            link: function (scope, element, attrs, ctrl) {
                if (!!scope.maxPeriodRangeConfiguration) {
                    scope.maxPeriodRange = scope.maxPeriodRangeConfiguration;
                }
                else {
                    scope.maxPeriodRange = 90;
                }

                if (!!scope.rangeDisableList && scope.rangeDisableList.constructor === Array) {
                    scope.rangeDisableList.forEach(function (value) {
                        scope.timePeriods.forEach(function (item) {
                            if (item.key === value) {
                                scope.timePeriods.splice(scope.timePeriods.indexOf(item), 1);
                            }
                        });
                    });
                    console.log(scope.timePeriods);
                }
            }
        };
    });
})();
