(function () {
    /* globals moment: false */
    /* globals angular: false */
    /* globals console: false */
    /* globals $: false */
    angular.module('adminPortal.reports').component('overviewComponent', {
        templateUrl: 'modules/reports/components/overview-component/overview-component.html',
        controllerAs: 'vm',
        bindings: {
            brands: '<'
        },
        controller: overviewComponentController
    });

    function overviewComponentController(Reports, Users, Brands, UtilityService, EmbeddedMerchantSearch) {
        var vm = this;
        vm.summary = undefined;
        vm.merchant_search = EmbeddedMerchantSearch;
        vm.usersService = Users;
        vm.utilityService = UtilityService;
        vm.filter_type = 'brand';
        vm.time_periods = Reports.time_periods;
        vm.current_time_period = 'today';
        vm.merchant_subset = false;
        vm.merchant_open_source = '';
        vm.initial_reports_load = false;
        vm.loading_main_reports = false;
        vm.line_sales_series = ['$ Sales', 'Transactions'];
        vm.bar_sales_series = ['$ Total Daily Sales'];
        vm.processing = false;

        vm.filterTypeClass = filterTypeClass;
        vm.fiftyOpacityOffset = fiftyOpacityOffset;
        vm.merchantSubsetChange = merchantSubsetChange;
        vm.runGraphReports = runGraphReports;


        function load() {
            Users.getUserSessionInfo().then(function (current_user) {
                if (Users.visibility === "brand") {
                    vm.filter_type = 'brand';
                    vm.brand_filter = vm.usersService.user_related_data.brand_id;
                    vm.custom_brand_filter = vm.usersService.user_related_data.brand_id;
                }

                if (Users.visibility === 'operator') {
                    vm.filter_type = 'merchant';
                    vm.custom_reports_filter_type = 'merchant';
                    vm.merchant_search.selected_merchants = [vm.usersService.operator_merchant];
                }
            });
        }

        function merchantSubsetChange() {
            if (vm.merchant_subset) {
                vm.filter_type = 'merchant';
            }
            else {
                vm.filter_type = 'brand';
            }
        }

        function filterTypeClass(value) {
            if (vm.filter_type === value) {
                return 'btn-success';
            }
            else {
                return 'btn-default';
            }
        }

        function runGraphReports() {
            vm.processing = true;

            var data = {};
            var url = 'overview';
            vm.loading_main_reports = true;

            if (vm.filter_type === 'brand') {
                data = {
                    brand_id: vm.brand_filter
                };
            }
            else {
                var report_merchants = UtilityService.convertArrayObjectsPropertyToArray(vm.merchant_search.selected_merchants, 'merchant_id');
                data = {
                    merchants: report_merchants
                };
            }

            data.time_range = vm.current_time_period;

            vm.summary = undefined;
            Reports.post(url, data).success(function (response) {
                vm.initial_reports_load = true;
                vm.summary = response.summary;
                vm.summary_secondary = response.summary_secondary;
                var data = response;
                setChartData(data.labels, data.chart_values, data.device_sales.labels, data.device_sales.sales, data.item_sales,
                    data.user_sales, data.pickup_delivery.labels, data.pickup_delivery.chart_values, data.day_of_week.labels, data.day_of_week.sales);
                vm.loading_main_reports = false;
                vm.processing = false;
            }).error(function (error) {
                console.log(error);
                vm.processing = false;
            });
        }

        function setChartData(x_axis_labels, sales, device_labels, device_counts, item_leaders, user_leaders, pickup_delivery_labels, pickup_delivery_counts, day_of_week_labels, day_of_week_sales) {
            vm.labels_hours = x_axis_labels;
            vm.sales = sales;

            vm.device_labels = device_labels;
            vm.device_counts = device_counts;

            vm.pickup_delivery_labels = pickup_delivery_labels;
            vm.pickup_delivery_counts = pickup_delivery_counts;

            vm.user_leaders = user_leaders;
            vm.item_leaders = item_leaders;

            vm.day_of_week_labels = day_of_week_labels;
            vm.day_of_week_sales = day_of_week_sales;
            vm.day_of_week_sales.series = ['Sales'];

            vm.series = ['Sales', 'Orders Count'];

            vm.onClick = function (points, evt) {
            };

            vm.datasetOverride = [{yAxisID: 'y-axis-1'}, {yAxisID: 'y-axis-2'}];

            vm.options = {
                scales: {
                    yAxes: [
                        {
                            id: 'y-axis-1',
                            type: 'linear',
                            display: true,
                            position: 'left'
                        },
                        {
                            id: 'y-axis-2',
                            type: 'linear',
                            display: true,
                            position: 'right'
                        }
                    ]
                },
                legend: {
                    display: true,
                    lables: {
                        fontColor: '#ffffff'
                    }
                }
            };

            vm.default_report_options = {
                legend: {display: true}
            };
        }

        function fiftyOpacityOffset() {
            if (vm.loading_main_reports) {
                return 'fifty-opacity';
            }
            else {
                return '';
            }
        }

        load();
    }
})();
