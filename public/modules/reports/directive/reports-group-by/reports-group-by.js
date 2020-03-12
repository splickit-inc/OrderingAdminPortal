angular.module('adminPortal.reports').directive('reportsGroupBy', function() {
    var controller = function ($scope, ReportsGroupBySelections) {
        $scope.groupBySelections = ReportsGroupBySelections;

        $scope.closeModal = function() {
            $("#group-by-modal").modal("hide");
        }
    };

    return {
        restrict: 'E',
        replace: true,
        scope: {

        },
        templateUrl: 'modules/reports/directive/reports-group-by/reports-group-by.html',
        controller: controller,
        link: function(scope, element, attrs, fn) {


        }
    };
});
