angular.module('adminPortal.merchant').controller('MerchantListCtrl', MerchantListCtrl);

function MerchantListCtrl($scope, $http, UtilityService, $location, Merchant, Users, SweetAlert, $localStorage, $translate) {
    var vm = this;

    vm.search_text = "";

    vm.search_processing = false;
    vm.search_submit = false;

    vm.initial_search = false;
    vm.Merchant = Merchant;
    vm.result_merchants = {};
    vm.processing = false;

    vm.recently_visited_merchants = [];

    vm.order_search_text = '';
    //This is for paginated table with responsive content
    vm.fieldNames = {
        merchant_id: {columnName: 'Merchant ID', class: 'fix-table-wrap'},
        name: {columnName: 'Business Name', class: 'fix-table-wrap'},
        display_name: {columnName: 'Display Name'},
        address1: {columnName: 'Address'},
        phone_no: {columnName: 'Phone Number', class: 'fix-table-wrap'}
    };

    vm.active_only = false;

    vm.searchParams = {};

    var active_letter;

    vm.merchantSearch = merchantSearch;

    vm.merchantLetter = merchantLetter;
    vm.viewMerchant = viewMerchant;
    vm.formatPhone = formatPhone;
    vm.currentLetter = currentLetter;
    vm.createMerchant = createMerchant;
    vm.hasSearchResult = hasSearchResult;
    vm.isUserOperator = isUserOperator;

    load();

    function load() {
        Users.getUserSessionInfo().then(function () {
            if (Users.visibility === 'operator') {
                $location.path("operator/home");
            }
        });

        Merchant.get('load').then(function (response) {
            vm.recently_visited_merchants = response.data.recently_visited;
        });
    }

    $scope.usersService = Users;
    $scope.$watch('usersService.visibility', function (newVal, oldVal, scope) {
        if (vm.isUserOperator()) {
            vm.search_text = '';
            vm.merchantSearch(true);
        }
        merchantSearch(true);
    });

    function merchantSearch(forceSearch) {
        vm.search_submit = true;
        vm.result_merchants = {};
        active_letter = "";

        if ((!!vm.search_form && vm.search_form.$valid) || !!forceSearch) {
            vm.initial_search = true;
            active_letter = '';
            var new_params = {
                search_text: vm.search_text,
                active_only: vm.active_only
            };
            vm.searchParams = UtilityService.mergeObject(vm.searchParams, new_params);
        }
    }

    function merchantLetter(letter) {
        vm.search_text = letter;
        if (active_letter !== letter) {
            active_letter = letter;
            vm.result_merchants = {};
            vm.processing = true;
            vm.initial_search = true;
            vm.searchParams = {
                search_text: letter,
                active_only: vm.active_only
            };
            $http.get('/merchant/first_letter_filter?search_text=' + letter + '&active_only=' + vm.active_only).success(function (response) {
                vm.result_merchants = response;
                vm.processing = false;
            });
        }
    }

    function viewMerchant(merchant) {
        Merchant.setCurrentMerchant(merchant.merchant_id).then(function (response) {
            $localStorage.currentMerchantSelected = response;
            Users.operator_merchant = response;
            $location.path('/merchant/general_info');
        });
    }

    function formatPhone(phone_no) {
        return UtilityService.formatPhone(phone_no);
    }

    //
    function currentLetter(letter) {
        if (letter === active_letter) {
            return "alphabet-active";
        }
        else {
            return "alphabet";
        }
    }

    function createMerchant() {
        $location.path('/merchant/create');
    }

    function hasSearchResult() {
        if (!!vm.result_merchants.data && vm.result_merchants.data.length > 0 || vm.processing) {
            return "hide-recently-visited-panel";
        }
        return "";
    }

    function isUserOperator() {
        return Users.visibility === 'operator';
    }
}
