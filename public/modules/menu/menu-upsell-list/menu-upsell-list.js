angular.module('adminPortal.menu').controller('MenuUpsellListCtrl', MenuUpsellListCtrl);

function MenuUpsellListCtrl(Menu, SweetAlert, $location, $timeout) {
    var vm = this;

    vm.current_item = {};
    vm.menu_types = [];
    vm.modifier_groups = [];

    vm.new = false;

    vm.delete_success = false;

    var delete_upsell = {};

    vm.page_open_sections = {
        sizes: true,
        modifier_groups: true,
        modifier_comes_with: true
    };

    //Methods
    vm.createNew = createNew;
    vm.deleteUpsell = deleteUpsell;
    vm.deleteUpsellConfirmation = deleteUpsellConfirmation;

    vm.menu = Menu;
    Menu.loadUpsells();

    function createNew() {
        $location.path('/menu/category_upsell/create');
    }

    function deleteUpsellConfirmation(upsell, menu_type_index, index) {
        delete_upsell.menu_type_index = menu_type_index;
        delete_upsell.upsell_index = index;
        delete_upsell.upsell_record = upsell;

        SweetAlert.swal({
                title: "Warning.",
                text: "Are you sure you want to remove the upsell for the section " + upsell.menu_type_name + " and item " + upsell.item_name + "?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Remove"
            },
            function (isConfirm) {
                if (isConfirm) {
                    deleteUpsell();
                }
            });
    }

    function deleteUpsell() {
        Menu.post('delete_category_upsell', delete_upsell.upsell_record).then(function (response) {
            vm.menu.upsells[delete_upsell.menu_type_index].upsells.splice(delete_upsell.upsell_index, 1);
            Menu.loadUpsells();
            SweetAlert.swal({
                title: "Deleted.",
                text: "The upsell was deleted.",
                type: "success",
                showCancelButton: false,
                confirmButtonColor: "#488214",
                confirmButtonText: "Great"
            });
        });
    }


}
