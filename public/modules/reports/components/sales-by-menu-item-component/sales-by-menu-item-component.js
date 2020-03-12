(function () {
    /* globals moment: false */
    /* globals angular: false */
    /* globals console: false */
    /* globals $: false */
    angular.module('adminPortal.reports').component('salesByMenuItemComponent', {
        templateUrl: 'modules/reports/components/sales-by-menu-item-component/sales-by-menu-item-component.html',
        controllerAs: 'vm',
        bindings: {
            customBrandFilter: '<',
            customReportsFilterType: '<',
            merchantSearch: '<',
            processing: '=?',
            exporting: '=?'
        },
        controller: salesByMenuItemReportComponentController
    });

    function salesByMenuItemReportComponentController($http, Users, UtilityService, SalesByMenuItemReportService, ReportsGroupBySelections, Reports) {
        var vm = this;

        vm.user = Users;
        vm.reports_group_by_selections = ReportsGroupBySelections;

        vm.datePicker = {
            date: {startDate: moment(), endDate: moment()},
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
                    'Last Year': [moment().startOf('year').subtract(1, 'year').startOf('year'), moment().startOf('year').subtract(1, 'year').endOf('year')],
                }
            }
        };

        vm.groupBy = [
            {
                desc: 'Date',
                key: 'order_date'
            }, {
                desc: 'Day of Week',
                key: 'order_day_of_week'
            }, {
                desc: 'Month',
                key: 'order_month'
            }, {
                desc: 'Year',
                key: 'order_year'
            }, {
                desc: 'Merchant',
                key: 'merchant_id'
            }];

        vm.selectedReportType = {
            desc: 'Sales By Menu Items',
            key: 'sales_by_menu_items',
            retrieveData: SalesByMenuItemReportService.getReport,
            exportReport: SalesByMenuItemReportService.exportReport,
            selectedOrderBy: null,
            columns: [
                {
                    title: 'Size ID',
                    key: 'item_size_id',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Size Name',
                    key: 'Size_Name',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Item Name',
                    key: 'Item_Name',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Menu Type',
                    key: 'Menu_Type',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Date',
                    key: 'Date',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Item Total',
                    key: 'Item_Total',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Item Count',
                    key: 'Item_Count',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Item Total with Modifiers',
                    key: 'Item_Total_with_Modifiers',
                    align: 'right',
                    filter: Reports.filterNone
                }]
        };

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
