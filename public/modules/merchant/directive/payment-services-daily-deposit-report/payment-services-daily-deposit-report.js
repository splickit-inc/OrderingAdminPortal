angular.module('adminPortal.merchant').directive('paymentServicesDailyDepositReport', function() {

    var controller = function ($scope) {

        $scope.searchParams = {
            load_report: false,
        }



        $scope.fieldNames = {
            report_date: {columnName: 'Report Date'},
            sweep_id: {columnName: 'Sweep ID'},
            amount: {columnName: 'Net Deposit'},
            gross_amount: {columnName: 'Amount'}
        };

        $scope.result_users = [];
    };

    return {
        restrict: 'E',
        replace: true,
        scope: {
            closeMerchantSelect: '&',
            selectedMerchantId: '=',
            //This parameter are optional
            searchFunction: '=?',
            autoSubmit: '=?',
            autoSearchVariable: '=?'
        },
        templateUrl: 'modules/merchant/directive/payment-services-daily-deposit-report/payment-services-daily-deposit-report.html',
        controller: controller,
        link: function(scope, element, attrs, fn) {

        }
    };
});
