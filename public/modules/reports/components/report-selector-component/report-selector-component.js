(function () {
    /* globals moment: false */
    /* globals angular: false */
    /* globals console: false */
    /* globals $: false */
    angular.module('adminPortal.reports').component('reportSelectorComponent', {
        templateUrl: 'modules/reports/components/report-selector-component/report-selector-component.html',
        controllerAs: 'vm',
        bindings: {
            selectedReportType: '=',
            processing: '=?',
            exporting: '=?',
            customBrandFilter: '<',
            customReportsFilterType: '<',
            merchantSearch: '<'
        },
        controller: transactionsReportComponentController
    });

    function transactionsReportComponentController() {
        var vm = this;
    }
})();
