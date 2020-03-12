angular.module('adminPortal.menu').controller('MenuUpsellCreateCtrl', MenuUpsellCreateCtrl);

function MenuUpsellCreateCtrl(Menu, UtilityService, SweetAlert, $location) {
    var vm = this;

    vm.menu = Menu;
    vm.menu.search_text = '';

    vm.selected_item = {};

    vm.unselected_menu_types = [];
    vm.selected_menu_types = [];

    vm.processing = false;

    vm.setUpsellItem = setUpsellItem;
    vm.showSearch = showSearch;
    vm.filterItems = filterItems;
    vm.addMenuTypeSelected = addMenuTypeSelected;
    vm.removeMenuTypeSelected = removeMenuTypeSelected;
    vm.create = create;

    function setUpsellItem(section, item) {
        vm.selected_item = item;
        vm.unselected_menu_types = Menu.menu_types.slice(0);
        vm.unselected_menu_types = UtilityService.sortArrayByPropertyAlpha(vm.unselected_menu_types, 'menu_type_name');
    }

    function showSearch() {
        if (!!vm.selected_item.item_id) {
            return false;
        }
        else {
            return true;
        }
    }

    function addMenuTypeSelected(index, section) {
        vm.unselected_menu_types.splice(index, 1);
        vm.selected_menu_types.push(section);

        vm.unselected_menu_types = UtilityService.sortArrayByPropertyAlpha(vm.unselected_menu_types, 'menu_type_name');
        vm.selected_menu_types = UtilityService.sortArrayByPropertyAlpha(vm.selected_menu_types, 'menu_type_name');
    }

    function removeMenuTypeSelected(index, section) {
        vm.selected_menu_types.splice(index, 1);
        vm.unselected_menu_types.push(section);

        vm.unselected_menu_types = UtilityService.sortArrayByPropertyAlpha(vm.unselected_menu_types, 'menu_type_name');
        vm.selected_menu_types = UtilityService.sortArrayByPropertyAlpha(vm.selected_menu_types, 'menu_type_name');
    }

    function create() {
        vm.processing = true;

        var data = {
            item: vm.selected_item,
            menu_types: vm.selected_menu_types
        };

        Menu.post('create_category_upsell', data).then(function (response) {
            Menu.loadUpsells();

            SweetAlert.swal({
                    title: "Success.",
                    text: "Upsell Successfully Created.",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#488214",
                    confirmButtonText: "Great"
                },
                function () {
                    $location.path('/menu/category_upsells');
                });
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
}
