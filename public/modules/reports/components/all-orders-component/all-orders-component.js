(function () {
    /* globals moment: false */
    /* globals angular: false */
    /* globals console: false */
    /* globals $: false */
    angular.module('adminPortal.reports').component('allOrdersComponent', {
        templateUrl: 'modules/reports/components/all-orders-component/all-orders-component.html',
        controllerAs: 'vm',
        bindings: {
            processing: '=?',
            exporting: '=?',
            customBrandFilter: '<',
            customReportsFilterType: '<',
            merchantSearch: '<'
        },
        controller: allOrdersController
    });

    function allOrdersController(Reports, AllOrdersService, ReportsGroupBySelections, UtilityService) {
        var vm = this;
        vm.reports_group_by_selections = ReportsGroupBySelections;
        vm.utility = UtilityService;
        vm.reportService = Reports;

        vm.selectedReportType = {
            desc: 'All Orders',
            key: 'all_orders',
            retrieveData: AllOrdersService.getAllOrders,
            exportReport: AllOrdersService.exportAllOrders,
            selectedOrderBy: null,
            columns: [
                {
                    title: 'Order ID',
                    key: 'order_id',
                    align: 'right',
                    filter: Reports.filterNone
                },
                {
                    title: 'User Name',
                    key: 'user_name',
                    align: 'right',
                    filter: Reports.filterNone
                },
                {
                    title: 'Order Date',
                    key: 'order_date',
                    align: 'right',
                    filter: Reports.filterNone
                },
                {
                    title: 'Order Amt',
                    key: 'order_amt',
                    align: 'right',
                    filter: Reports.filterNone
                },
                {
                    title: 'Total Tax Amt',
                    key: 'total_tax_amt',
                    align: 'right',
                    filter: Reports.filterNone
                },
                {
                    title: 'Promo Amt',
                    key: 'promo_amt',
                    align: 'right',
                    filter: Reports.filterNone
                },
                {
                    title: 'Delivery Amt',
                    key: 'delivery_amt',
                    align: 'right',
                    filter: Reports.filterNone
                },
                {
                    title: 'Tip Amt',
                    key: 'tip_amt',
                    align: 'right',
                    filter: Reports.filterNone
                },
                {
                    title: 'Grand Total',
                    key: 'grand_total',
                    align: 'right',
                    filter: Reports.filterNone
                },
                {
                    title: 'Order Qty',
                    key: 'order_qty',
                    align: 'right',
                    filter: Reports.filterNone
                }
            ]
        };

        vm.groupBy = [];



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
                    'Yesterday': [moment().subtract(1, 'days').startOf('day'), moment().startOf('day')],
                    'This Week': [moment().startOf('week'), moment()],
                    'Last Week': [moment().startOf('week').subtract(1, 'week'), moment().endOf('week').subtract(1, 'week')],
                    'This Month': [moment().startOf('month'), moment()],
                    'Last Month': [moment().startOf('month').subtract(1, 'month').startOf('month'), moment().startOf('month').subtract(1, 'month').endOf('month')]
                }
            }
        };
    }
})();
