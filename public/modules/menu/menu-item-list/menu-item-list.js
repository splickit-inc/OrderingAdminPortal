angular.module('adminPortal.menu').controller('MenuItemListCtrl', MenuItemListCtrl);

function MenuItemListCtrl(Menu, $timeout, $location, SweetAlert, $http, $anchorScroll, MenuSectionFactory, UtilityService) {
    var vm = this;

    vm.all_menu_items = [];
    vm.all_mod_items = [];

    vm.new_section = {};
    vm.new_section.active = true;
    vm.new_section.active_all_day = true;
    vm.new_section.start_time = '12:00';
    vm.new_section.start_time_am_pm = 'am';
    vm.new_section.end_time = '11:59';
    vm.new_section.end_time_am_pm = 'pm';

    vm.filter_hide_inactive = false;

    vm.edit_section = {};

    vm.new_item = {
        item_id: 'new'
    };

    vm.new_item.menu_type = {};

    vm.new_modifier_item = {
        modifier_item_id: 'new'
    };

    vm.new_modifier_group = {};
    vm.new_modifier_group.active = true;
    vm.new_modifier_group.modifier_type = 'T';
    vm.edit_modifier_group = {};

    vm.sections_priority_menu = {};
    vm.sections_priority_processing = false;

    vm.mod_groups_priority_menu = {};
    vm.mod_groups_priority_processing = false;

    vm.item_priority_section = {};
    vm.item_priority_processing = false;
    var item_priority_section_index;

    vm.modifier_item_priority_group = {};
    vm.modifier_item_priority_processing = false;
    var modifier_item_priority_section_index;

    vm.modifier_types = [
        {
            type_id_value: "T",
            type_id_name: "Top"
        },
        {
            type_id_value: "S",
            type_id_name: "Side"
        },
        {
            type_id_value: "I",
            type_id_name: "Interdependent"
        },
        {
            type_id_value: "I1",
            type_id_name: "Interdependent-1"
        },
        {
            type_id_value: "I2",
            type_id_name: "Interdependent-2"
        },
        {
            type_id_value: "I3",
            type_id_name: "Interdependent-3"
        },
        {
            type_id_value: "I3C",
            type_id_name: "Interdependent-3c"
        }
    ];

    //Methods
    vm.createSection = createSection;
    vm.openEditSection = openEditSection;
    vm.updateSection = updateSection;
    vm.createModifierGroup = createModifierGroup;
    vm.viewItem = viewItem;
    vm.viewModifierItem = viewModifierItem;
    vm.filterItems = filterItems;
    vm.filterModItems = filterModItems;
    vm.openEditModifierGroup = openEditModifierGroup;
    vm.updateModifierGroup = updateModifierGroup;
    vm.sortNumber = sortNumber;
    vm.openSectionsPriority = openSectionsPriority;
    vm.reOrderSections = reOrderSections;
    vm.openModGroupsPriority = openModGroupsPriority;
    vm.reOrderModGroups = reOrderModGroups;
    vm.openItemPriority = openItemPriority;
    vm.reOrderItems = reOrderItems;
    vm.openModifierItemPriority = openModifierItemPriority;
    vm.reOrderModifierItems = reOrderModifierItems;
    vm.firstFiveCharacters = firstFiveCharacters;
    // vm.openDeleteSectionSize = openDeleteSectionSize;
    // vm.deleteSectionSize = deleteSectionSize;
    vm.deleteSectionSizeCancel = deleteSectionSizeCancel;
    vm.addEditSectionSize = addEditSectionSize;
    vm.deleteNewSectionSize = deleteNewSectionSize;
    vm.deleteItemDialog = deleteItemDialog;
    vm.deleteMenuTypeDialog = deleteMenuTypeDialog;
    vm.deleteModifierItemDialog = deleteModifierItemDialog;
    vm.deleteModifierGroupDialog = deleteModifierGroupDialog;
    vm.setNewSectionActiveAllDay = setNewSectionActiveAllDay;
    vm.setEditSectionActiveAllDay = setEditSectionActiveAllDay;
    vm.menuTypeFilter = menuTypeFilter;
    vm.modifierGroupFilter = modifierGroupFilter;
    vm.itemsFilter = itemsFilter;
    vm.setNewItemModGroupConfig = setNewItemModGroupConfig;
    vm.updateSizePrintName = updateSizePrintName;

    vm.menu = Menu;
    vm.menu.search_text = '';

    Menu.getFullMenu().then(function() {
        $timeout(function() {
            if (typeof Menu.last_edit_object.type != 'undefined') {
                if (Menu.last_edit_object.type == 'menu_item') {
                    $anchorScroll.yOffset = 120;
                    $anchorScroll('item-anchor-'+Menu.last_edit_object.id);
                }
                else {
                    $anchorScroll.yOffset = 120;
                    $anchorScroll('mod-item-anchor-'+Menu.last_edit_object.id);
                }
            }
        }, 200);
    });



    // Menu.getFullMenu().then(function(response) {
    //
    //
    //     vm.categories = response.data.lookup.cat_id;
    //
    //     Menu.menu_types = vm.full_menu.menu.menu_types;
    //     Menu.modifier_groups = vm.full_menu.menu.modifier_groups;
    //
    //     vm.all_menu_items = response.data.all_menu_items;
    //     vm.all_mod_items = response.data.all_mod_items;
    //
    //     $timeout(function() {
    //         if (Menu.current_item_id) {
    //             $anchorScroll.yOffset = 120;
    //             $anchorScroll('item-anchor-'+Menu.current_item_id);
    //         }
    //         else if (Menu.current_mod_item_id) {
    //             $anchorScroll.yOffset = 120;
    //             $anchorScroll('mod-item-anchor-'+Menu.current_mod_item_id);
    //         }
    //     }, 500);
    // });

    function resetForm() {
        vm.new_section = {};
        vm.new_section.active = true;
        vm.new_section_form.$setPristine();

        vm.edit_section_form.$setPristine();

        vm.new_modifier_group = {};
        vm.new_modifier_group.active = true;
        vm.new_modifier_group.modifier_type = 'T';
        vm.new_mod_group_form.$setPristine();

        vm.new_section_form.menu_type_name.$faded = false;
        vm.new_section_form.create_cat_id.$faded = false;
        vm.new_section_form.sizes.$faded = false;

        vm.new_mod_group_form.modifier_group_name.$faded = false;
        vm.new_mod_group_form.create_modifier_type.$faded = false;
        vm.new_mod_group_form.priority.$faded = false;
        vm.new_mod_group_form.new_mod_items.$faded = false;
        vm.new_mod_group_form.default_item_price.$faded = false;
        vm.new_mod_group_form.default_item_max.$faded = false;
    }


    function openEditSection(index, section, event) {
        MenuSectionFactory.setSection(section);
        $location.path('/menu/menu_section');

        // section.priority = parseInt(section.priority);
        // vm.edit_section = section;
        //
        // vm.edit_section.index = index;
        // event.stopPropagation();
        // $("#edit-section-modal").modal('toggle');
    }

    // function openDeleteSectionSize(index, size) {
    //     vm.edit_section.delete_size_index = index;
    //     vm.edit_section.delete_size = size;
    // }

    function deleteNewSectionSize(index) {
        vm.edit_section.sizes.splice(index, 1);
    }

    function deleteSectionSizeCancel() {
        vm.edit_section.delete_size = false;
    }

    // function openCreateSectionSize() {
    //     vm.edit_section.new_size_name = "";
    //     vm.edit_section.create_new_size = true;
    // }

    function addEditSectionSize() {
        var new_section_size = {
            size_name: vm.edit_section.new_size_name,
            size_display_name: '',
            description: '',
            active: true,
            new: true,
            apply_all_items: true
        };
        vm.edit_section.sizes.push(new_section_size);
    }

    function createSection() {
        vm.new_section.submit = true;
        if (vm.new_section_form.$valid) {
            vm.new_section.processing = true;
            Menu.post('create_menu_type', vm.new_section).then(function (response) {
                vm.new_section.success = true;
                vm.new_section.processing = false;

                var new_section = response.data;
                new_section.menu_items = new_section.items;

                Menu.menu_types.push(new_section);

                if (typeof new_section.items != 'undefined') {
                    var item_index;
                    for (item_index = 0; item_index < new_section.items.length; item_index++) {
                        var added_item = new_section.items[item_index];
                        added_item.section = new_section;

                        Menu.all_items.push(added_item);
                    }
                }


                $("#add-section-modal").modal('toggle');
                $timeout(resetForm, 3500);
            });
        }
    }

    function updateSection() {
        vm.edit_section.submit = true;
        if (vm.edit_section_form.$valid) {
            vm.edit_section.processing = true;
            Menu.post('update_menu_type', vm.edit_section).then(function (response) {
                var section = response.data;

                for (var size_index = 0; size_index < section.sizes.length; size_index++) {
                    section.sizes[size_index]['priority'] = parseInt(section.sizes[size_index]['priority']);
                }



                Menu.menu_types[vm.edit_section.index] = section;

                vm.edit_section.success = true;
                vm.edit_section.processing = false;

                $("#edit-section-modal").modal('toggle');
                $timeout(resetForm, 3500);
            });
        }
    }

    function openEditModifierGroup(index, modifier_group, event) {
        modifier_group.default_item_price = parseInt(modifier_group.default_item_price);
        modifier_group.default_item_max = parseInt(modifier_group.default_item_max);
        modifier_group.priority = parseInt(modifier_group.priority);

        vm.edit_modifier_group = modifier_group;
        vm.edit_modifier_group.index = index;
        $("#edit-modifier-modal").modal('toggle');
        event.stopPropagation();
    }

    function updateModifierGroup() {
        vm.edit_modifier_group.submit = true;
        if (vm.edit_mod_group_form.$valid) {
            vm.edit_modifier_group.processing = true;
            Menu.post('update_modifier_group', vm.edit_modifier_group).then(function (response) {
                //vm.menu.modifier_groups[vm.edit_modifier_group.index] = response.data;

                vm.edit_modifier_group.success = true;
                vm.edit_modifier_group.processing = false;
                $("#edit-modifier-modal").modal('toggle');
                $timeout(resetForm, 3500);
            });
        }
    }

    function createModifierGroup() {
        vm.new_modifier_group.submit = true;
        if (vm.new_mod_group_form.$valid) {
            vm.new_modifier_group.processing = true;
            Menu.post('create_modifier_group', {new_modifier_group : vm.new_modifier_group, modifier_groups: Menu.modifier_groups}).then(function (response) {
                response.data.modifier_items = response.data.created_modifier_items;

                Menu.modifier_groups = response.data.updated_mod_groups;

                var mod_item_index;

                for (mod_item_index = 0; mod_item_index < response.data.created_modifier_items.length; mod_item_index++) {
                    var added_item = response.data.modifier_items[mod_item_index];
                    added_item.modifier_group = response.data;

                    Menu.all_modifier_items.push(added_item);
                }

                vm.new_modifier_group.success = true;
                vm.new_modifier_group.processing = false;
                $("#add-new-modifier-modal").modal('toggle');
                $timeout(resetForm, 3500);
            });
        }
    }

    function viewItem(section, item, section_index, item_index) {
        Menu.current_item_id = item.item_id;
        Menu.current_mod_item_id = false;

        //If we access viewItem with Swearch, we need to figure out the item's index for the regular menu view to update it later
        if (section_index === 'search_item') {
            for (section_index = 0; section_index < vm.menu.menu_types.length; section_index++) {
                var section_items = vm.menu.menu_types[section_index]['menu_items'];

                for (item_index = 0; item_index < section_items.length; item_index++) {
                    if (section_items[item_index]['item_id'] === item.item_id) {
                        Menu.current_item_section_index = section_index;
                        Menu.current_item_index = item_index;
                        break;
                    }
                }
            }
        }
        else {

            Menu.current_item_section_index = section_index;
            Menu.current_item_index = item_index;
        }

        if (item_index == 'new') {
            vm.new_item.menu_type = section;
            $("#new-item-copy-mods-modal").modal('toggle');
        }
        else {
            Menu.get('menu_type/' + section.menu_type_id + '/item/' + item.item_id).then(function (response) {
                $location.path('/menu/item');
            });
            openItemPage(section.menu_type_id, item.item_id);
        }
    }

    function setNewItemModGroupConfig(mod_item_id) {
        $('#your-modal-id').modal('hide');
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        openItemPage(vm.new_item.menu_type.menu_type_id, vm.new_item.item_id, mod_item_id);
    }

    function openItemPage(menu_type_id, item_id, mod_group_item_id) {
        Menu.get('menu_type/' + menu_type_id + '/item/' + item_id+'/mod_group_item/'+mod_group_item_id).then(function (response) {
            $location.path('/menu/item');
        });
    }

    function viewModifierItem(modifier_group, modifier_item, modifier_group_index, modifier_item_index) {
        Menu.current_item_id = false;
        Menu.current_mod_item_id = modifier_item.modifier_item_id;

        //If we access viewItem with Swearch, we need to figure out the item's index for the regular menu view to update it later
        if (modifier_group_index === 'search_item') {
            for (modifier_group_index = 0; modifier_group_index < vm.menu.menu_types.length; modifier_group_index++) {
                var modifier_group_items = vm.menu.menu_types[modifier_group_index]['menu_items'];

                for (modifier_item_index = 0; modifier_item_index < modifier_group_items.length; modifier_item_index++) {
                    if (modifier_group_items[modifier_item_index]['modifier_item_id'] === modifier_item.modifier_item_id) {
                        Menu.current_item_modifier_group_index = modifier_group_index;
                        Menu.current_modifier_item_index = modifier_item_index;
                        break;
                    }
                }
            }
        }
        else {
            Menu.current_mod_group.index = modifier_group_index;
            Menu.current_mod_item.index = modifier_item_index;
        }

        Menu.current_mod_group.index = modifier_group_index;
        Menu.current_mod_item.index = modifier_item_index;
        Menu.get('modifier_group/' + modifier_group.modifier_group_id + '/modifier_item/' + modifier_item.modifier_item_id).then(function (response) {
            $location.path('/menu/modifier_item');
        });
    }


    function filterItems(item) {

        if (vm.menu.getSearchTextLength() < 3) {
            return false;
        }
        var filter_text_reg_exp = new RegExp(vm.menu.search_text.toUpperCase(), 'g');

        if (item.item_name.toUpperCase().match(filter_text_reg_exp)) {
            return true;
        }
        else {
            return false;
        }
    }

    function filterModItems(mod_item) {
        if (vm.menu.getSearchTextLength() < 3) {
            return false;
        }
        var filter_text_reg_exp = new RegExp(vm.menu.search_text.toUpperCase(), 'g');

        if (mod_item.modifier_item_name.toUpperCase().match(filter_text_reg_exp)) {
            return true;
        }
        else {
            return false;
        }
    }

    function sortNumber(indx) {
        return indx + 1;
    }

    function openSectionsPriority() {
        vm.sections_priority_menu = vm.menu;
    }

    function reOrderSections() {
        vm.sections_priority_processing = true;
        Menu.post('menu_type/priority_order', vm.sections_priority_menu).then(function (response) {
            vm.sections_priority_processing = false;
            vm.menu = vm.sections_priority_menu;
            $("#re-order-sections-modal").modal('toggle');
        });
    }

    function openModGroupsPriority() {
        vm.mod_groups_priority_menu = vm.menu;
    }

    function reOrderModGroups() {
        vm.mod_groups_priority_processing = true;
        Menu.post('modifier_group/priority_order', vm.mod_groups_priority_menu).then(function (response) {
            vm.mod_groups_priority_processing = false;
            vm.full_menu = vm.mod_groups_priority_menu;
            $("#re-order-mod-groups-modal").modal('toggle');
        });
    }

    function openItemPriority(index, section) {
        vm.item_priority_section = section;
        item_priority_section_index = index;
    }

    function reOrderItems() {
        vm.item_priority_processing = true;
        Menu.post('items/priority_order', vm.item_priority_section).then(function (response) {
            vm.item_priority_processing = false;
            vm.menu.menu_types[item_priority_section_index] = vm.item_priority_section;
            $("#re-order-items-modal").modal('toggle');
        });
    }

    function openModifierItemPriority(index, modifier_group) {
        vm.modifier_item_priority_group = modifier_group;
        modifier_item_priority_section_index = index;
    }

    function reOrderModifierItems() {
        vm.modifier_item_priority_processing = true;
        Menu.post('modifier_items/priority_order', vm.modifier_item_priority_group).then(function (response) {
            vm.modifier_item_priority_processing = false;
            vm.menu.modifier_groups[modifier_item_priority_section_index] = vm.modifier_item_priority_group;
            $("#re-order-modifier-items-modal").modal('toggle');
        });
    }

    function deleteItemDialog(section,item,section_index, item_index) {
        SweetAlert.swal({
                title: "Warning",
                text: "Are you sure you want to delete the item "+section.menu_type_name+" - "+item.item_id + " " + item.item_name + " ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function (isConfirm) {
                if (isConfirm) {
                    $http.delete('/menu/menu_item/'+item.item_id).then(function (response) {
                        var deleted_section_index = UtilityService.findIndexByKeyValue(Menu.menu_types, 'menu_type_id', section.menu_type_id);
                        var deleted_section_item_index = UtilityService.findIndexByKeyValue(Menu.menu_types[deleted_section_index]['menu_items'], 'item_id', item.item_id);
                        Menu.menu_types[deleted_section_index]['menu_items'].splice(deleted_section_item_index, 1);

                        var all_items_index = UtilityService.findIndexByKeyValue(Menu.all_items, 'item_id', item.item_id);
                        Menu.all_items.splice(all_items_index, 1);
                    });
                }
            });
    }

    function deleteModifierItemDialog(modifier_group, modifier_item,modifier_group_index, modifier_item_index) {
        SweetAlert.swal({
                title: "Warning",
                text: "Are you sure you want to delete the modifier item "+modifier_group.modifier_group_name+" - "+modifier_item.modifier_item_id + " " + modifier_item.modifier_item_name + " ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function (isConfirm) {
                if (isConfirm) {
                    $http.delete('/menu/modifier_item/'+modifier_item.modifier_item_id).then(function (response) {
                        Menu.modifier_groups[modifier_group_index]['modifier_items'].splice(modifier_item_index, 1);

                        var deleted_modifier_group_index = UtilityService.findIndexByKeyValue(Menu.modifier_groups, 'modifier_group_id', modifier_group.modifier_group_id);
                        var deleted_modifier_group_mod_item_index = UtilityService.findIndexByKeyValue(Menu.modifier_groups[deleted_modifier_group_index]['modifier_items'],
                            'modifier_item_id', modifier_item.modifier_item_id);
                        Menu.modifier_groups[deleted_modifier_group_index]['modifier_items'].splice(deleted_modifier_group_mod_item_index, 1);

                        var all_mod_items_index = UtilityService.findIndexByKeyValue(Menu.all_modifier_items, 'modifier_item_id', modifier_item.modifier_item_id);
                        Menu.all_modifier_items.splice(all_mod_items_index, 1);
                    });
                }
            });
    }

    function deleteMenuTypeDialog(index,section) {
        SweetAlert.swal({
                title: "Warning",
                text: "Are you sure you want to delete the menu section "+section.menu_type_id+" - "+section.menu_type_name +  " ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function (isConfirm) {
                if (isConfirm) {
                    $http.delete('/menu/menu_type/'+section.menu_type_id).then(function (response) {
                        Menu.menu_types.splice(index, 1);
                    });
                }
            });
    }

    function deleteModifierGroupDialog(index,modifier_group) {
        SweetAlert.swal({
                title: "Warning",
                text: "Are you sure you want to delete the modifier group "+modifier_group.modifier_group_id+" - "+modifier_group.modifier_group_name +  " ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function (isConfirm) {
                if (isConfirm) {
                    $http.delete('/menu/modifier_group/'+modifier_group.modifier_group_id).then(function (response) {
                        Menu.modifier_groups.splice(index, 1);
                    });
                }
            });
    }

    function firstFiveCharacters(text) {
        if (!!text) {
            if (text) {
                if (text.length > 3 && text) {
                    return text.substring(0, 4);
                }
                else {
                    return text;
                }
            }
            else {
                return "";
            }
        }
        else {
            return "-";
        }
    }

    function setNewSectionActiveAllDay() {
        if (vm.new_section.active_all_day) {
            vm.new_section.start_time = '12:00';
            vm.new_section.start_time_am_pm = 'am';
            vm.new_section.end_time = '11:59';
            vm.new_section.end_time_am_pm = 'pm';
        }
    }

    function setEditSectionActiveAllDay() {
        if (vm.edit_section.active_all_day) {
            vm.edit_section.start_time = '12:00';
            vm.edit_section.start_time_am_pm = 'am';
            vm.edit_section.end_time = '11:59';
            vm.edit_section.end_time_am_pm = 'pm';
        }
    }

    function menuTypeFilter(menu_type) {
        if (!vm.filter_hide_inactive) {
            return true;
        }
        else {
            if (menu_type.active) {
                return true;
            }
            else {
                return false;
            }
        }
    }

    function modifierGroupFilter(modifier_group) {
        if (!vm.filter_hide_inactive) {
            return true;
        }
        else {
            if (modifier_group.active) {
                return true;
            }
            else {
                return false;
            }
        }
    }

    function itemsFilter(item) {
        if (!vm.filter_hide_inactive) {
            return true;
        }
        else {
            if (item.active == 1 || item.active == 'Y') {
                return true;
            }
            else {
                return false;
            }
        }
    }

    function updateSizePrintName(size) {
        if (typeof size.size_print_name == 'undefined') {
            size.size_print_name = size.size_name;
        }
    }

}