angular.module('adminPortal.menu').controller('MenuModifierItemCtrl', MenuModifierItemCtrl);

function MenuModifierItemCtrl(Menu, UtilityService, SweetAlert, $location, $scope, EmbeddedMerchantSearch, MerchantGroup, $http) {
    var vm = this;

    vm.current_mod_item = {};
    vm.current_mod_item.default_modifier_price = {};
    vm.current_mod_item.default_modifier_price['price'] = 0;
    vm.current_mod_item.active = true;
    vm.modifier_groups = [];

    vm.show_active_price_only = true;

    $scope.numberTest = 3;

    //Methods
    vm.updateModifierItem = updateModifierItem;
    vm.sizeName = sizeName;
    vm.backToItemsModifiers = backToItemsModifiers;
    vm.setMerchants = setMerchants;
    vm.selectMerchantsToPropagate = selectMerchantsToPropagate;
    vm.filterInactive = filterInactive;
    vm.setPrintNameIfBlank = setPrintNameIfBlank;

    vm.new = false;

    load();

    EmbeddedMerchantSearch.propagate_type = 'subset';
    EmbeddedMerchantSearch.selectable_merchants = [];
    EmbeddedMerchantSearch.selected_merchants = [];

    MerchantGroup.selectable_merchant_groups = [];
    MerchantGroup.selected_merchant_group = null;
    MerchantGroup.search_url = 'merchant_group/search_all';
    Menu.saveOpenSectionsAndModifierGroups();

    function load() {
        if (Menu.current_menu == null) {
            $http.get('/current_menu').success(function (response) {
                Menu.current_menu = response;
                loadModifierItem();
            });

            // Menu.getFullMenu().then(function() {
            //     loadModifierItem();
            // });
        }
        else {
            loadModifierItem();
        }
    }

    function loadModifierItem() {
        Menu.get('current_modifier_item').then(function (response) {
            vm.current_mod_item = response.data;

            Menu.last_edit_object.type = 'modifier_item';
            Menu.last_edit_object.id = vm.current_mod_item.modifier_item_id;

            if (vm.current_mod_item.default_modifier_price['price'] === 0) {
                vm.current_mod_item.default_modifier_price['price'] = '0';
            }
            else {
                vm.current_mod_item.default_modifier_price['price'] = parseFloat(vm.current_mod_item.default_modifier_price['price']);
            }

            for (var size_index = 0; size_index < vm.current_mod_item.all_sizes.length; size_index++) {
                vm.current_mod_item.all_sizes[size_index]['modifier_price'] = parseFloat(vm.current_mod_item.all_sizes[size_index]['modifier_price']);
            }

            vm.modifier_groups = Menu.modifier_groups;

            vm.current_mod_item.submit = false;

            if (typeof vm.current_mod_item.modifier_item_name === "undefined") {
                vm.new = true;
                vm.current_mod_item.default_modifier_price['price'] = 0;
            }

            vm.current_mod_item.priority = parseInt(vm.current_mod_item.priority);
            vm.current_mod_item.modifier_item_max = parseInt(vm.current_mod_item.modifier_item_max);
        });
    }

    function setMerchants() {
        vm.current_mod_item.propagate_type = EmbeddedMerchantSearch.propagate_type;
        vm.current_mod_item.propagate_merchants = EmbeddedMerchantSearch.selected_merchants;

        if (EmbeddedMerchantSearch.propagate_type == 'group') {
            vm.current_mod_item.merchant_group_id = MerchantGroup.selected_merchant_group.id;
        }

        updateModifierItem();
    }

    function updateModifierItem() {
        vm.current_mod_item.submit = true;
        if (vm.modifier_item_form.$valid) {
            vm.current_mod_item.processing = true;

            Menu.post('modifier_item', vm.current_mod_item).then(function (response) {
                vm.current_mod_item.processing = false;
                vm.current_mod_item.submit = false;

                if (Menu.current_mod_item.index == 'new') {
                    var new_modifier_item = response.data;
                    Menu.modifier_groups[Menu.current_mod_group.index]['modifier_items'].push(new_modifier_item);
                    Menu.all_modifier_items.push(new_modifier_item);
                }
                else {

                    if (typeof Menu.modifier_groups[Menu.current_mod_group.index] != 'undefined') {
                        Menu.modifier_groups[Menu.current_mod_group.index]['modifier_items'][Menu.current_mod_item.index] = vm.current_mod_item;

                        var all_mod_items_index = UtilityService.findIndexByKeyValue(Menu.all_modifier_items, 'modifier_item_id', vm.current_mod_item.modifier_item_id);
                        Menu.all_modifier_items[all_mod_items_index] = vm.current_mod_item;
                    }
                }

                SweetAlert.swal({
                        title: "The modifier item " + vm.current_mod_item.modifier_item_name + " has been updated!",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#488214",
                        confirmButtonText: "OK"
                    },
                    function () {
                        $location.path('/menu/items');
                    });
            });
        }
    }

    function sizeName(size) {
        var size_name_length = size.size_name.length;

        if (!isNaN(size.size_name)) {
            return size.size_name;
        }
        else if (typeof size_name_length != "undefined") {
            if (size.size_name.length > 0) {
                return size.size_name;
            }
            else {
                return size.size_print_name;
            }
        }
        else {
            return size.size_print_name;
        }
    }

    function selectMerchantsToPropagate() {
        if (Menu.current_menu.version == '3.00') {
            $('#merchant-select-modal').modal('toggle');
        }
        else {
            updateModifierItem();
        }
    }

    function backToItemsModifiers() {
        $location.path('/menu/items');
    }

    function setPrintNameIfBlank() {
        if (typeof vm.current_mod_item.modifier_item_print_name == 'undefined') {
            vm.current_mod_item.modifier_item_print_name = vm.current_mod_item.modifier_item_name;
            return;
        }
        if (vm.current_mod_item.modifier_item_print_name == '' || vm.current_mod_item.modifier_item_print_name == null) {
            vm.current_mod_item.modifier_item_print_name = vm.current_mod_item.modifier_item_name;
        }
    }

    function filterInactive(size) {
        console.log(size);
        if (!vm.show_active_price_only) {
            return true;
        }
        else {
            if (size.active) {
                return true;
            }
            else {
                return false;
            }
        }
    }
}
