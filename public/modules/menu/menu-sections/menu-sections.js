angular.module('adminPortal.menu').controller('MenuSectionsCtrl', MenuSectionsCtrl);

function MenuSectionsCtrl(Menu, $timeout, UtilityService, SweetAlert) {
    var vm = this;

    vm.sections = [];

    vm.new_section = {};
    vm.edit_section = {};

    vm.delete_menu_type = {};
    vm.modifier_groups = [];
    vm.new_modifier_group = {};

    vm.modifier_types = [];
    vm.categories = [];
    vm.menu_name = "";

    var edit_section_index;

    //Functions
    vm.createSection = createSection;
    vm.decodeCategory = decodeCategory;
    vm.deleteMenuTypeConfirmation = deleteMenuTypeConfirmation;
    vm.createModifierGroup = createModifierGroup;
    vm.decodeModifierType = decodeModifierType;
    vm.editModifierGroupDialog = editModifierGroupDialog;
    vm.deleteModifierGroupConfirmDialog = deleteModifierGroupConfirmDialog;
    vm.yestNoTrueFalseConvert = yestNoTrueFalseConvert;
    vm.changeRoute = changeRoute;

    load();

    function load() {
        Menu.get('menu_types').then(function (response) {
            vm.categories = response.data.lookup.cat_id;
            vm.modifier_types = response.data.lookup.modifier_type;

            vm.sections = response.data.menu.menu.menu_types;

            vm.modifier_groups = response.data.menu.menu.modifier_groups;
            vm.menu_name = response.data.menu.menu_name;
        });
    }

    function resetForm() {
        vm.new_section = {};
        vm.new_section_form.$setPristine();
        vm.new_section_form.items.$faded = false;
        vm.new_section_form.sizes.$faded = false;

        vm.new_modifier_group = {};
        vm.new_mod_group_form.$setPristine();
        vm.new_mod_group_form.item_list.$faded = false;
    }

    function createSection() {
        vm.new_section.submit = true;
        if (vm.new_section_form.$valid) {
            vm.new_section.processing = true;
            Menu.post('create_menu_type', vm.new_section).then(function (response) {
                vm.sections.push(response.data);
                vm.new_section.success = true;
                vm.new_section.processing = false;

                $("#add-section-modal").modal('toggle');
                $timeout(resetForm, 3500);
            });
        }
    }

    function deleteMenuTypeConfirmation(menu_type, index) {
        vm.delete_menu_type.menu_type = menu_type;
        vm.delete_menu_type.index = index;

        SweetAlert.swal({
                title: "Are you want to delete the Menu Section " + vm.delete_menu_type.menu_type.menu_type_name + "?",
                text: "You will not be able to recover the section.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!"
            },
            function () {
                SweetAlert.swal("This is where we'd actually delete the menu section, but it's not set up with the back end yet.");
            });
    }

    function createModifierGroup() {
        vm.new_modifier_group.submit = true;
        if (vm.new_mod_group_form.$valid) {
            vm.new_modifier_group.processing = true;
            Menu.post('create_modifier_group', vm.new_modifier_group).then(function (response) {
                vm.modifier_groups.push(response.data);
                vm.new_modifier_group.success = true;
                vm.new_modifier_group.processing = false;
                $("#add-new-modifier-modal").modal('toggle');
                $timeout(resetForm, 3500);
            });
        }
    }

    function decodeModifierType(modifier_type) {
        return UtilityService.returnOneArrayFieldWithAnotherArrayFieldValue(vm.modifier_types, 'type_id_value', 'type_id_name', modifier_type);
    }

    function editModifierGroupDialog(modifier_group) {
        return UtilityService.returnOneArrayFieldWithAnotherArrayFieldValue(vm.categories, 'type_id_value', 'type_id_name', modifier_group);
    }

    function deleteModifierGroupConfirmDialog(modifier_group) {
        return UtilityService.returnOneArrayFieldWithAnotherArrayFieldValue(vm.categories, 'type_id_value', 'type_id_name', modifier_group);
    }

    function decodeCategory(cat_id) {
        return UtilityService.returnOneArrayFieldWithAnotherArrayFieldValue(vm.categories, 'type_id_value', 'type_id_name', cat_id);
    }

    function yestNoTrueFalseConvert(val) {
        return UtilityService.yesNoTrueFalseConversion(val);
    }

    function changeRoute(new_route) {
        UtilityService.changeRoute(new_route);
    }
}
