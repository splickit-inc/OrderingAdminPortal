(function () {
    'use strict';
    angular.module('adminPortal.reports').factory('MerchantConfigService', MerchantConfigService);

    function MerchantConfigService(UtilityService, FileDownload) {

        var merchantConfigReportService = {};

        merchantConfigReportService.getMerchantConfig = function (fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants) {
            var url = '/reports/merchant_config'
            url = setUpParameters(url, page, orderBy, perPage, brand, merchants);
            return UtilityService.cancelableHttpGet(url);
        }

        merchantConfigReportService.exportMerchantConfig = function (fromDate, toDate, page, groupBy, orderBy, perPage, brand, merchants) {
            var url = '/reports/merchant_config/exported'
            url = setUpParameters(url, page, orderBy, perPage, brand, merchants);
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

        return merchantConfigReportService;
    }
})();