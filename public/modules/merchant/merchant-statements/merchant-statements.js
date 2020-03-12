angular.module('adminPortal.merchant').controller('MerchantStatementsCtrl', MerchantStatementsCtrl);

function MerchantStatementsCtrl(StatementService) {
    var vm = this;

    vm.paginatedStatements = {};
    vm.fieldNames = {
        invoice: {columnName: 'Invoice Number', class: 'fix-table-wrap'},
        periodByYear: {columnName: 'Date Range'},
        previous_balance: {columnName: 'Previous Balance', class: 'fix-table-wrap'},
        net_proceeds: {columnName: 'Net Proceeds', class: 'fix-table-wrap'},
        balance: {columnName: 'Balance', class: 'fix-table-wrap'}
    };
    vm.searchParams = {};

    vm.currentStatement = {};

    vm.showPrintableStatement = showPrintableStatement;
    vm.printStatementDetails = printStatementDetails;
    vm.exportRecords = exportRecords;


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

    function exportRecords() {
        StatementService.exportStatement(vm.searchParams).then(function (response) {
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
}
