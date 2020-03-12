angular.module('shared').factory('SweetAlert', ['$window', '$rootScope', function SweetAlert($window, $rootScope) {
    var $swal = $window;

    return {
        swal: swal
    }

    function swal(config, onConfirm) {
        if (!onConfirm || !(onConfirm instanceof Function)) {
            return $swal.swal(config);
        }
        return $swal.swal(config).then(
            function () {
                $rootScope.$evalAsync(function () {
                    onConfirm(true);
                })
            },
            function () {
                $rootScope.$evalAsync(function () {
                    onConfirm(false);
                })
            });
    }
}]);