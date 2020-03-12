(function () {
    /* globals moment: false */
    /* globals angular: false */
    /* globals console: false */
    /* globals $: false */
    angular.module('adminPortal.reports').component('statementsComponent', {
        templateUrl: 'modules/reports/components/statements-component/statements-component.html',
        controllerAs: 'vm',
        bindings: {
            customBrandFilter: '<',
            customReportsFilterType: '<',
            merchantSearch: '<',
            processing: '=?',
            exporting: '=?'
        },
        controller: statementsReportComponentController
    });

    function statementsReportComponentController(StatementService, Users, cssInjector, UtilityService, $scope) {
        var vm = this;
        cssInjector.add("modules/merchant/merchant-statements/merchant-statements.css");

        vm.getStatements = getStatements;
        vm.showPrintableStatement = showPrintableStatement;
        vm.printStatementDetails = printStatementDetails;
        vm.exportRecords = exportRecords;
        vm.isValidToBeShown = isValidToBeShown;


        vm.paginatedStatements = {};
        vm.fieldNames = {
            invoice: {columnName: 'Invoice Number', class: 'fix-table-wrap'},
            periodByYear: {columnName: 'Date Range'},
            previous_balance: {columnName: 'Previous Balance', class: 'fix-table-wrap'},
            net_proceeds: {columnName: 'Net Proceeds', class: 'fix-table-wrap'},
            balance: {columnName: 'Balance', class: 'fix-table-wrap'}
        };
        vm.searchParams = undefined;
        vm.merchants = [];
        vm.currentStatement = {};
        vm.datePicker = {
            date: {startDate: moment(), endDate: moment()},
            options: {
                pickerClasses: 'custom-picker-colors',
                buttonClasses: 'btn',
                applyButtonClasses: 'btn-success',
                locale: {
                    applyLabel: "Apply",
                    cancelLabel: 'Cancel',
                    customRangeLabel: 'Custom',
                    format: "MMM-DD-YYYY" //will give you 2017-01-06
                    //format: "D-MMM-YY", //will give you 6-Jan-17
                    //format: "D-MMMM-YY", //will give you 6-January-17
                },
                ranges: {
                    'This Month': [moment().startOf('month'), moment()],
                    'Last Month': [moment().startOf('month').subtract(1, 'month').startOf('month'), moment().startOf('month').subtract(1, 'month').endOf('month')],
                    'Current Year': [moment().startOf('year'), moment()],
                    'Last Year': [moment().startOf('year').subtract(1, 'year').startOf('year'), moment().startOf('year').subtract(1, 'year').endOf('year')],
                }
            }
        };

        function showPrintableStatement(statement) {
            Object.assign(vm.currentStatement, statement);
            if (!!vm.currentStatement.weekly) {
                var weekStatements = [];
                var weekly = vm.currentStatement.weekly.split('\n');
                var order = {Sun: 1, Mon: 2, Tue: 3, Wed: 4, Thu: 5, Fri: 6, Sat: 7, Tot: 8};
                weekly.sort(function (a, b) {
                    var dayA = a.substring(0, 3);
                    var dayB = b.substring(0, 3);
                    return order[dayA] - order[dayB];
                });
                weekly.forEach(function (value) {
                    var values = value.split('\t');
                    weekStatements.push(values);
                    if (values.length === 9) {
                        vm.showExtraColumn = true;
                    }
                    else {
                        vm.showExtraColumn = false;
                    }
                });
                vm.currentStatement.weekly = weekStatements;
            }
            $('#detailsModal').modal('show');
        }

        function printStatementDetails() {
            window.print();
        }

        function isValidToBeShown() {
            if (vm.customReportsFilterType === 'brand' && !!!vm.customBrandFilter) {
                return false;
            }
            return !(vm.customReportsFilterType === 'merchant' && (vm.merchantSearch.selected_merchants.length === 0));
        }

        function getStatements() {
            var reportParams = {
                start_date: vm.datePicker.date.startDate.format('YYYY-MM-DD'),
                end_date: vm.datePicker.date.endDate.format('YYYY-MM-DD'),
                filter_type: vm.customReportsFilterType
            };

            if (vm.customReportsFilterType === 'brand') {
                reportParams.brand_id = vm.customBrandFilter;
            }
            else {
                delete reportParams.brand_id;
            }
            if (vm.customReportsFilterType === 'merchant') {
                reportParams.merchant_list = UtilityService.convertArrayObjectsPropertyToArray(vm.merchantSearch.selected_merchants, 'merchant_id');
            }
            else {
                delete reportParams.merchant_list;
            }
            //this is being converted to a new object in order to trigger the new request event.
            vm.searchParams = reportParams;
        }

        function exportRecords() {
            if (vm.customReportsFilterType === 'merchant') {
                vm.searchParams.merchant_list = UtilityService.convertArrayObjectsPropertyToArray(vm.merchantSearch.selected_merchants, 'merchant_id');
                vm.searchParams.merchant_list = UtilityService.convertArrayToGetList(vm.searchParams.merchant_list);
            }

            StatementService.exportReportStatement(vm.searchParams).then(function (response) {
                var headers = response.headers();
                var filename = 'report_' + Date.now() + '.csv';
                var contentType = headers['content-type'];

                var linkElement = document.createElement('a');
                try {
                    var blob = new Blob([response.data], {type: contentType});
                    var url = window.URL.createObjectURL(blob);

                    linkElement.setAttribute('href', url);
                    linkElement.setAttribute("download", filename);

                    var clickEvent = new MouseEvent("click", {
                        "view": window,
                        "bubbles": true,
                        "cancelable": false
                    });
                    linkElement.dispatchEvent(clickEvent);
                } catch (e) {
                    console.log(e);
                }
            }).catch(function (error) {
                console.log(error);
            });
        }

        vm.autoSearch = false;

        function load() {
            Users.getUserSessionInfo().then(function (current_user) {
                if (current_user.visibility === "brand") {
                    vm.autoSearch = true;
                }
            }).catch(function () {
                console.log('unable to get the current user');
            });
        }

        load();

        $scope.$on('$destroy', function () {
            cssInjector.remove("modules/merchant/merchant-statements/merchant-statements.css");
        });
    }
})();
