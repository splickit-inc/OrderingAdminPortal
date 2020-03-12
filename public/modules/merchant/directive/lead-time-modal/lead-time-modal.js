angular.module('adminPortal.merchant').directive('leadTimeModal', function () {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            handlerName: '=',
            headerTitle: '=?',
            currentLeadTimeSelected: '=?',
            formModel: '=?',
            submitButton: '&?',
            cancelButton: '&?'
        },
        templateUrl: 'modules/merchant/directive/lead-time-modal/lead-time-modal.html',
        link: function (scope, element, attrs, fn) {

            if (!!scope.formModel) {
                scope.formModel = scope.holiday_form;
            }

            scope.weekDay = [
                {id: '2', name: 'Monday'},
                {id: '3', name: 'Tuesday'},
                {id: '4', name: 'Wednesday'},
                {id: '5', name: 'Thursday'},
                {id: '6', name: 'Friday'},
                {id: '7', name: 'Saturday'},
                {id: '1', name: 'Sunday'}
            ];
            scope.type = [
                {id: 'R', name: 'Pickup'},
                {id: 'D', name: 'Delivery'}
            ];
        }
    };
});
