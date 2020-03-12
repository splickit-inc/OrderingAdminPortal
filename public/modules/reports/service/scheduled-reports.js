angular.module('adminPortal.reports').factory('ScheduledReports',function($http) {

    var scheduledReports = {};
    scheduledReports.reports = [];
    scheduledReports.edit_report = {};
    scheduledReports.edit_form_submit = false;

    scheduledReports.frequencies = [
        {
            id: 'daily',
            name: 'Daily'
        },
        {
            id: 'weekly',
            name: 'Weekly'
        },
        {
            id: 'monthly',
            name: 'Monthly'
        }
    ];


    scheduledReports.getScheduledReports = function() {
        return $http.get('scheduled_reports').then(function(response) {
            scheduledReports.reports = response.data;
        });
    }

    scheduledReports.updatedReport = function() {
        return $http.put('scheduled_reports/'+scheduledReports.edit_report.id, scheduledReports.edit_report).then(function(response) {
            scheduledReports.reports[scheduledReports.edit_report.index] = response.data;
            $("#edit-scheduled-report-modal").modal("hide");
        });
    }

    scheduledReports.deleteReport = function(report_id) {
        return $http.delete('scheduled_reports/'+report_id).then(function(response) {

        });
    }

    return scheduledReports;
});