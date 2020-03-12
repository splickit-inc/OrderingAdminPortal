(function () {
    /* globals moment: false */
    /* globals angular: false */
    /* globals console: false */
    /* globals $: false */
    angular.module('adminPortal.reports').component('promosReportComponent', {
        templateUrl: 'modules/reports/components/promos-report-component/promos-report-component.html',
        controllerAs: 'vm',
        bindings: {
            customBrandFilter: '<',
            customReportsFilterType: '<',
            merchantSearch: '<',
            processing: '=?',
            exporting: '=?'
        },
        controller: promoReportComponentController
    });

    function promoReportComponentController($http, Users, UtilityService, PromoReportService, ReportsGroupBySelections, Reports) {
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
                key: 'Date'
            }, {
                desc: 'Merchant',
                key: 'merchant_id'
            }, {
                desc: 'Promo ID',
                key: 'promo_id'
            }, {
                desc: 'Promo Code',
                key: 'promo_code'
            }, {
                desc: 'Order Type',
                key: 'order_type'
            }, {
                desc: 'Group Order',
                key: 'group_order'
            }];

        vm.selectedReportType = {
            desc: 'Promos',
            key: 'promos',
            retrieveData: PromoReportService.getReport,
            exportReport: PromoReportService.exportReport,
            selectedOrderBy: null,
            columns: [
                {
                    title: 'Order #',
                    key: 'order_cnt',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Order Amt',
                    key: 'order_amt',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Promo Amt',
                    key: 'promo_amt',
                    align: 'right',
                    filter: Reports.filterNone
                }, {
                    title: 'Delivery Amt',
                    key: 'delivery_amt',
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
