(function () {
    /* globals moment: false */
    /* globals angular: false */
    /* globals console: false */
    /* globals $: false */
    angular.module('adminPortal.reports').component('transactionsComponent', {
        templateUrl: 'modules/reports/components/transactions-component/transactions-component.html',
        controllerAs: 'vm',
        bindings: {
            processing: '=?',
            exporting: '=?',
            customBrandFilter: '<',
            customReportsFilterType: '<',
            merchantSearch: '<'
        },
        controller: transactionsReportComponentController
    });

    function transactionsReportComponentController(Reports, TransactionsReportService, ReportsGroupBySelections, UtilityService) {
        var vm = this;
        vm.reports_group_by_selections = ReportsGroupBySelections;
        vm.utility = UtilityService;

        vm.selectedReportType = {
            desc: 'Transactions',
            key: 'transactions',
            retrieveData: TransactionsReportService.getTransactions,
            exportReport: TransactionsReportService.exportTransactionsReport,
            selectedOrderBy: null,
            columns: [
                {
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
                    title: 'Order Amount',
                    key: 'order_amount',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Tip',
                    key: 'tip',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Tax',
                    key: 'tax',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Discount',
                    key: 'discount',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Delivery Fee',
                    key: 'delivery_fee',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Grand Total',
                    key: 'grand_total',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Average Order Value',
                    key: 'avg_order_value',
                    align: 'right',
                    filter: Reports.filterNumber
                }]
        };

        vm.groupBy = [
            {
                desc: 'Date',
                key: 'order_date'
            }, {
                desc: 'Hour of Day',
                key: 'order_hour'
            }, {
                desc: 'Day of Week',
                key: 'order_day_of_week'
            }, {
                desc: 'Month',
                key: 'order_month'
            }, {
                desc: 'Year',
                key: 'order_year'
            },
            // {
            //     desc: 'Order Type',
            //     key: 'order_type'
            // },
            {
                desc: 'Payment Type',
                key: 'payment_type'
            }, {
                desc: 'Merchant',
                key: 'merchant_id'
            }];

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
