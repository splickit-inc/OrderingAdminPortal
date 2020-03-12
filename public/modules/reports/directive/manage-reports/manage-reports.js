angular.module('adminPortal.reports').directive('manageReports', function() {
    var controller = function ($scope, ScheduledReports, SweetAlert, UtilityService) {
        ScheduledReports.getScheduledReports();
        $scope.scheduled_reports = ScheduledReports;

        $scope.openEditReport = function(index, report) {
            ScheduledReports.edit_report = report;
            ScheduledReports.edit_report.index = index;
            $("#edit-scheduled-report-modal").modal("show");
        }

        $scope.deleteReportDialog = function(report) {
            SweetAlert.swal({
                    title: "Warning",
                    text: "Are you sure you want to delete the report "+report.report_name+"?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Delete"
                },
                function (isConfirm) {
                    if (isConfirm) {
                        ScheduledReports.deleteReport(report.id).then(function() {
                            var report_index = UtilityService.findIndexByKeyValue(ScheduledReports.reports, 'id', report.id);
                            ScheduledReports.reports.splice(report_index, 1);
                        });
                    }
                });
        }
    };

    return {
        restrict: 'E',
        replace: true,
        scope: {

        },
        controller: controller,
        templateUrl: 'modules/reports/directive/manage-reports/manage-reports.html',
        link: function(scope, element, attrs, fn) {


        }
    };
});
