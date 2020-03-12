angular.module('adminPortal.customerService').controller('CustomerServiceTbdCtrl', CustomerServiceTbdCtrl);

function CustomerServiceTbdCtrl(UtilityService, SweetAlert, $http) {
    var vm = this;
    vm.search = {};
    vm.search.processing = false;
    vm.search.text = "";
    vm.searchParams = {
        search_text: '',
        order_by: 'created'
    };
    vm.fieldNames = {
        created: {columnName: 'Date', class: 'fix-table-wrap'},
        location: {columnName: 'Location'},
        phone_number: {columnName: 'Phone Number'},
        amount: {columnName: 'Amount'}
    };

    vm.result_users = {};


    vm.hasSearchResult = hasSearchResult;
    vm.formatPhone = formatPhone;
    vm.search = search;


    function search() {
        if (vm.search_form.$valid) {
            vm.search.initial_search = true;
            var new_params = {
                search_text: vm.search.text
            };
            vm.searchParams = UtilityService.mergeObject(vm.searchParams, new_params);
        }
    }

    function formatPhone(phone_no) {
        return UtilityService.formatPhone(phone_no);
    }

    function hasSearchResult() {
        if (vm.result_users.length > 0 || vm.search.processing) {
            return 'hide-recently-visited-panel';
        }
        return '';
    }
}
