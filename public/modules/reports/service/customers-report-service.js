(function () {
    'use strict';
    angular.module('adminPortal.reports').factory('CustomersReportService', CustomersReportService);

    function CustomersReportService(UtilityService, FileDownload) {

        var customersReportService = {};

        customersReportService.getCustomers = function (fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants, setTimeRange) {
            var url = '/reports/customers'
            url = setUpParameters(url, fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants, setTimeRange);
            return UtilityService.cancelableHttpGet(url);
        }

        customersReportService.exportCustomersReport = function (fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants, setTimeRange) {
            var url = '/reports/customers/exported';
            url = setUpParameters(url, fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants, setTimeRange);
            return FileDownload.download(url);
        }

        function setUpParameters(url, fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants, setTimeRange) {
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
            if (!!perPage) {
                url += p + 'per_page=' + perPage;
            }
            if (!!merchants) {
                url += p + 'merchants=' + JSON.stringify(merchants);
            }
            if (!!setTimeRange) {
                url += p + 'set_time_range=' + setTimeRange;
            }
            return url;
        }

        return customersReportService;
    }
})();