angular.module('adminPortal.menu').controller('MenuListCtrl',MenuListCtrl);

function MenuListCtrl($scope, $location, Menu, $http, SweetAlert, Brands, Merchant,Users, $localStorage) {
    var vm = this;

    vm.newMenu = {
        merchants: []
    };
    vm.search_result = [];
    vm.edit_menu = {};
    vm.form = {};
    vm.processing = false;
    vm.brands = [];
    vm.search = {};
    vm.search.text = "";
    vm.brandsProcessing = true;
    vm.merchant_search = {};
    vm.brandMerchants = [];
    vm.merchant_processing = false;
    vm.merchantFilter = "";
    vm.invalidForm = false;
    vm.error = "";
    vm.search.results = [];
    vm.can_select_brands = false;
    vm.defaultBrand_id = null;

    vm.order_by = 'menu_id';

    vm.createMenu = createMenu;
    vm.menuSearch = menuSearch;
    vm.viewMenu = viewMenu;
    vm.merchantSearch = merchantSearch;
    vm.loadBrandMerchants = loadBrandMerchants;
    vm.addMerchant = addMerchant;
    vm.removeMerchant = removeMerchant;
    vm.addAllMerchants = addAllMerchants;
    vm.removeAllMerchants = removeAllMerchants;
    vm.filterMerchants = filterMerchants;
    vm.resetForm = resetForm;
    vm.hasSearchResult = hasSearchResult;
    vm.createMenuBtnClickEvent = createMenuBtnClickEvent;
    vm.basicMenuEditOpen = basicMenuEditOpen;
    vm.updateMenu = updateMenu;
    vm.orderByChange = orderByChange;

    load();

    function load() {
        vm.search.processing = true;
        Users.getUserSessionInfo().then(function (current_user) {
            // if(current_user.visibility === "brand")
            // {
            //     vm.search.processing = true;
            //     vm.search.initial_search = true;
            //     Menu.getMenusByBrand(current_user.user_related_data.brand_id).then(function (response) {
            //         vm.search.results = response;
            //         vm.search.processing = false;
            //     });
            // }

            if (!Users.hasPermission('brands_filter')) {
                vm.defaultBrand_id = current_user.user_related_data.brand_id;
                return;
            }
            vm.can_select_brands = true;
        }).catch(function () {
            console.log('unable to get the current user');
        });

        Menu.get('load').then(function (response) {
            vm.brands = response.data.brands;
            vm.recently_visited = response.data.recently_visited;
            vm.brandsProcessing = false;
            vm.search.results = response.data.load_menus;
            vm.search.initial_search = true;
            vm.search.processing = false;

        });
        $('#create-menu-modal').on('hidden.bs.modal', vm.resetForm);
    }

    function loadBrandMerchants(brandId) {
        if(!!vm.defaultBrand_id){
            vm.newMenu.brand_id = vm.defaultBrand_id;
        }
        vm.merchant_processing = true;
        Brands.getBrandMerchants(brandId).then(function (response) {
            vm.brandMerchants = response.data;
            vm.newMenu.merchants = [];
            vm.merchant_processing = false;
        });
    }

    function addMerchant(merchant) {
        vm.newMenu.merchants.push(merchant);
        vm.brandMerchants = vm.brandMerchants.filter(function (el) {
            return el.merchant_id != merchant.merchant_id;
        });
        resortMerchantLists();
    }

    function removeMerchant(merchant) {
        vm.brandMerchants.push(merchant);
        vm.newMenu.merchants = vm.newMenu.merchants.filter(function (el) {
            return el.merchant_id != merchant.merchant_id;
        });
        resortMerchantLists();
    }

    function addAllMerchants() {
        vm.newMenu.merchants = vm.newMenu.merchants.concat(vm.brandMerchants);
        vm.brandMerchants = [];
        resortMerchantLists();
    }

    function removeAllMerchants() {
        vm.brandMerchants = vm.brandMerchants.concat(vm.newMenu.merchants);
        vm.newMenu.merchants = [];
        resortMerchantLists();
    }

    function resortMerchantLists() {
        sortMerchantList(vm.brandMerchants);
        sortMerchantList(vm.newMenu.merchants);
    }

    function sortMerchantList(list) {
        if (!!list && list.length > 1) {
            list.sort(compareMerchants);
        }
    }

    function compareMerchants(m1, m2) {
        var s1 = m1.display_name.toLowerCase();
        var s2 = m2.display_name.toLowerCase();
        return ((s1 == s2) ? 0 : ((s1 > s2) ? 1 : -1));
    }

    function createMenu() {
        vm.newMenu.submit = true;
        if (!vm.form.$valid) {
            return;
        }
        vm.processing = true;
        if(!!vm.defaultBrand_id){
            vm.newMenu.brand_id = vm.defaultBrand_id;
        }
        Menu.post('create', vm.newMenu).then(function (response) {
            vm.processing = false;
            $("#create-menu-modal").modal('toggle');
            SweetAlert.swal({
                    title: "Success.",
                    text: "The new "+vm.newMenu.name+" menu has been initiated, You will now be taken to our full menu creation tool.",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#488214",
                    confirmButtonText: "Great, let's go!"
                },
                function () {
                    $location.path('/menu/items');
                });
        }, function errorCallback(response) {
            vm.processing = false;
            vm.newMenu.error = response.statusText;
        });
    }

    function filterMerchants(merchant) {
        if (vm.merchantFilter.length < 2) {
            return true;
        }
        var regexMerchant = new RegExp(vm.merchantFilter.toUpperCase(), 'g');
        return merchant.display_name.toUpperCase().match(regexMerchant) ||
            merchant.city.toUpperCase().match(regexMerchant) ||
            merchant.zip.toUpperCase().match(regexMerchant) ||
            merchant.merchant_id.toString().toUpperCase().match(regexMerchant);
    }

    function resetForm() {
        if (vm.processing) {
            return;
        }
        $scope.$apply(function () {
            vm.newMenu = {
                merchants: [],
                submit: false
            };
            vm.form["name"].$faded = false;
            vm.form.$setPristine();
            vm.form.$setUntouched();
            vm.form.$rollbackViewValue();
        });
    }

    function menuSearch() {
        vm.search.submit = true;
        vm.search.results = [];

        if (vm.search_form.$valid) {
            vm.search.initial_search = true;
            vm.search.processing = true;
            $http.post('/menu_search', {search_text: vm.search.text, order_by: vm.order_by}).success(function (response) {
                Menu.search_results = response;
                vm.search.results = Menu.getSearchResults();

                vm.search.processing = false;
                vm.search.submit = false;
            }).error(function (response, status) {
                vm.search.processing = false;
                vm.search.submit = false;
                SweetAlert.swal({
                    title: "Warning",
                    text: "Sorry, your search timed out. Please try again.",
                    type: "warning",
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Ok"
                });
            });
        }
    }

    function orderByChange(orderColumn) {
        vm.order_by = orderColumn;
        menuSearch();
    }

    function viewMenu(menu) {
        vm.search.results = [];
        vm.search.processing = true;
        $http.get('/set_menu_id/' + menu.menu_id).success(function (response) {
            $localStorage.currentMenuSelected = menu;
            Menu.current_menu = menu;
            Menu.getFullMenu();
            $location.path('/menu/items');
        });
    }

    function merchantSearch() {
        if (vm.merchant_search_form.$valid) {
            vm.merchant_search.processing = true;
            $http.post('/merchant_search', {search_text: vm.merchant_search.text}).success(function (response) {
                Merchant.search_results = response;
                vm.result_merchants = Merchant.getSearchResults();
                vm.merchant_search.processing = false;
                vm.search_submit = false;
            });
        }
    }

    function hasSearchResult() {
        if(vm.search.processing || vm.search.results.length > 0)
        {
            return "hide-recently-visited-panel"
        }
        return "";
    }

    function createMenuBtnClickEvent() {
        if(!!vm.defaultBrand_id){
            loadBrandMerchants(vm.defaultBrand_id);
        }
        $("#create-menu-modal").modal('toggle');
    }

    function basicMenuEditOpen(menu, event) {
        event.stopPropagation();
        vm.edit_menu = menu;
        $('#edit-menu-modal').modal('show');
    }

    function updateMenu() {
        vm.edit_menu.submit = true;
        Menu.post('basic_update', vm.edit_menu).then(function (response) {
            vm.processing = false;
            $("#edit-menu-modal").modal('toggle');
            SweetAlert.swal({
                    title: "Success.",
                    text: "The menu "+vm.edit_menu.name+" has been updated.",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#488214",
                    confirmButtonText: "OK"
                },
                function () {

                });
        }, function errorCallback(response) {
            vm.processing = false;
            vm.newMenu.error = response.statusText;
        });
    }
}
