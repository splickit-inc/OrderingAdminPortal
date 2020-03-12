angular.module('adminPortal.menu').controller('MenuOverviewReportCtrl', MenuOverviewReportCtrl);

function MenuOverviewReportCtrl(Menu, $timeout, $scope, Users, $http) {
    var vm = this;

    vm.menu_merchant_id = false;

    vm.menuTypeMenuItemReport = [];
    vm.modifierGroupModifierItemReport = [];
    vm.merchantMenuReport = [];

    vm.exportMenuTypeHeaders = ['Section', 'Section Priority', 'Item', 'Item Description', 'Item Active'];
    vm.exportModGroupHeaders = ['Modifier Group', 'Modifier Group Active', 'Modifier Item', 'Modifier Item Active'];
    vm.exportMerchatHeaders = ['IMGM ID', 'Section ID', 'Section Priority', 'Section Name', 'Item ID', 'Item Priority', 'Item Name', 'Mod Group Priority', 'Mod Group ID', 'Mod Group Name'];

    vm.current_report = 'menu_type_item';

    vm.userService = Users;

    load();

    vm.loadReportWithMerchant = loadReportWithMerchant;
    vm.merchantSearchByMenu = merchantSearchByMenu;
    vm.reportButtonClass = reportButtonClass;
    vm.loadZeroRecordMenu = loadZeroRecordMenu;

    // $timeout(function () {
    //     console.log('trying to show modal');
    //     $("#merchant-single-select-modal").modal('show');
    // }, 1000);

    function load() {
        console.log(Users.hasPermission('multi_merchs_filter'));
        if (Users.hasPermission('multi_merchs_filter')) {
            $("#merchant-single-select-modal").modal('show');
        }
        else {
            loadReportWithMerchant(-1);
        }
    }

    function loadReportWithMerchant() {
        vm.menuTypeMenuItemReport = [];
        vm.modifierGroupModifierItemReport = [];
        vm.merchantMenuReport = [];

        $http.get('menu/export/'+Menu.quick_edit_merchant_id).then(function(response) {
            vm.menuTypeMenuItemReport = response.data.menuTypeMenuItemReport;
            vm.modifierGroupModifierItemReport = response.data.modifierGroupModifierItemReport;
            vm.merchantMenuReport = response.data.merchantMenuReport;
            $("#merchant-single-select-modal").modal('hide');
        });
    }

    function loadZeroRecordMenu() {
        Menu.quick_edit_merchant_id = 0;
        loadReportWithMerchant();
    }

    function merchantSearchByMenu(search_text) {
        return Menu.getMerchantByMenu(search_text);
    }

    function reportButtonClass(report) {
        if (report == vm.current_report) {
            return 'btn btn-success';
        }
        else {
            return "btn btn-primary";
        }
    }

    $timeout(function () {
        load();
    }, 1000);
}
