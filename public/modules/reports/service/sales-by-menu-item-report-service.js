(function () {
    'use strict';
    angular.module('adminPortal.reports').factory('SalesByMenuItemReportService', SalesByMenuItemReportService);

    function SalesByMenuItemReportService(UtilityService, FileDownload) {

        var salesByMenuItemReportService = {};

        salesByMenuItemReportService.getReport = function (fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants) {
            var url = '/reports/sales_menu_items';
            url = setUpParameters(url, fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants);
            return UtilityService.cancelableHttpGet(url);
        };

        salesByMenuItemReportService.exportReport = function (fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants) {
            var url = '/reports/sales_menu_items/exported';
            url = setUpParameters(url, fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants);
            return FileDownload.download(url);
        };

        function setUpParameters(url, fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants) {
            var p = '?';
            if (!!fromDate && !!toDate) {
                url += p + 'date_range=' + fromDate + ',' + toDate;
                p = '&';
            }
            if (!!page) {
                url += p + 'page=' + page;
                p = '&';
            }
            if (!!groupBy) {
                url += p + 'group_by=' + groupBy;
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
            if (!!merchants) {
                url += p + 'merchants=' + JSON.stringify(merchants);
            }
            return url;
        }
        return salesByMenuItemReportService;
    }
})();
