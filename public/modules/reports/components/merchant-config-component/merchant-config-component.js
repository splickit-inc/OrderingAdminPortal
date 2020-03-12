(function () {
    /* globals moment: false */
    /* globals angular: false */
    /* globals console: false */
    /* globals $: false */
    angular.module('adminPortal.reports').component('merchantConfigComponent', {
        templateUrl: 'modules/reports/components/merchant-config-component/merchant-config-component.html',
        controllerAs: 'vm',
        bindings: {
            processing: '=?',
            exporting: '=?',
            customBrandFilter: '<',
            customReportsFilterType: '<',
            merchantSearch: '<'
        },
        controller: merchantConfigComponentController
    });

    function merchantConfigComponentController(Reports, MerchantConfigService, ReportsGroupBySelections, UtilityService) {
        var vm = this;
        vm.reports_group_by_selections = ReportsGroupBySelections;
        vm.utility = UtilityService;
        vm.reportService = Reports;

        vm.getMerchantConfig = MerchantConfigService.getMerchantConfig();

        vm.selectedReportType = {
            desc: 'Merchants Config',
            key: 'merchants-config',
            retrieveData: MerchantConfigService.getMerchantConfig,
            exportReport: MerchantConfigService.exportMerchantConfig,
            selectedOrderBy: null,
            columns: [
                {
                    title: 'Merchant ID',
                    key: 'merchant_id',
                    align: 'right',
                    filter: Reports.filterNone
                },
                {
                    title: 'Store ID',
                    key: 'display_name',
                    align: 'right',
                    filter: Reports.filterNone
                },
                {
                    title: 'Ordering',
                    key: 'ordering_on',
                    align: 'right',
                    filter: Reports.filterNone
                },
                {
                    title: 'Delivery',
                    key: 'delivery',
                    align: 'right',
                    filter: Reports.filterNone
                },
                {
                    title: 'Advanced Ordering',
                    key: 'advanced_ordering',
                    align: 'right',
                    filter: Reports.filterNone
                },
                {
                    title: 'Group Ordering',
                    key: 'group_ordering_on',
                    align: 'right',
                    filter: Reports.filterNone
                },
                {
                    title: 'Catering',
                    key: 'catering_on',
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
