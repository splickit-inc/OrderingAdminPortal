(function () {
    /* globals moment: false */
    /* globals angular: false */
    /* globals console: false */
    /* globals $: false */
    angular.module('adminPortal.reports').component('defaultReportViewComponent', {
        templateUrl: 'modules/reports/components/default-report-view-component/default-report-view-component.html',
        controllerAs: 'vm',
        bindings: {
            selectedReportType: '=',
            processing: '=?',
            exporting: '=?',
            selectedGroupBy: '<',
            customBrandFilter: '<',
            scheduleOption: '<',
            customReportsFilterType: '<',
            startDate: '<',
            endDate: '<',
            setTimeRange: '<',
            merchantSearch: '<'
        },
        controller: defaultReportViewComponentController
    });

    function defaultReportViewComponentController($scope, $http, $filter, SweetAlert, UtilityService, Reports, ReportsGroupBySelections) {
        var vm = this;

        vm.isValidToSchedule = isValidToSchedule;
        vm.runReport = runReport;
        vm.exportReport = exportReport;
        vm.onPageChanged = onPageChanged;
        vm.sortByColumn = sortByColumn;
        vm.openAddScheduleModal = openAddScheduleModal;

        vm.groupBy = Reports.groupBy;

        vm.totalData = 0;
        vm.page = 1;
        vm.perPage = 25;
        vm.perPageOptions = [10, 25, 50];

        function isValidToSchedule() {
            if (vm.customReportsFilterType === 'brand' && (!!!vm.customBrandFilter)) {
                return true;
            }
            return vm.customReportsFilterType === 'merchant' && (vm.merchantSearch.selected_merchants.length === 0);
        }

        function cancelReport() {
            if (!!vm.dataRequest) {
                vm.dataRequest.abort();
                vm.processing = false;
                vm.exporting = false;
            }
        }

        function runReport() {
            if (vm.processing) {
                cancelReport();
                return;
            }
            loadDataList(1);
        }

        function exportReport() {
            if (vm.exporting) {
                cancelReport();
                return;
            }
            downloadReportFile();
        }

        function loadDataList(page) {
            console.log(vm.setTimeRange);
            if (!vm.selectedReportType) {
                return;
            }
            cancelReport();
            vm.processing = true;

            (vm.dataRequest = vm.selectedReportType.retrieveData(
                    vm.startDate,
                    vm.endDate,
                    !page ? vm.page : page,
                    ReportsGroupBySelections.getGroupByList(),
                    vm.selectedReportType.selectedOrderBy,
                    vm.perPage,
                    vm.customReportsFilterType === 'brand' ? vm.customBrandFilter : false,
                    vm.customReportsFilterType === 'merchant' ? UtilityService.convertArrayObjectsPropertyToArray(vm.merchantSearch.selected_merchants, 'merchant_id') : false,
                    Reports.period_type.key
                )
            ).then(function (response) {
                vm.processing = false;
                var result = response.data;
                vm.dataList = result.data;
                vm.totalData = result.total;
                vm.page = result.current_page;
                vm.orderBy = vm.selectedReportType.columns.slice(0);
                addGroupByColumn();
            }).catch(function (response) {
                if (response.status !== -1) {
                    vm.processing = false;
                    var errors = UtilityService.formatErrors(response.data.errors);
                    showError(errors);
                }
            });
        }

        function downloadReportFile() {
            if (!vm.selectedReportType) {
                return;
            }
            cancelReport();
            vm.exporting = true;

            (vm.dataRequest = vm.selectedReportType.exportReport(
                    vm.startDate,
                    vm.endDate,
                    false,
                    ReportsGroupBySelections.getGroupByList(),
                    vm.selectedReportType.selectedOrderBy,
                    false,
                    vm.customReportsFilterType === 'brand' ? vm.customBrandFilter : false,
                    vm.customReportsFilterType === 'merchant' ? UtilityService.convertArrayObjectsPropertyToArray(vm.merchantSearch.selected_merchants, 'merchant_id') : false,
                    Reports.period_type.key
                )
            ).then(function (data) {
                vm.exporting = false;
                var filename = vm.selectedReportType.key + '_report_' + vm.startDate + '_to_' + vm.endDate +
                    (!vm.selectedGroupBy ? '' : '_group_by_' + vm.selectedGroupBy.key) + '.csv';
                if (window.navigator && window.navigator.msSaveOrOpenBlob) { // for IE
                    window.navigator.msSaveOrOpenBlob(data, filename);
                    return;
                }
                var link = document.createElement('a');
                link.style = 'display: none';
                link.href = data;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                setTimeout(function () {
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(data)
                }, 100);
            }).catch(function (response) {
                if (response.status !== -1) {
                    vm.exporting = false;
                    var errors = UtilityService.formatErrors(response.data.errors);
                    showError(errors);
                }
            });
        }

        function showError(errorMsg) {
            console.log("SHOW ERROR PLS: " + errorMsg);
            SweetAlert.swal({
                title: "Error",
                html: errorMsg,
                type: "error",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "OK"
            });
        }

        function onPageChanged(newPage) {
            vm.page = newPage;
            loadDataList();
        }

        function sortByColumn(column) {
            if (!vm.selectedReportType || !vm.orderBy || !vm.orderBy.length) {
                return;
            }
            var selectedOrderBy = null;
            for (var i = 0; i < vm.orderBy.length; i++) {
                if (vm.orderBy[i].key === column) {
                    if (!vm.orderBy[i].sort) {
                        vm.orderBy[i].sort = '+';
                    }
                    else if (vm.orderBy[i].sort === '+') {
                        vm.orderBy[i].sort = '-';
                    }
                    else if (vm.orderBy[i].sort === '-') {
                        vm.orderBy[i].sort = null;
                    }
                    selectedOrderBy = !vm.orderBy[i].sort ? null : vm.orderBy[i];
                } else {
                    vm.orderBy[i].sort = null;
                }
            }
            if (!selectedOrderBy) {
                vm.selectedReportType.selectedOrderBy = null;
            }
            else {
                vm.selectedReportType.selectedOrderBy = selectedOrderBy.sort + selectedOrderBy.key;
            }
            loadDataList();
        }

        //Clear any pending request
        $scope.$on("$destroy",
            function (event) {
                cancelReport();
            }
        );

        function addGroupByColumn() {
           var group_bys = ReportsGroupBySelections.selected_group_bys;

            for (var i = 0; i < group_bys.length; i++) {
                var group_by = group_bys[i];

                var lastOrderBy = vm.orderBy[vm.orderBy.length - 1].key;
                if (lastOrderBy === group_by.key) {
                    return;
                }

                if (!!vm.selectedReportType.selectedOrderBy && vm.selectedReportType.selectedOrderBy.indexOf(lastOrderBy) !== -1) {
                    vm.selectedReportType.selectedOrderBy = null;
                }
                var filter = group_by.key === 'order_date' ? Reports.filterDate : Reports.filterNone;

                if (group_by.key == 'merchant_id') {
                    vm.orderBy.unshift({
                        title: 'Display Name',
                        key: 'store_name',
                        align: 'right',
                        filter: filter
                    });
                }

                vm.orderBy.unshift({
                    title: group_by.desc,
                    key: group_by.key,
                    align: 'right',
                    filter: filter
                });
            }


        }

        function openAddScheduleModal() {
            $('#add-schedule-modal').modal('show');
        }

        vm.addNewSchedule = addNewSchedule;

        vm.frequency = [
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

        vm.newSchedule = {};

        function addNewSchedule() {
            if (vm.add_schedule_form.$valid) {
                vm.newSchedule.period_start_date = vm.startDate;
                vm.newSchedule.period_end_date = vm.endDate;
                vm.newSchedule.period_type = Reports.period_type;

                vm.newSchedule.group_bys = ReportsGroupBySelections.selected_group_bys;

                vm.newSchedule.selected_order_by = vm.selectedReportType.selectedOrderBy;
                vm.newSchedule.report_type = vm.selectedReportType.key;

                if (vm.customReportsFilterType === 'brand') {
                    vm.newSchedule.brand_id = vm.customBrandFilter;
                }
                else {
                    vm.newSchedule.brand_id = null;
                }
                if (vm.customReportsFilterType === 'merchant') {
                    vm.newSchedule.selected_merchants = UtilityService.convertArrayObjectsPropertyToArray(vm.merchantSearch.selected_merchants, 'merchant_id');
                }
                else {
                    vm.newSchedule.selected_merchants = null;
                }
                $http.post('/reports/schedule', vm.newSchedule).success(function (response) {
                    $('#add-schedule-modal').modal('hide');
                    vm.newSchedule = {};
                    vm.add_schedule_form.$setPristine();
                    vm.add_schedule_form.$setUntouched();
                    SweetAlert.swal({
                        title: "Success",
                        text: "Your report has been scheduled.",
                        showCancelButton: false,
                        confirmButtonColor: "#488214",
                        type: "success",
                        confirmButtonText: "Ok"
                    });
                }).error(function (response, status) {
                    console.log(response);
                    SweetAlert.swal({
                        title: "Warning",
                        text: "Sorry, Something went wrong please contact support.",
                        type: "warning",
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Ok"
                    });
                });
            }
        }
    }
})();
