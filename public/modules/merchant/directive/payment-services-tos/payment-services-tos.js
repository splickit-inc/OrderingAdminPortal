angular.module('adminPortal.merchant').directive('paymentServicesTos', function() {

    var controller = function ($scope) {

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
        templateUrl: 'modules/merchant/directive/payment-services-tos/payment-services-tos.html',
        controller: controller,
        link: function(scope, element, attrs, fn) {

        }
    };
});
