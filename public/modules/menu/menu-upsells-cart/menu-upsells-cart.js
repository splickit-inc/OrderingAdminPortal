angular.module('adminPortal.menu').controller('MenuCartUpsellListCtrl', MenuCartUpsellListCtrl);

function MenuCartUpsellListCtrl(Menu, SweetAlert, $location, $timeout) {
    var vm = this;

    vm.createNew = createNew;
    vm.deleteUpsell = deleteUpsell;
    vm.deleteUpsellConfirmation = deleteUpsellConfirmation;

    vm.menu = Menu;
    Menu.loadCartUpsells();

    function createNew() {
        $location.path('/menu/cart_upsells/create');
    }

    function deleteUpsellConfirmation(upsell) {
        SweetAlert.swal({
                title: "Warning.",
                text: "Are you sure you want to remove the cart upsell " + upsell.item_name + " for the menu " + vm.menu.menuWithCardUpsells.name + "?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Remove"
            },
            function (isConfirm) {
                if (isConfirm) {
                    deleteUpsell(upsell);
                }
            });
    }

    function deleteUpsell(upsell) {
        Menu.post('delete_cart_upsell', upsell).success(function (response) {
            vm.menu.menuWithCardUpsells.cart_upsells.splice(vm.menu.menuWithCardUpsells.cart_upsells.indexOf(upsell), 1);
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
