angular.module('adminPortal.reports').factory('ReportsGroupBySelections',function(UtilityService) {

    var reportsGroupBySelections = {};

    reportsGroupBySelections.unselected_group_bys = [];
    reportsGroupBySelections.selected_group_bys = [];

    reportsGroupBySelections.resetSelections = resetSelections;
    reportsGroupBySelections.addGroupBy = addGroupBy;
    reportsGroupBySelections.removeGroupBy = removeGroupBy;
    reportsGroupBySelections.getGroupByList = getGroupByList;

    var current_report = '';

    function resetSelections(options, new_report) {
        if (current_report != new_report) {
            reportsGroupBySelections.unselected_group_bys = options.slice();
            reportsGroupBySelections.selected_group_bys = [];
        }
        current_report = new_report;
        $("#group-by-modal").modal("show");
    }

    function addGroupBy(group_by, index) {
        reportsGroupBySelections.selected_group_bys.push(group_by);
        reportsGroupBySelections.unselected_group_bys.splice(index, 1);
    }

    function removeGroupBy(group_by, index) {
        reportsGroupBySelections.unselected_group_bys.push(group_by);
        reportsGroupBySelections.selected_group_bys.splice(index, 1);
    }

    function getGroupByList() {
        var simple_group_by_array = UtilityService.convertObjectToSimpleArray(reportsGroupBySelections.selected_group_bys, 'key');
        return simple_group_by_array.join();
    }

    return reportsGroupBySelections;
});