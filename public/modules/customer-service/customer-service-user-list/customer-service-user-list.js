angular.module('adminPortal.customerService').controller('CustomerServiceUserListCtrl', CustomerServiceUserListCtrl);

function CustomerServiceUserListCtrl($http, UtilityService, $location, SplickitUser, SweetAlert) {
    var vm = this;

    vm.search = {};
    vm.search.text = "";
    vm.search.initial_search = false;
    vm.result_users = SplickitUser.getSearchResults();

    vm.recently_visited_users = [];

    vm.total_search_result_count = 0;
    vm.users_per_page = 20;
    vm.loading_offset_dataset = false;


    vm.edit_user = {};

    vm.searchParams = {
        search_text: vm.search.text,
        past_seven_days: vm.search.past_seven_days
    };
    vm.fieldNames = {
        user_id: {columnName: 'User ID', class: 'fix-table-wrap'},
        email: {columnName: 'Email', class: 'text-wrap-word-break'},
        first_name: {columnName: 'First Name', class: 'text-wrap-word-break'},
        last_name: {columnName: 'Last Name'},
        balance: {columnName: 'Balance', class: 'text-wrap-word-break'},
        orders: {columnName: 'Orders'},
        contact_no: {columnName: 'Phone'},
        created: {columnName: 'Created'}
    };

    vm.result_users = [];

    vm.searchUsers = searchUsers;
    vm.formatPhone = formatPhone;
    vm.viewUser = viewUser;
    vm.fiftyOpacityOffset = fiftyOpacityOffset;
    vm.hasSearchResult = hasSearchResult;

    load();

    function load() {
        $http.get('/recently_visited_users').success(function (response) {
            vm.recently_visited_users = response;
        });
    }

    function searchUsers() {
        vm.search.submit = true;
        vm.search.result_users = true;

        if (vm.search_form.$valid) {
            vm.search.initial_search = true;
            var new_params = {
                search_text: vm.search.text,
                past_seven_days: vm.search.past_seven_days
            };
            vm.searchParams = UtilityService.mergeObject(vm.searchParams, new_params);
        }
    }

    function formatPhone(phone_no) {
        return UtilityService.formatPhone(phone_no);
    }

    function viewUser(user) {
        $http.post('/customer_service/set_current_user', {user_id: user.user_id}).success(function (response) {
            $location.path('/customer_service/user');
        });
    }

    function fiftyOpacityOffset() {
        if (vm.loading_offset_dataset) {
            return 'fifty-opacity';
        }
        else {
            return '';
        }
    }

    function hasSearchResult() {
        if (vm.result_users.length > 0 || vm.search.processing) {
            return "hide-recently-visited-panel";
        }
        return "";
    }
}
