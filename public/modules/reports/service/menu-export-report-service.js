(function () {
    'use strict';
    angular.module('adminPortal.reports').factory('MenuExportReportService', MenuExportReportService);

    function MenuExportReportService(UtilityService, FileDownload) {

        var menuExportReportService = {};

        menuExportReportService.getMenuExport = function (fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants) {
            var url = '/reports/menu_export';
            url = setUpParameters(url, page, orderBy, perPage, brand, merchants);
            return UtilityService.cancelableHttpGet(url);
        }

        menuExportReportService.exportMenuExport = function (fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants, setTimeRange) {
            var url = '/reports/menu_export/exported';
            url = setUpParameters(url, fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants, setTimeRange);
            return FileDownload.download(url);
        }

        function setUpParameters(url, page, orderBy, perPage, brand, merchants) {
            var p = '?';
            if (!!page) {
                url += p + 'page=' + page;
                p = '&';
            }
            if (!!orderBy) {
                url += p + 'order_by=' + orderBy;
                p = '&';
            }
            if (!!perPage) {
                url += p + 'per_page=' + perPage;
            }
            if (!!brand) {
                url += p + 'brand=' + brand;
            }
            if (!!perPage) {
                url += p + 'per_page=' + perPage;
            }
            if (!!merchants) {
                url += p + 'merchants=' + JSON.stringify(merchants);
            }
            return url;
        }

        return menuExportReportService;
    }
})();