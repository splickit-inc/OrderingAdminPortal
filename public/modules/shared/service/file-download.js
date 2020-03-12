angular.module('shared').factory('FileDownload', function ($q, $http, UtilityService) {

    var fileDownload = {};


    fileDownload.download = function (url) {
        var deferredAbort = $q.defer();
        var request = $http({
            method: "get",
            url: url,
            responseType: 'arraybuffer',
            timeout: deferredAbort.promise
        });
        var promise = request.then(
            function (response) {
                var blob = new Blob([response.data]);
                if (window.navigator && window.navigator.msSaveOrOpenBlob) { // for IE
                    return (blob);
                }
                var url = URL.createObjectURL(blob);
                return (url);
            },
            function (response) {
                var str = UtilityService.arrayBufferToString(response.data)
                if(response.status!=-1){
                    response.data = angular.fromJson(str);
                }
                return ($q.reject(response));
            }
        );
        promise.abort = function () {
            deferredAbort.resolve();
        };
        promise.finally(
            function () {
                promise.abort = angular.noop;
                deferredAbort = request = promise = null;
            }
        );
        return (promise);
    }

    return fileDownload;
});