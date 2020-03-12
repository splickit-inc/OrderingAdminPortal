angular.module('adminPortal.promo').controller('PromoListCtrl', PromoListCtrl);

function PromoListCtrl($http, $location, SweetAlert, Promos, Users, UtilityService) {
    var vm = this;

    vm.recent_promos = [];
    vm.search = {};
    vm.search.paginatedResult = {};
    vm.search.paginatedResult.data = [];
    vm.search.text = "";
    vm.order_by = "Promo.modified";
    vm.search.active_only = true;
    vm.search.processing = false;
    vm.new_brand = {};

    vm.user_service = Users;

    vm.promo_type_options = Promos.promo_types.slice(0);
    vm.promo_type_options.push({
        code: -1,
        value: 'All'
    });
    vm.search.promo_type = -1;

    //This is for paginated table with responsive content
    vm.fieldNames = {
        promo_id: {columnName: 'Promo ID', class: 'fix-table-wrap'},
        promo_key_word: {columnName: 'Keyword'},
        start_date: {columnName: 'Start Day', class: 'fix-table-wrap'},
        end_date: {columnName: 'End Day', class: 'fix-table-wrap'},
        description: {columnName: 'Description'},
        promo_type: {columnName: 'Promo Type'},
        active: {columnName: 'Active'}
    };

    vm.createNewPromo = createNewPromo;
    vm.promoSearch = promoSearch;
    vm.viewPromo = viewPromo;
    vm.hasSearchResult = hasSearchResult;

    load();

    function load() {
        $http.get('/promos/recently_visited').success(function (response) {
            vm.recent_promos = response;
        });
        
        if (!Users.hasPermission('brands_filter')) {
            vm.fieldNames.operator_owned = {
                columnName: 'Editable'
            };
        }
    }

    function createNewPromo() {
        $location.path('/promo/create');
    }

    vm.searchParams = {
        search_text: vm.search.text,
        active_only: vm.search.active_only,
        promo_type: vm.search.promo_type
    };

    function promoSearch() {
        vm.search.submit = true;

        if (vm.search_form.$valid) {
            vm.search.initial_search = true;
            vm.search.processing = true;
            var new_params = {
                search_text: vm.search.text,
                active_only: vm.search.active_only,
                promo_type: vm.search.promo_type
            };
            vm.searchParams = UtilityService.mergeObject(vm.searchParams, new_params);
        }
    }

    function viewPromo(promo) {
        if (Users.hasPermission('edit_promo')) {
            $http.get('/set_edit_promo/' + promo.promo_id).success(function (response) {
                $location.path('/promo/edit');
            });
        }
        else {

            if (promo.operator_owned == 'Y') {
                $http.get('/set_edit_promo/' + promo.promo_id).success(function (response) {
                    $location.path('/promo/edit');
                });
            }
            else {
                SweetAlert.swal({
                    title: 'Promo ' + promo.promo_id + ' Description',
                    text: promo.full_description,
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#488214",
                    confirmButtonText: "OK"
                });
            }
        }
    }

    function hasSearchResult() {
        if (!!vm.search.paginatedResult.data && (vm.search.paginatedResult.data.length > 0 || vm.search.processing)) {
            return "hide-recently-visited-panel";
        }
        return "";
    }
}