angular.module('adminPortal.reports').controller('ReportsCtrl', ReportsCtrl);

function ReportsCtrl($scope, $filter, $compile, Reports, CustomersReportService,
                     TransactionsReportService, SweetAlert, Users, $http, EmbeddedMerchantSearch, UtilityService, Brands) {
    var vm = this;

    vm.merchant_search = EmbeddedMerchantSearch;
    vm.filter_type = 'brand';
    vm.custom_reports_filter_type = 'brand';

    vm.brands = [];

    vm.merchant_custom_subset = false;

    vm.user = Users;

    vm.enableMerchantGroups = false;

    vm.propertyName = 'name';
    vm.summary = undefined;
    vm.reverse = false;
    vm.sortBy = function (propertyName) {
        vm.reverse = (vm.propertyName === propertyName) ? !vm.reverse : false;
        vm.propertyName = propertyName;
    };

    //   Chart.defaults.global.defaultFontColor = '#fff';

    vm.brands = [];

    vm.filterTypeClass = filterTypeClass;
    vm.merchantCustomSubsetChange = merchantCustomSubsetChange;

    vm.time_periods = Reports.time_periods;
    vm.current_time_period = 'today';

    vm.reportTypes = [
        {
            desc: 'Sales',
            key: 'transactions'
        },
        {
            desc: 'Menu Item Sales',
            key: 'sales_by_menu_items'
        },
        {
            desc: 'All Orders',
            key: 'all_orders'
        },
        {
            desc: 'Customer Activity',
            key: 'customers'
        },
        {
            desc: 'Statements',
            key: 'statements_report'
        },
        {
            desc: 'Store Settings',
            key: 'store_settings'
        },
        {
            desc: 'Promos',
            key: 'promo'
        }
        // ,
        // {
        //     desc: 'Menu Export',
        //     key: 'menu_export'
        // }
    ];

    vm.orderBy = [];
    vm.selectedReportType = null;

    vm.processing = false;
    vm.exporting = false;
    vm.selectedGroupBy = null;
    vm.dataList = null;
    vm.dataRequest = null;

    vm.onReportTypeChanged = onReportTypeChanged;
    vm.switchToMerchantGroups = switchToMerchantGroups;

    function onReportTypeChanged() {
        vm.selectedGroupBy = null;
    }

    function filterTypeClass(value, current_set_variable) {
        if (current_set_variable === value) {
            return 'btn-success';
        }
        else {
            return 'btn-default';
        }
    }

    function merchantCustomSubsetChange() {
        if (vm.merchant_custom_subset) {
            vm.custom_reports_filter_type = 'merchant';
            EmbeddedMerchantSearch.merchantGroupLoad();
        }
        else {
            vm.custom_reports_filter_type = 'brand';
        }
    }

    function switchToMerchantGroups() {
        vm.enableMerchantGroups = true;

        if (vm.merchant_search.selectable_merchant_groups.length == 0) {
            vm.merchant_search.merchantGroupSearch();
        }
    }

    function load() {

        Users.getUserSessionInfo().then(function (current_user) {
            if (Users.hasPermission('brands_filter')) {
                Brands.getAllBrands().success(function (response) {
                    vm.brands = response;
                });
            }

            if (Users.visibility === "brand") {
                vm.custom_reports_filter_type = 'brand';
                vm.custom_brand_filter = Users.user_related_data.brand_id;
            }

            if (Users.visibility === 'operator') {
                vm.custom_reports_filter_type = 'merchant';
                vm.merchant_search.selected_merchants = [Users.operator_merchant];
            }
        });
    }
    load();
}
