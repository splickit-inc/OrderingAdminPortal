(function () {
    'use strict';
    angular.module('adminPortal.reports').factory('AllOrdersService', AllOrdersService);

    function AllOrdersService(UtilityService, FileDownload) {

        var allOrdersService = {};

        allOrdersService.getAllOrders = function (fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants) {
            var url = '/reports/all_orders'
            url = setUpParameters(url, fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants);
            return UtilityService.cancelableHttpGet(url);
        }

        allOrdersService.exportAllOrders = function (fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants) {
            var url = '/reports/all_orders/exported'
            url = setUpParameters(url, fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants);
            return FileDownload.download(url);
        }

        function setUpParameters(url, fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants) {
            console.log('brand', brand);
            console.log('merchant', merchants);
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

        return allOrdersService;
    }
})();