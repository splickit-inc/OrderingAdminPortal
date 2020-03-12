(function () {
    'use strict';
    angular
        .module('adminPortal.promo')
        .controller('PromoCreateCtrl', PromoCreateCtrl);

    function PromoCreateCtrl(
        $location,
        SweetAlert,
        Promos,
        Menu,
        Merchant,
        $http,
        UtilityService,
        EmbeddedMerchantSearch,
        Users,
        $rootScope) {

        var vm = this;
        vm.initData = initData;

        vm.startDate = moment();
        vm.endDate = moment();

        vm.userService = Users;

        vm.date_options = {};
        vm.search = {};

        EmbeddedMerchantSearch.search_url = 'merchant_search';

        EmbeddedMerchantSearch.selectable_merchants = [];
        EmbeddedMerchantSearch.selected_merchants = 'merchant_search';
        Merchant.all_merchants = [];

        vm.merchant_search_service = EmbeddedMerchantSearch;

        vm.recently_visited_web_skins = [];

        vm.brands = [];

        var key_word_with_merchants = 'A promo keyword you entered is already in use during this time frame.';
        var key_word_without_merchants = 'A promo keyword you entered is already in use during this time frame.';
        vm.key_word_date_error = '';

        vm.discount_types = Promos.discount_types;
        vm.discount_types_4_5 = Promos.discount_types_4_5;
        vm.promo_types = Promos.promo_types;
        vm.order_types = Promos.order_types;

        vm.brand_name = false;

        vm.menu_search = {};
        vm.example_bogo = {};
        vm.can_select_brands = false;
        vm.defaultBrand_id = null;
        vm.bogo_menu_selected = false;
        var i;

        vm.dateFormat = 'MM/DD/YYYY';
        vm.iconOptions = {
            time: 'fa fa-clock-o',
            date: 'fa fa-calendar',
            up: 'fa fa-plus',
            down: 'fa fa-minus',
            next: 'fa fa-chevron-right',
            previous: 'fa fa-chevron-left'
        };

        vm.promo_data = {};
        vm.promo_data.is_valid = false;
        vm.promo_data.order_type = 'all';
        vm.promo_data.key_word = '';
        vm.promo_data.start_date = '';
        vm.promo_data.end_date = '';
        vm.promo_data.max_amt_off = 0;
        vm.merchant_search_service.selected_merchants = [];

        vm.enableMerchantGroups = false;
        vm.validatingMerchantGroups = false;

        vm.create = create;
        vm.loadMenuOptions = loadMenuOptions;
        vm.filterMerchants = filterMerchants;
        vm.menuSearch = menuSearch;
        vm.selectMenu = selectMenu;
        vm.openSelectCategories = openSelectCategories;
        vm.setSelectedCategories = setSelectedCategories;
        vm.openSelectSections = openSelectSections;
        vm.setSelectedSections = setSelectedSections;
        vm.setMerchants = setMerchants;
        vm.onPeriodChanged = onPeriodChanged;

        vm.openSelectSectionSizes = openSelectSectionSizes;
        vm.setSelectSectionSizes = setSelectSectionSizes;

        vm.openSelectSectionItems = openSelectSectionItems;
        vm.setBogoSectionItems = setBogoSectionItems;

        vm.openSectionItemSizes = openSectionItemSizes;
        vm.setSectionItemSizes = setSectionItemSizes;

        vm.resetPromoObject = resetPromoObject;
        vm.discountPromoType = discountPromoType;
        vm.zeroOutUnselectedDiscountTypes = zeroOutUnselectedDiscountTypes;
        vm.validateKeyWords = validateKeyWords;
        vm.validateMerchantsMenu = validateMerchantsMenu;
        vm.showSelectMerchantModal = showSelectMerchantModal;

        load();

        vm.merchant_factory = Merchant;

        vm.menu = Menu;

        initData();
        resetPromoObject();

        function initData() {
            initDatePickers();
        }

        function load() {
            Menu.resetSelections();

            Users.getUserSessionInfo().then(function (current_user) {
                if (!!current_user && !!current_user.user_related_data && !!current_user.user_related_data.brand_id) {
                    vm.promo_data.brand_id = current_user.user_related_data.brand_id;
                }
                if (!Users.hasPermission('brands_filter')) {
                    vm.defaultBrand_id = current_user.user_related_data.brand_id;
                    vm.promo_data.brand_id = vm.defaultBrand_id;
                    loadMenuOptions(vm.defaultBrand_id);
                    return;
                }
                Promos.get('load').then(function (response) {
                    vm.brands = response.data.brands;
                });
                vm.can_select_brands = true;
            }).catch(function () {
                console.log('unable to get the current user');
            });
        }

        function loadMenuOptions() {

            if (vm.promo_data.promo_type == 300) {
                vm.promo_data.percent_off = 100;
            }
            else {
                vm.promo_data.percent_off = 0;
            }

            vm.promo_data.is_valid = false;
            vm.merchant_search_service.selected_merchants = [];
            $rootScope.safeApply();
            vm.brand_name = UtilityService.returnOneArrayFieldWithAnotherArrayFieldValue(vm.brands, 'brand_id', 'brand_name', vm.promo_data.brand_id);

            Promos.get('brand_menus/' + vm.promo_data.brand_id).then(function (response) {
                vm.search.results = response.data;
                Menu.search_results = response.data;
                if (vm.search.results.length === 1) {
                    selectMenu(vm.search.results[0]);
                }
            });
        }

        function resetForm() {
            vm.create_form.$setPristine();

            vm.create_form.brand.$faded = false;
            vm.create_form.keywords.$faded = false;
            vm.create_form.description.$faded = false;
            vm.create_form.cmb_promo_type.$faded = false;
            vm.create_form.order_type.$faded = false;
            vm.create_form.max_use.$faded = false;
            vm.create_form.max_redemptions.$faded = false;
            vm.create_form.qualifying_amt.$faded = false;
            vm.create_form.discount_type.$faded = false;
            vm.create_form.percent_off.$faded = false;
            vm.create_form.fixed_price.$faded = false;
            vm.create_form.fixed_amount_off.$faded = false;
            vm.create_form.max_amount_off.$faded = false;
        }

        function resetPromoObject() {
            vm.promo_data = {};
            vm.promo_data.order_type = 'all';
            vm.promo_data.key_word = '';
            vm.promo_data.start_date = '';
            vm.promo_data.end_date = '';
            vm.merchant_search_service.selected_merchants = [];

            vm.promo_data.merchants = [];
            vm.promo_data.qualifying_amt = 0;
            vm.promo_data.all_merchants = true;
            vm.promo_data.automatic_promo = false;

            vm.promo_data.bogo_menu_selections = {};

            vm.promo_data.bogo_menu_selections.buy_selections = {};
            vm.promo_data.bogo_menu_selections.get_selections = {};

            vm.promo_data.bogo_menu_selections.buy_selections.categories = [];
            vm.promo_data.bogo_menu_selections.buy_selections.sections = [];
            vm.promo_data.bogo_menu_selections.buy_selections.section_sizes = [];
            vm.promo_data.bogo_menu_selections.buy_selections.items = [];
            vm.promo_data.bogo_menu_selections.buy_selections.section_items_sizes = [];

            vm.promo_data.bogo_menu_selections.get_selections.categories = [];
            vm.promo_data.bogo_menu_selections.get_selections.sections = [];
            vm.promo_data.bogo_menu_selections.get_selections.section_sizes = [];
            vm.promo_data.bogo_menu_selections.get_selections.items = [];
            vm.promo_data.bogo_menu_selections.get_selections.section_items_sizes = [];

            vm.promo_data.buy_all_discount = {};

            vm.promo_data.buy_all_discount.categories = [];
            vm.promo_data.buy_all_discount.sections = [];
            vm.promo_data.buy_all_discount.section_sizes = [];
            vm.promo_data.buy_all_discount.items = [];
            vm.promo_data.buy_all_discount.section_items_sizes = [];

            vm.promo_data.buy_any_discount = {};

            vm.promo_data.buy_any_discount.categories = [];
            vm.promo_data.buy_any_discount.sections = [];
            vm.promo_data.buy_any_discount.section_sizes = [];
            vm.promo_data.buy_any_discount.items = [];
            vm.promo_data.buy_any_discount.section_items_sizes = [];


            vm.promo_data.bogo_menu_selections.buy_type = '';
            vm.promo_data.bogo_menu_selections.get_type = '';
            vm.promo_data.is_valid = false;
        }

        function create() {
            vm.promo_data.submit = true;
            if (vm.create_form.$valid && (!vm.promo_data.start_date || !vm.promo_data.start_date || vm.promo_data.start_date.length === 0 || vm.promo_data.end_date.length === 0)) {
                vm.errorMessage = "Please select the date range before submit.";
                $('#error-message').modal('show');
                return;
            }

            checkMenuSelectionValidity();

            if (vm.create_form.$valid) {

                if (!UtilityService.isSet(vm.promo_data.merchants) || vm.promo_data.merchants.length < 1) {
                    if (vm.userService.hasPermission('multi_merchs_filter')) {
                        SweetAlert.swal({
                                title: "The promo will be applied for all the selected brand's merchants.",
                                type: "success",
                                showCancelButton: true,
                                confirmButtonColor: "#488214",
                                confirmButtonText: "Confirm All Merchants"
                            },
                            function (result) {
                                if (result === true) {

                                    Promos.post('create', vm.promo_data).then(function (response) {
                                        vm.promo_data.processing = false;

                                        SweetAlert.swal({
                                                title: "The promo " + vm.promo_data.description + " has been created!",
                                                type: "success",
                                                showCancelButton: false,
                                                confirmButtonColor: "#488214",
                                                confirmButtonText: "OK"
                                            },
                                            function () {
                                                // vm.promo_data = {};
                                                // vm.promo_data.merchants = [];
                                                $location.path('/promos');
                                            });
                                        resetForm();
                                    });
                                }
                            });
                    }
                    else {
                        Promos.post('create', vm.promo_data).then(function (response) {
                            vm.promo_data.processing = false;

                            SweetAlert.swal({
                                    title: "The promo " + vm.promo_data.description + " has been created!",
                                    type: "success",
                                    showCancelButton: false,
                                    confirmButtonColor: "#488214",
                                    confirmButtonText: "OK"
                                },
                                function () {
                                    // vm.promo_data = {};
                                    // vm.promo_data.merchants = [];
                                    $location.path('/promos');
                                });
                            resetForm();
                        });
                    }
                }
                else {
                    Promos.post('create', vm.promo_data).then(function (response) {
                        vm.promo_data.processing = false;

                        SweetAlert.swal({
                                title: "The promo " + vm.promo_data.description + " has been created!",
                                type: "success",
                                showCancelButton: false,
                                confirmButtonColor: "#488214",
                                confirmButtonText: "OK"
                            },
                            function () {
                                // vm.promo_data = {};
                                // vm.promo_data.merchants = [];
                                $location.path('/promos');
                            });
                        resetForm();
                    });
                }
            }
        }

        function onPeriodChanged(startDate, endDate) {

            if (startDate > endDate) {
                vm.create_form.keywords.$setValidity("to_before_from", false);
            }
            else {
                vm.create_form.keywords.$setValidity("to_before_from", true);
            }

            vm.promo_data.start_date = !startDate ? null : startDate.format('YYYY-MM-DD');
            vm.promo_data.end_date = !endDate ? null : endDate.format('YYYY-MM-DD');

            validateKeyWords();
        }


        function filterMerchants(merchant) {
            if (vm.merchant_factory.length < 25) {
                return true;
            }
            else {
                if (vm.merchants_filter.length < 3) {
                    return true;
                }
                var regex_merchant_search = new RegExp(vm.merchants_filter.toUpperCase(), 'g');

                if (merchant.name.toUpperCase().match(regex_merchant_search)) {
                    return true;
                }
                else {
                    return false;
                }
            }
        }

        function menuSearch() {
            vm.search.submit = true;
            vm.search.results = [];

            if (vm.menu_search_form.$valid) {
                vm.search.initial_search = true;
                vm.search.processing = true;
                $http.post('/menu_search', {
                    search_text: vm.search.text,
                    order_by: 'name'
                }).success(function (response) {
                    Menu.search_results = response;
                    vm.search.results = Menu.getSearchResults();

                    vm.search.processing = false;
                    vm.search.submit = false;
                });
            }
        }

        function selectMenu(menu) {
            vm.promo_data.menu_id = menu.menu_id;
            $http.get('/set_promo_menu_id/' + menu.menu_id).success(function (response) {
                if (angular.element("#menu-select-modal").is(':visible')) {
                    $("#menu-select-modal").modal('toggle');
                }

                vm.example_bogo = response;
                vm.bogo_menu_selected = true;

                Menu.current_menu = menu;
                Menu.getFullMenu();
                Menu.getPromoBogoOptions();
            });
            validateMerchantsMenu();
        }

        function openSelectCategories(type) {
            Menu.promo_menu_selection_types.menu_type_select = type;

            if (type === 'bogo-buy') {
                Menu.selected_menu_categories = vm.promo_data.bogo_menu_selections.buy_selections.categories;
            }
            else if (type === 'bogo-get') {
                Menu.selected_menu_categories = vm.promo_data.bogo_menu_selections.get_selections.categories;
            }
            else if (type === 'discount-all') {
                Menu.selected_menu_categories = vm.promo_data.buy_all_discount.categories;
            }
            else if (type === 'discount-any') {
                Menu.selected_menu_categories = vm.promo_data.buy_any_discount.categories;
            }

            //Add All Category Types Later, Now Smaw can only except Entre
            //var all_categories = Menu.menu_type_categories;
            var all_categories = [{
                type_id_name: 'Entrée',
                type_id_value: 'Entre',
            }];


            Menu.unselected_menu_categories = UtilityService.removeObjectsFromArrayOfObjects(Menu.selected_menu_categories, all_categories);

            $("#menu-select-categories").modal('toggle');
        }

        function setSelectedCategories() {
            var selected_menu_categories = Menu.selected_menu_categories;
            var categories = [];
            for (i = 0; i < selected_menu_categories.length; i++) {
                categories.push('"' + selected_menu_categories[i]['type_id_name'] + '"')
            }

            if (Menu.promo_menu_selection_types.menu_type_select === 'bogo-buy') {
                vm.promo_data.bogo_menu_selections.buy_selections.categories = selected_menu_categories;
                vm.promo_data.buy_categories = selected_menu_categories;
                vm.promo_data.bogo_menu_selections.selected_buy_category_list = UtilityService.convertArrayToGrammarList(categories);
            }
            else if (Menu.promo_menu_selection_types.menu_type_select === 'bogo-get') {
                vm.promo_data.bogo_menu_selections.get_selections.categories = selected_menu_categories;
                vm.promo_data.get_categories = selected_menu_categories;
                vm.promo_data.bogo_menu_selections.selected_get_category_list = UtilityService.convertArrayToGrammarList(categories);
            }
            else if (Menu.promo_menu_selection_types.menu_type_select === 'discount-all') {
                vm.promo_data.buy_all_discount.categories = selected_menu_categories;
                vm.promo_data.buy_all_discount_category_list = UtilityService.convertArrayToGrammarList(categories);
            }
            else if (Menu.promo_menu_selection_types.menu_type_select === 'discount-any') {
                vm.promo_data.buy_any_discount.categories = selected_menu_categories;
                vm.promo_data.buy_any_discount_category_list = UtilityService.convertArrayToGrammarList(categories);
            }
            $("#menu-select-categories").modal('toggle');
        }

        function openSelectSections(type) {
            Menu.promo_menu_selection_types.menu_type_select = type;

            if (type === 'bogo-buy') {
                Menu.selected_menu_types = vm.promo_data.bogo_menu_selections.buy_selections.sections;
            }
            else if (type === 'bogo-get') {
                Menu.selected_menu_types = vm.promo_data.bogo_menu_selections.get_selections.sections;
            }
            else if (type === 'discount-all') {
                Menu.selected_menu_types = vm.promo_data.buy_all_discount.sections;
            }
            else if (type === 'discount-any') {
                Menu.selected_menu_types = vm.promo_data.buy_any_discount.sections;
            }
            var all_menu_types = Menu.menu_types;
            Menu.unselected_menu_types = UtilityService.removeObjectsFromArrayOfObjects(Menu.selected_menu_types, all_menu_types);
            $("#menu-select-bogo-sections").modal('toggle');
        }

        function setSelectedSections() {
            var selected_menu_types = Menu.selected_menu_types;
            var sections = [];
            for (i = 0; i < selected_menu_types.length; i++) {
                sections.push('"' + selected_menu_types[i]['menu_type_name'] + '"')
            }

            if (Menu.promo_menu_selection_types.menu_type_select === 'bogo-buy') {
                vm.promo_data.bogo_menu_selections.buy_selections.sections = selected_menu_types;
                vm.promo_data.buy_menu_types = selected_menu_types;
                vm.promo_data.bogo_menu_selections.selected_buy_sections_list = UtilityService.convertArrayToGrammarList(sections);
            }
            else if (Menu.promo_menu_selection_types.menu_type_select === 'bogo-get') {
                vm.promo_data.bogo_menu_selections.get_selections.sections = selected_menu_types;
                vm.promo_data.get_menu_types = selected_menu_types;
                vm.promo_data.bogo_menu_selections.selected_get_sections_list = UtilityService.convertArrayToGrammarList(sections);
            }
            else if (Menu.promo_menu_selection_types.menu_type_select === 'discount-all') {
                vm.promo_data.buy_all_discount.sections = selected_menu_types;
                vm.promo_data.buy_all_discount_sections_list = UtilityService.convertArrayToGrammarList(sections);
            }
            else if (Menu.promo_menu_selection_types.menu_type_select === 'discount-any') {
                vm.promo_data.buy_any_discount.sections = selected_menu_types;
                vm.promo_data.buy_any_discount_sections_list = UtilityService.convertArrayToGrammarList(sections);
            }
            $("#menu-select-bogo-sections").modal('toggle');
        }

        function openSelectSectionSizes(type) {
            vm.promo_data.bogo_menu_selections.type = type;
            Menu.promo_menu_selection_types.menu_type_section_size_select = type;

            if (type === 'bogo-buy') {
                Menu.selected_menu_type_sizes = vm.promo_data.bogo_menu_selections.buy_selections.section_sizes;
            }
            else if (type === 'bogo-get') {
                Menu.selected_menu_type_sizes = vm.promo_data.bogo_menu_selections.get_selections.section_sizes;
            }
            else if (type === 'discount-all') {
                Menu.selected_menu_types = vm.promo_data.buy_all_discount.section_sizes;
            }
            else if (type === 'discount-any') {
                Menu.selected_menu_types = vm.promo_data.buy_any_discount.section_sizes;
            }
            var all_menu_type_sizes = Menu.menu_type_sizes;
            Menu.unselected_menu_type_sizes = UtilityService.removeObjectsFromArrayOfObjects(Menu.selected_menu_type_sizes, all_menu_type_sizes);
            $("#menu-select-bogo-section-sizes").modal('toggle');
        }

        function setSelectSectionSizes() {
            var selected_menu_type_sizes = Menu.selected_menu_type_sizes;
            var section_sizes = [];
            for (i = 0; i < selected_menu_type_sizes.length; i++) {
                section_sizes.push('"' + selected_menu_type_sizes[i]['menu_type_name'] + ' - ' + selected_menu_type_sizes[i]['size_name'] + '"')
            }

            if (Menu.promo_menu_selection_types.menu_type_section_size_select === 'bogo-buy') {
                vm.promo_data.bogo_menu_selections.buy_selections.section_sizes = selected_menu_type_sizes;
                vm.promo_data.buy_section_sizes = selected_menu_type_sizes;
                vm.promo_data.bogo_menu_selections.selected_buy_section_sizes_list = UtilityService.convertArrayToGrammarList(section_sizes);
            }
            else if (Menu.promo_menu_selection_types.menu_type_section_size_select === 'bogo-get') {
                vm.promo_data.bogo_menu_selections.get_selections.section_sizes = selected_menu_type_sizes;
                vm.promo_data.get_section_sizes = selected_menu_type_sizes;
                vm.promo_data.bogo_menu_selections.selected_get_section_sizes_list = UtilityService.convertArrayToGrammarList(section_sizes);
            }
            else if (Menu.promo_menu_selection_types.menu_type_section_size_select === 'discount-all') {
                vm.promo_data.buy_all_discount.section_sizes = selected_menu_type_sizes;
                vm.promo_data.buy_all_discount.section_sizes_list = UtilityService.convertArrayToGrammarList(section_sizes);
            }
            else if (Menu.promo_menu_selection_types.menu_type_section_size_select === 'discount-any') {
                vm.promo_data.buy_any_discount.section_sizes = selected_menu_type_sizes;
                vm.promo_data.buy_any_discount.section_sizes_list = UtilityService.convertArrayToGrammarList(section_sizes);
            }
            $("#menu-select-bogo-section-sizes").modal('toggle');
        }

        function openSelectSectionItems(type) {
            vm.promo_data.bogo_menu_selections.type = type;

            if (type === 'bogo-buy') {
                Menu.selected_menu_type_items = vm.promo_data.bogo_menu_selections.buy_selections.items;
            }
            else if (type === 'bogo-get') {
                Menu.selected_menu_type_items = vm.promo_data.bogo_menu_selections.get_selections.items;
            }
            else if (type === 'discount-all') {
                Menu.selected_menu_type_items = vm.promo_data.buy_all_discount.items;
            }
            else if (type === 'discount-any') {
                Menu.selected_menu_type_items = vm.promo_data.buy_any_discount.items;
            }
            var all_menu_type_items = Menu.menu_type_items;
            Menu.unselected_menu_type_items = UtilityService.removeObjectsFromArrayOfObjects(Menu.selected_menu_type_items, all_menu_type_items);
            $("#menu-select-bogo-section-items").modal('toggle');
        }

        function setBogoSectionItems() {

            var selected_menu_type_items = Menu.selected_menu_type_items;
            var section_items = [];
            for (i = 0; i < selected_menu_type_items.length; i++) {
                section_items.push('"' + selected_menu_type_items[i]['menu_type_name'] + ' - ' + selected_menu_type_items[i]['item_name'] + '"')
            }

            if (vm.promo_data.bogo_menu_selections.type === 'bogo-buy') {
                vm.promo_data.bogo_menu_selections.buy_selections.items = selected_menu_type_items;
                vm.promo_data.buy_section_items = selected_menu_type_items;
                vm.promo_data.bogo_menu_selections.selected_buy_section_items_list = UtilityService.convertArrayToGrammarList(section_items);
            }
            else if (vm.promo_data.bogo_menu_selections.type === 'bogo-get') {
                vm.promo_data.bogo_menu_selections.get_selections.items = selected_menu_type_items;
                vm.promo_data.get_section_items = selected_menu_type_items;
                vm.promo_data.bogo_menu_selections.selected_get_section_items_list = UtilityService.convertArrayToGrammarList(section_items);
            }
            else if (vm.promo_data.bogo_menu_selections.type === 'discount-all') {
                vm.promo_data.buy_any_discount.items = selected_menu_type_items;
                vm.promo_data.buy_all_discount_section_items_list = UtilityService.convertArrayToGrammarList(section_items);
            }
            else if (vm.promo_data.bogo_menu_selections.type === 'discount-any') {
                vm.promo_data.buy_any_discount.items = selected_menu_type_items;
                vm.promo_data.buy_any_discount_section_items_list = UtilityService.convertArrayToGrammarList(section_items);
            }
            $("#menu-select-bogo-section-items").modal('toggle');
        }

        function openSectionItemSizes(type) {
            vm.promo_data.bogo_menu_selections.type = type;

            if (type === 'bogo-buy') {
                Menu.selected_menu_type_items_sizes = vm.promo_data.bogo_menu_selections.buy_selections.section_items_sizes;
            }
            else if (type === 'bogo-get') {
                Menu.selected_menu_type_items_sizes = vm.promo_data.bogo_menu_selections.get_selections.section_items_sizes;
            }
            else if (type === 'discount-all') {
                Menu.selected_menu_type_items_sizes = vm.promo_data.buy_all_discount.section_items_sizes;
            }
            else if (type === 'discount-any') {
                Menu.selected_menu_type_items_sizes = vm.promo_data.buy_any_discount.section_items_sizes;
            }
            var all_menu_type_items_sizes = Menu.menu_type_items_sizes;
            Menu.unselected_menu_type_items_sizes = UtilityService.removeObjectsFromArrayOfObjects(Menu.selected_menu_type_items_sizes, all_menu_type_items_sizes);

            $("#menu-select-bogo-section-items-sizes").modal('toggle');
        }

        function setSectionItemSizes() {
            var selected_menu_type_item_sizes = Menu.selected_menu_type_items_sizes;
            var section_items_sizes = [];

            for (i = 0; i < selected_menu_type_item_sizes.length; i++) {
                section_items_sizes.push('"' + selected_menu_type_item_sizes[i]['menu_type_name'] + ' - ' + selected_menu_type_item_sizes[i]['item_name'] + ' - ' + selected_menu_type_item_sizes[i]['size_name'] + '"');
            }

            if (vm.promo_data.bogo_menu_selections.type === 'bogo-buy') {
                vm.promo_data.bogo_menu_selections.buy_selections.section_items_sizes = selected_menu_type_item_sizes;
                vm.promo_data.buy_item_sizes = selected_menu_type_item_sizes;
                vm.promo_data.bogo_menu_selections.selected_buy_section_items_sizes_list = UtilityService.convertArrayToGrammarList(section_items_sizes);
            }
            else if (vm.promo_data.bogo_menu_selections.type === 'bogo-get') {
                vm.promo_data.bogo_menu_selections.get_selections.section_items_sizes = selected_menu_type_item_sizes;
                vm.promo_data.get_item_sizes = selected_menu_type_item_sizes;
                vm.promo_data.bogo_menu_selections.selected_get_section_items_sizes_list = UtilityService.convertArrayToGrammarList(section_items_sizes);
            }
            else if (vm.promo_data.bogo_menu_selections.type === 'discount-all') {
                vm.promo_data.buy_all_discount.section_items_size = selected_menu_type_item_sizes;
                vm.promo_data.buy_all_discount_section_items_sizes_list = UtilityService.convertArrayToGrammarList(section_items_sizes);
            }
            else if (vm.promo_data.bogo_menu_selections.type === 'discount-any') {
                vm.promo_data.buy_any_discount.section_items_size = selected_menu_type_item_sizes;
                vm.promo_data.buy_any_discount_section_items_sizes_list = UtilityService.convertArrayToGrammarList(section_items_sizes);
            }
            $("#menu-select-bogo-section-items-sizes").modal('toggle');
        }

        function initDatePickers() {
            $(function () {
                $('#from_date').datetimepicker({
                    format: vm.dateFormat,
                    icons: vm.iconOptions
                });
                $('#to_date').datetimepicker({
                    format: vm.dateFormat,
                    icons: vm.iconOptions,
                    useCurrent: false //Important! See issue #1075
                });
                $("#from_date").on("dp.change", function (e) {
                    $('#to_date').data("DateTimePicker").minDate(e.date);
                    vm.startDate = e.date;
                });
                $("#to_date").on("dp.change", function (e) {
                    $('#from_date').data("DateTimePicker").maxDate(e.date);
                    vm.endDate = e.date;
                });
            });
        }

        function setMerchants() {
            if (vm.enableMerchantGroups === true) {
                vm.merchant_search_service.selected_merchants = [];
                vm.merchant_search_service.selected_merchant_groups.forEach(function (group) {
                    vm.validatingMerchantGroups = true;
                    vm.promo_data.is_valid = false;
                    vm.merchant_search_service.getMerchantsByGroupID(group.id).success(function (response) {
                        vm.merchant_search_service.selected_merchants = vm.merchant_search_service.selected_merchants.concat(response.merchants);
                        vm.promo_data.merchants = vm.promo_data.merchants.concat(UtilityService.convertArrayObjectsPropertyToArray(response.merchants, 'merchant_id'));
                        validateMerchantsMenu();
                        validateKeyWords();
                        vm.validatingMerchantGroups = false;
                        vm.promo_data.is_valid = true;
                        $rootScope.safeApply();
                    });
                });
                $('#merchant-select-modal').modal('hide');
                return;
            }

            vm.promo_data.merchants = UtilityService.convertArrayObjectsPropertyToArray(EmbeddedMerchantSearch.selected_merchants, 'merchant_id');
            validateMerchantsMenu();
            validateKeyWords();
            vm.promo_data.is_valid = true;
            $('#merchant-select-modal').modal('hide');

        }

        function discountPromoType() {
            if (vm.promo_data.promo_type === 1 || vm.promo_data.promo_type === 4 || vm.promo_data.promo_type === 5) {
                return true;
            }
            else {
                return false;
            }
        }

        function zeroOutUnselectedDiscountTypes() {
            for (i = 0; i < vm.discount_types_4_5.length; i++) {
                if (vm.promo_data.discount_type !== vm.discount_types_4_5[i]['code']) {
                    vm.promo_data[vm.discount_types_4_5[i]['code']] = 0;
                }
            }
        }

        function validateKeyWords() {
            if (UtilityService.isSet(vm.promo_data.key_word) && UtilityService.isSet(vm.promo_data.start_date) && UtilityService.isSet(vm.promo_data.end_date)) {

                var data = {
                    key_words: vm.promo_data.key_word,
                    brand_id: vm.promo_data.brand_id,
                    merchants: vm.promo_data.merchants,
                    start_date: vm.promo_data.start_date,
                    end_date: vm.promo_data.end_date
                };

                vm.create_form.keywords.$setValidity("key_word_new", true);
                vm.create_form.keywords.$setValidity("key_word_new_merchant", true);

                $http.post('/promo/validate_keyword', data).success(function (response) {
                    if (response.valid_key_words) {
                        vm.create_form.keywords.$setValidity("key_word_new", true);
                    }
                    else {
                        vm.promo_data.is_valid = false;
                        vm.create_form.keywords.$setValidity("key_word_new", false);

                        if (vm.merchant_search_service.selected_merchants.length > 0) {
                            vm.key_word_date_error = key_word_with_merchants;
                        }
                        else {
                            vm.key_word_date_error = key_word_without_merchants;
                        }
                    }
                });
            }
        }

        function validateMerchantsMenu() {
            if ((vm.promo_data.promo_type === 2 || vm.promo_data.promo_type === 4 || vm.promo_data.promo_type === 5) && vm.promo_data.merchants.length > 0 && Menu.current_menu != null) {
                vm.create_form.keywords.$setValidity("merchants_menu_valid", true);

                var data = {
                    merchants: vm.promo_data.merchants,
                    menu: Menu.current_menu.menu_id
                };

                $http.post('/promo/validate_merchants_menu', data).success(function (response) {
                    if (response.valid_merchants_menu) {
                        vm.create_form.keywords.$setValidity("merchants_menu_valid", true);
                    }
                    else {
                        vm.promo_data.is_valid = false;
                        vm.create_form.keywords.$setValidity("merchants_menu_valid", false);
                    }
                });
            }
        }

        function showSelectMerchantModal() {
            if (!vm.promo_data.automatic_promo && !!vm.promo_data.key_word && vm.promo_data.key_word.length > 0 && (!vm.promo_data.start_date || !vm.promo_data.end_date)) {
                vm.errorMessage = "Please select the date range before select merchants for promos.";
                $('#error-message').modal('show');
                return;
            }

            if (!!vm.promo_data.brand_id && (vm.promo_data.key_word || vm.promo_data.automatic_promo) && vm.promo_data.description && vm.promo_data.promo_type) {
                vm.promo_data.is_valid = false;
                vm.merchant_search_service.merchantSearchByBrand(vm.promo_data.brand_id);
                vm.merchant_search_service.merchantGroupLoad();
                $('#merchant-select-modal').modal('show');
                $('#merchant-select-modal').on('hidden.bs.modal', function () {
                    if (vm.promo_data.is_valid === false) {
                        vm.merchant_search_service.selected_merchants = [];
                        $rootScope.safeApply();
                    }
                });
            }
            else {
                vm.errorMessage = "Please fill all the required fields above before select merchants for promos.";
                $('#error-message').modal('show');
            }
        }

        function checkMenuSelectionValidity() {
            if (vm.promo_data.promo_type === 2) {
                var buy_valid = UtilityService.checkAtleastOneArrayInObjectLengthGreaterThanZero(vm.promo_data.bogo_menu_selections.buy_selections);
                vm.create_form.keywords.$setValidity("buy_object_selected", buy_valid);

                var get_valid = UtilityService.checkAtleastOneArrayInObjectLengthGreaterThanZero(vm.promo_data.bogo_menu_selections.get_selections);
                vm.create_form.keywords.$setValidity("get_object_selected", get_valid);
            }
            else if (vm.promo_data.promo_type === 4) {
                var buy_any_valid = UtilityService.checkAtleastOneArrayInObjectLengthGreaterThanZero(vm.promo_data.buy_any_discount);
                vm.create_form.keywords.$setValidity("buy_any_object_selected", buy_any_valid);
            }
            else if (vm.promo_data.promo_type === 5) {
                var buy_all_valid = UtilityService.checkAtleastOneArrayInObjectLengthGreaterThanZero(vm.promo_data.buy_all_discount);
                vm.create_form.keywords.$setValidity("buy_all_object_selected", buy_all_valid);
            }
        }

        vm.propertyName = 'name';
        vm.reverse = false;
        vm.sortBy = function (propertyName) {
            vm.reverse = (vm.propertyName === propertyName) ? !vm.reverse : false;
            vm.propertyName = propertyName;
        };
    }
})();