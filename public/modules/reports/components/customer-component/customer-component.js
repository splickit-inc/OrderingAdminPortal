(function () {
    /* globals moment: false */
    /* globals angular: false */
    /* globals console: false */
    /* globals $: false */
    angular.module('adminPortal.reports').component('customerComponent', {
        templateUrl: 'modules/reports/components/customer-component/customer-component.html',
        controllerAs: 'vm',
        bindings: {
            processing: '=?',
            exporting: '=?',
            customBrandFilter: '<',
            customReportsFilterType: '<',
            merchantSearch: '<'
        },
        controller: customerReportComponentController
    });

    function customerReportComponentController(Reports, CustomersReportService, ReportsGroupBySelections, UtilityService) {
        var vm = this;
        vm.reports_group_by_selections = ReportsGroupBySelections;
        vm.utility = UtilityService;
        vm.reportService = Reports;

        vm.selectedReportType = {
            desc: 'Customers',
            key: 'customers',
            retrieveData: CustomersReportService.getCustomers,
            exportReport: CustomersReportService.exportCustomersReport,
            selectedOrderBy: null,
            columns: [
                {
                    title: 'User ID',
                    key: 'user_id',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'First Name',
                    key: 'first_name',
                    align: 'left',
                    filter: Reports.filterNone
                }, {
                    title: 'Last Name',
                    key: 'last_name',
                    align: 'left',
                    filter: Reports.filterNone
                }, {
                    title: 'Email',
                    key: 'email',
                    align: 'left',
                    filter: Reports.filterNone
                }, {
                    title: 'First Order Date',
                    key: 'first_order_date',
                    align: 'right',
                    filter: Reports.filterDate
                },{
                    title: 'Last Order Date',
                    key: 'last_order_date',
                    align: 'right',
                    filter: Reports.filterDate
                }, {
                    title: 'Total Orders',
                    key: 'total_orders',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Pickup Orders',
                    key: 'pickup_orders',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Delivery Orders',
                    key: 'delivery_orders',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Total Spent',
                    key: 'total_spent',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Average Order Value',
                    key: 'avg_order_value',
                    align: 'right',
                    filter: Reports.filterNumber
                }
            ]
        };

        vm.groupBy = [
            {
                desc: 'Hour of Day',
                key: 'order_hour'
            }];

        vm.reportDateOptions = [
            {
                desc: 'Last 30 Days',
                key: 'Last_30'
            },
            {
                desc: 'Last 60 Days',
                key: 'Last_60'
            },
            {
                desc: 'Last 90 Days',
                key: 'Last_90'
            },
            {
                desc: 'Last Year',
                key: 'Last_365'
            },
            {
                desc: 'Last 2 Years',
                key: 'Last_730'
            }
        ];

        vm.datePicker = {
            date: {startDate: moment().startOf('day'), endDate: moment()},
            options: {
                pickerClasses: 'custom-picker-colors',
                buttonClasses: 'btn',
                applyButtonClasses: 'btn-success',
                locale: {
                    applyLabel: "Apply",
                    cancelLabel: 'Cancel',
                    customRangeLabel: 'Custom',
                    format: "MMM-DD-YYYY" //will give you 2017-01-06
                    //format: "D-MMM-YY", //will give you 6-Jan-17
                    //format: "D-MMMM-YY", //will give you 6-January-17
                },
                ranges: {
                    'Today': [moment().startOf('day'), moment()],
                    'Yesterday': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
                    'This Week': [moment().startOf('week'), moment()],
                    'Last Week': [moment().startOf('week').subtract(1, 'week'), moment().endOf('week').subtract(1, 'week')],
                    'This Month': [moment().startOf('month'), moment()],
                    'Last Month': [moment().startOf('month').subtract(1, 'month').startOf('month'), moment().startOf('month').subtract(1, 'month').endOf('month')],
                    'Current Year': [moment().startOf('year'), moment()],
                    'Last Year': [moment().startOf('year').subtract(1, 'year').startOf('year'), moment().startOf('year').subtract(1, 'year').endOf('year')]
                }
            }
        };
    }
})();
