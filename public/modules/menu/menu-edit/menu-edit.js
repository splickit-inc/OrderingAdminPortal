angular.module('adminPortal.menu').controller('MenuEditCtrl', MenuEditCtrl);

function MenuEditCtrl(Menu, $timeout, $scope, Users, SweetAlert, UtilityService, MenuItem, $location, $routeParams, Merchant, $localStorage, $http) {
    var vm = this;

    vm.current_mod_item = {};
    vm.modifier_groups = [];

    vm.merchant_menu_types = ['All', 'Pickup', 'Delivery'];
    vm.loading = false;
    vm.filter_text = "";
    vm.filter_positive_items_only = false;
    vm.succesSaveRow = "";
    //Methods
    vm.full_menu = [];
    vm.edit_item = {};
    vm.edit_modifier_item = {};
    vm.menu_merchant_id = false;
    vm.pos_import_url = false;
    vm.user = Users;

    $scope.full_menu = {};

    vm.new = false;
    vm.isOperator = true;
    var unix_time;
    var mod_item_unix_item;
    vm.merchant_select_type = 'menu-merchant-select';

    vm.changeItemActive = changeItemActive;
    vm.editItem = editItem;
    vm.editModItem = editModItem;
    vm.updateItemSubmit = updateItemSubmit;
    vm.updateModifierItemSubmit = updateModifierItemSubmit;
    vm.changeModifierItemActive = changeModifierItemActive;
    vm.filterItems = filterItems;
    vm.filterModItems = filterModItems;
    vm.updateItem = updateItem;
    vm.updateItemPost = updateItemPost;
    vm.toggleStopPropagation = toggleStopPropagation;
    vm.setMenuMerchant = setMenuMerchant;
    vm.posImport = posImport;
    vm.merchantSearchByMenu = merchantSearchByMenu;
    vm.updateModifierItemDelay = updateModifierItemDelay;
    vm.itemAllowedModifiersOpen = itemAllowedModifiersOpen;
    vm.submitAllowedModifierGroups = submitAllowedModifierGroups;
    vm.menuItemFilter = menuItemFilter;
    vm.selectMerchant = selectMerchant;
    vm.copyMenuToMerchant = copyMenuToMerchant;
    vm.defaultPricesConfirm = defaultPricesConfirm;
    vm.returnFirstEightDesc = returnFirstEightDesc;

    load();

    function load() {
        if (!!$routeParams.merchant_id) {
            Merchant.setCurrentMerchant($routeParams.merchant_id).then(function (response) {
                vm.isOperator = false;
                $localStorage.currentMerchantSelected = response;
                Users.operator_merchant = response;
                Menu.get('edit_menu').then(function (response) {
                    vm.full_menu = response.data;
                    $scope.full_menu = vm.full_menu;
                    vm.loading = false;
                    formatFullMenuCorrectly();
                    if (!!response.data.import_url && response.data.import_url) {
                        vm.pos_import_url = response.data.import_url;
                        vm.last_pos_import_date = response.data.menu.last_pos_import_date;
                    }

                });
            });
            return;
        }

        if (!!$routeParams.menu_id) {
            $http.get('/set_menu_id/' + $routeParams.menu_id).success(function (response) {
                Menu.getFullMenu();
                retrieveInformation();
            });
            return;
        }
        retrieveInformation();
    }

    function retrieveInformation() {
        Users.getUserSessionInfo().then(function () {
            if (Users.getVisibility() === 'operator') {
                var operator_menu_id = Users.getOperatorMenu();

                if (operator_menu_id) {
                    vm.loading = true;
                    Menu.get('edit_menu').then(function (response) {
                        vm.full_menu = response.data;
                        $scope.full_menu = vm.full_menu;
                        vm.loading = false;
                        formatFullMenuCorrectly();
                        if (!!response.data.import_url && response.data.import_url) {
                            vm.pos_import_url = response.data.import_url;
                            vm.last_pos_import_date = response.data.menu.last_pos_import_date;
                            console.log(vm.last_pos_import_date);
                        }
                    });
                }
                else {
                    SweetAlert.swal({
                            title: "Warning.",
                            text: "Please go to Settings->Ordering to assign a menu to this location.",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "OK"
                        },
                        function (isConfirm) {
                            if (isConfirm) {
                                $location.path('/merchant/ordering');
                            }
                        });
                }

            }
            else {
                Menu.checkQuickEditSet().then(function (response) {
                    vm.loading = false;
                    vm.isOperator = false;
                    $("#merchant-single-select-modal").modal('show');
                });
            }
        });
    }

    function merchantSearchByMenu(search_text) {
        return Menu.getMerchantByMenu(search_text);
    }

    function changeItemActive(item) {
        updateItemPost(item, 'active');
    }

    function editItem(item) {
        vm.edit_item = item;
    }

    function updateItemSubmit() {
        vm.edit_item.processing = true;
        updateItem(vm.edit_item);
    }

    function setMenuMerchant(merchant) {
        vm.menu_merchant_id = Menu.quick_edit_merchant_id;
        Menu.get('edit_menu_merchant/' + vm.menu_merchant_id).then(function (response) {
            vm.full_menu = response.data.menu;
            Menu.menu_merchant = response.data.merchant;
            $scope.full_menu = vm.full_menu;

            if (response.data.menu.import_url.length > 2) {
                vm.pos_import_url = response.data.menu.import_url;
                vm.last_pos_import_date = response.data.menu.last_pos_import_date;
                console.log(vm.last_pos_import_date);
            }

            vm.loading = false;
            formatFullMenuCorrectly();
            $("#merchant-single-select-modal").modal('hide');
        });
    }

    function updateItemPost(item, property) {
        item.loading = true;
        Menu.post('full_menu_update_item', item).then(function (response) {
            item.loading = false;
            item['success'] = true;

            $timeout(function () {
                item['success'] = false;
            }, 1500);
        }).catch(function (error) {
            item['fail'] = true;
            item.loading = false;
            $timeout(function () {
                item['fail'] = false;
            }, 1500);
        });
    }

    function changeModifierItemActive(mod_item) {
        updateModifierItem(mod_item);
    }

    function updateModifierItemSubmit() {
        vm.edit_modifier_item.processing = true;
        updateModifierItem(vm.edit_modifier_item);
    }

    function editModItem(mod_item) {
        vm.edit_modifier_item = mod_item;
    }

    function updateModItemDelayCheck(item, property, timestamp) {
        $timeout(function () {
            if (timestamp === mod_item_unix_item) {
                updateModifierItem(item, property);
            }
            else {
            }
        }, 500);
    }

    function updateModifierItemDelay(mod_item, property) {
        mod_item_unix_item = (new Date()).getTime();
        updateModItemDelayCheck(mod_item, property, mod_item_unix_item);
    }

    function updateModifierItem(modifier_item, property) {
        modifier_item.loading = true;
        Menu.post('full_menu_update_modifier_item', modifier_item).then(function (response) {
            modifier_item.loading = false;
            modifier_item.success = true;


            $timeout(function () {
                modifier_item.success = false;
            }, 1500);
        }).catch(function (error) {
            modifier_item.fail = true;
            modifier_item.loading = false;
            $timeout(function () {
                modifier_item.fail = false;
            }, 1500);
        });
    }

    function filterItems(item) {
        if (vm.filter_text.length < 3) {
            return false;
        }
        var filter_text_reg_exp = new RegExp(vm.filter_text.toUpperCase(), 'g');

        if (item.item_name.toUpperCase().match(filter_text_reg_exp)) {
            if (vm.filter_positive_items_only) {
                if (item.price <= 0) {
                    return false;
                }
                else {
                    return true;
                }
            }
            else {
                return true;
            }
        }
        else {
            return false;
        }
    }

    function filterModItems(mod_item) {
        if (vm.filter_text.length < 3) {
            return false;
        }
        var filter_text_reg_exp = new RegExp(vm.filter_text.toUpperCase(), 'g');

        if (mod_item.modifier_item_name.toUpperCase().match(filter_text_reg_exp)) {
            return true;
        }
        else {
            return false;
        }
    }


    function updateItemDelay(item, property, timestamp) {
        $timeout(function () {
            if (timestamp === unix_time) {
                updateItemPost(item, property);
            }
            else {
            }
        }, 500);
    }

    function updateItem(item, property) {
        unix_time = (new Date()).getTime();
        updateItemDelay(item, property, unix_time);
    }

    function toggleStopPropagation(event) {
        event.preventDefault();
        event.stopPropagation();
        document.getElementById("table-fix").focus();
    }

    function formatFullMenuCorrectly() {
        for (var key in vm.full_menu.menu_types) {
            for (var item_index = 0; item_index < vm.full_menu.menu_types[key].length; item_index++) {
                vm.full_menu.menu_types[key][item_index]['price'] = parseFloat(vm.full_menu.menu_types[key][item_index]['price']);
            }
        }

        for (var mod_key in vm.full_menu.modifier_groups) {
            for (var mod_item_index = 0; mod_item_index < vm.full_menu.modifier_groups[mod_key].length; mod_item_index++) {
                vm.full_menu.modifier_groups[mod_key][mod_item_index]['modifier_price'] = parseFloat(vm.full_menu.modifier_groups[mod_key][mod_item_index]['modifier_price']);
            }
        }

        for (var all_item_index = 0; all_item_index < vm.full_menu.all_items.length; all_item_index++) {
            vm.full_menu.all_items[all_item_index]['price'] = parseFloat(vm.full_menu.all_items[all_item_index]['price']);
        }

        for (var all_mod_item_index = 0; all_mod_item_index < vm.full_menu.all_modifier_items.length; all_mod_item_index++) {
            vm.full_menu.all_modifier_items[all_mod_item_index]['modifier_price'] = parseFloat(vm.full_menu.all_modifier_items[all_mod_item_index]['modifier_price']);
        }

        vm.full_menu.all_modifier_items = vm.full_menu.all_modifier_items.map(function (item) {
            return UtilityService.parseStringBooleanValues(item);
        });
    }

    function posImport() {
        Menu.post('pos_import', {import_url: vm.pos_import_url}).then(function (response) {
            SweetAlert.swal({
                title: 'Your POS update is in progress and will take several minutes. Check back later to review changes.',
                text: "",
                type: "success",
                showCancelButton: false,
                confirmButtonColor: "#488214",
                confirmButtonText: "Great"
            });
        });
    }

    function submitAllowedModifierGroups() {
        MenuItem.updateItemObjectForAllowedModifierChanges().then(function () {
            MenuItem.data.propagate_type = 'subset';
            MenuItem.data.propagate_merchants = [{merchant_id: Menu.quick_edit_merchant_id}];
            Menu.post('item', MenuItem.data).then(function (response) {

            });
        });

    }

    function itemAllowedModifiersOpen(item) {
        MenuItem.loadMenuItem(item.item_id);
        $("#edit-allowed-modifiers-modal").modal('toggle');
    }

    function menuItemFilter(item) {
        var filter_pass = true;

        if (vm.filter_hide_inactive) {
            if (!item.active) {
                filter_pass = false;
            }
        }

        if (vm.filter_positive_items_only) {
            if (item.price <= 0) {
                filter_pass = false;
            }
        }

        return filter_pass;
    }

    function copyMenuToMerchant() {
        var merchant = Menu.quick_edit_merchant;

        SweetAlert.swal({
                title: "Warning",
                text: "Are you sure you want to apply this menu to merchant  "+merchant.merchant_id+" - "+merchant.address1 + " " + merchant.city + ", " + merchant.state +" "+merchant.zip + " ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
            },
            function (isConfirm) {
                if (isConfirm) {
                    Menu.post('copy_merchant_menu', {destination_merchant_id : Menu.quick_edit_merchant_id}).then(function (response) {

                        SweetAlert.swal({
                            title: 'The menu has been copied to the selected merchant.',
                            text: "",
                            type: "success",
                            showCancelButton: false,
                            confirmButtonColor: "#488214",
                            confirmButtonText: "Great"
                        }, function() {
                            $("#merchant-single-select-modal").modal('toggle');
                        });
                    });
                }
            });
    }

    function selectMerchant(merchant) {
        if (vm.merchant_select_type == 'menu-merchant-select') {
            vm.setMenuMerchant(merchant);
        }
        else if (vm.merchant_select_type == 'menu-copy-select') {
            vm.copyMenuToMerchant(merchant);
        }
    }

    function defaultPricesConfirm() {
        SweetAlert.swal({
                title: "Warning",
                text: "Are you sure? This action will default your prices back to the Master Menu Pricing.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
            },
            function (isConfirm) {
                if (isConfirm) {
                    Menu.post('copy_merchant_menu', {source_merchant_id : 'master'}).then(function (response) {
                        setMenuMerchant('copy');
                        SweetAlert.swal({
                            title: "Default Prices Restored",
                            text: "This merchant's menu has been reverted back to the master, default price list. The menu will now reload.",
                            type: "success",
                            showCancelButton: false,
                            confirmButtonColor: "#488214",
                            confirmButtonText: "Great"
                        });
                    });
                }
            });
    }

    function returnFirstEightDesc(desc) {
        return desc.substring(0, 8);
    }
}
