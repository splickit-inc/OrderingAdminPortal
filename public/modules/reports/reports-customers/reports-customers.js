(function () {
    'use strict';
    angular.module('adminPortal.reports').controller('ReportsCustomersCtrl', ReportsCustomersCtrl);

    function ReportsCustomersCtrl(CustomersReportService) {
        var vm = this;
        vm.processing = false;
        vm.startDate = moment();
        vm.endDate = moment().add(1, 'day');
        vm.totalCustomers = 0;
        vm.page = 1;
        vm.perPage = 25;
        vm.perPageOptions = [10, 25, 50];
        vm.selectedOrderBy = null;
        vm.selectedGroupBy = null;
        vm.orderBy = [{
            title: 'User ID',
            key: 'user_id',
            align: 'right'
        }, {
            title: 'First Name',
            key: 'first_name',
            align: 'left'
        }, {
            title: 'Last Name',
            key: 'last_name',
            align: 'left'
        }, {
            title: 'Email',
            key: 'email',
            align: 'left'
        }, {
            title: '1st Order Date',
            key: 'first_order_date',
            align: 'right'
        }, {
            title: 'Last Order Date',
            key: 'last_order_date',
            align: 'right'
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
            title: 'Order Spent',
            key: 'order_spent',
            align: 'right'
        }, {
            title: 'Total Spent',
            key: 'total_spent',
            align: 'right'
        }, {
            title: 'Average Order Value',
            key: 'avg_order_value',
            align: 'right'
        }];

        vm.groupBy = [{
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

        vm.customers = {};
        vm.customersRequest = {};

        vm.onPeriodChanged = onPeriodChanged;
        vm.onGroupByChanged = onGroupByChanged;
        vm.onPageChanged = onPageChanged;
        vm.sortByColumn = sortByColumn;

        function loadCustomers() {
            if (!!vm.customersRequest) {
                vm.customersRequest.abort();
                vm.processing = false;
            }
            vm.processing = true;
            (vm.customersRequest = CustomersReportService
                    .getCustomers(vm.startDate, vm.endDate, vm.page,
                        !vm.selectedGroupBy ? null : vm.selectedGroupBy.key,
                        vm.selectedOrderBy, vm.perPage)
            ).then(function (response) {
                vm.processing = false;
                var result = response.data;
                vm.customers = result.data;
                vm.totalCustomers = result.total;
            }).catch(function (response) {
                if (response.status != -1) {
                    vm.processing = false;
                }
            });
        }

        function onPeriodChanged(startDate, endDate) {
            vm.startDate = !startDate ? null : startDate.format('YYYY-MM-DD');
            vm.endDate = !endDate ? null : endDate.format('YYYY-MM-DD');
            loadCustomers();
        }

        function onGroupByChanged() {
            addGroupByColumn();
            loadCustomers();
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
            loadCustomers();
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
            loadCustomers();
        }
    }
})();
