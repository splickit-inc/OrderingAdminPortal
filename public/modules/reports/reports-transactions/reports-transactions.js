(function () {
    'use strict';
    angular.module('adminPortal.reports').controller('ReportsTransactionsCtrl', ReportsTransactionsCtrl);

    function ReportsTransactionsCtrl(TransactionsReportService, ReportsGroupBySelections) {
        var vm = this;
        vm.processing = false;
        vm.startDate = moment();
        vm.endDate = moment();
        vm.totalTransactions = 0;
        vm.page = 1;
        vm.perPage = 25;
        vm.perPageOptions = [10, 25, 50];
        vm.selectedOrderBy = null;
        vm.selectedGroupBy = null;
        vm.orderBy = [{
            title: 'Merchant ID',
            key: 'merchant_id',
            align: 'right'
        }, {
            title: 'Store Name',
            key: 'store_name',
            align: 'left'
        }, {
            title: 'Total Orders',
            key: 'total_orders',
            align: 'right'
        }, {
            title: 'Pickup Orders',
            key: 'pickup_orders',
            align: 'right'
        }, {
            title: 'Delivery Orders',
            key: 'delivery_orders',
            align: 'right'
        }, {
            title: 'Order Amount',
            key: 'order_amount',
            align: 'right'
        }, {
            title: 'Tip',
            key: 'tip',
            align: 'right'
        }, {
            title: 'Tax',
            key: 'tax',
            align: 'right'
        }, {
            title: 'Discount',
            key: 'discount',
            align: 'right'
        }, {
            title: 'Delivery Fee',
            key: 'delivery_fee',
            align: 'right'
        }, {
            title: 'Grand Total',
            key: 'grand_total',
            align: 'right'
        }, {
            title: 'Average Order Value',
            key: 'avg_order_value',
            align: 'right'
        }];

        vm.groupByOptions = [{
            desc: 'Date',
            key: 'order_date'
        }, {
            desc: 'Hour of Day',
            key: 'order_hour'
        }, {
            desc: 'Day of Week',
            key: 'order_day_of_week'
        }, {
            desc: 'Month',
            key: 'order_month'
        }, {
            desc: 'Year',
            key: 'order_year'
        }, {
            desc: 'Order Type',
            key: 'order_type'
        }, {
            desc: 'Payment Type',
            key: 'payment_type'
        }];
        vm.transactions = {};
        vm.transactionsRequest = {};
        vm.onPeriodChanged = onPeriodChanged;
        vm.onGroupByChanged = onGroupByChanged;
        vm.onPageChanged = onPageChanged;
        vm.sortByColumn = sortByColumn;
        vm.openGroupBySelections = openGroupBySelections;

        function loadTransactions() {
            if (!!vm.transactionsRequest) {
                vm.transactionsRequest.abort();
                vm.processing = false;
            }
            vm.processing = true;
            (vm.transactionsRequest = TransactionsReportService
                    .getTransactions(vm.startDate, vm.endDate, vm.page,
                        !vm.selectedGroupBy ? null : vm.selectedGroupBy.key,
                        vm.selectedOrderBy, vm.perPage)
            ).then(function (response) {
                vm.processing = false;
                var result = response.data;
                vm.transactions = result.data;
                vm.totalTransactions = result.total;
            }).catch(function (response) {
                if (response.status != -1) {
                    vm.processing = false;
                }
            });
        }

        function onPeriodChanged(startDate, endDate) {
            vm.startDate = !startDate ? null : startDate.format('YYYY-MM-DD');
            vm.endDate = !endDate ? null : endDate.format('YYYY-MM-DD');
            loadTransactions();
        }

        function onGroupByChanged() {
            addGroupByColumn();
            loadTransactions();
        }

        function addGroupByColumn() {
            var lastOrderBy = vm.orderBy[vm.orderBy.length - 1].key;
            var hasGroupByAsColumn = vm.groupBy.some(function (gb) {
                return gb.key === lastOrderBy;
            });
            if (hasGroupByAsColumn) {
                vm.orderBy.pop();
            }
            if (!!vm.selectedOrderBy && vm.selectedOrderBy.indexOf(lastOrderBy) != -1) {
                vm.selectedOrderBy = null;
            }
            vm.orderBy.push({
                title: vm.selectedGroupBy.desc,
                key: vm.selectedGroupBy.key,
                align: 'right'
            });
        }

        function onPageChanged(newPage) {
            vm.page = newPage;
            loadTransactions();
        }

        function sortByColumn(column) {
            var selectedOrderBy = null;
            for (var i = 0; i < vm.orderBy.length; i++) {
                if (vm.orderBy[i].key == column) {
                    if (!vm.orderBy[i].sort) {
                        vm.orderBy[i].sort = '+';
                    }
                    else if (vm.orderBy[i].sort == '+') {
                        vm.orderBy[i].sort = '-';
                    }
                    else if (vm.orderBy[i].sort == '-') {
                        vm.orderBy[i].sort = null;
                    }
                    selectedOrderBy = !vm.orderBy[i].sort ? null : vm.orderBy[i];
                } else {
                    vm.orderBy[i].sort = null;
                }
            }
            if (!selectedOrderBy) {
                vm.selectedOrderBy = null;
            }
            else {
                vm.selectedOrderBy = selectedOrderBy.sort + selectedOrderBy.key;
            }
            loadTransactions();
        }

        function openGroupBySelections() {
            ReportsGroupBySelections.resetSelections(vm.groupByOptions);
            $("#group-by-modal").modal("show");

        }
    }
})();
