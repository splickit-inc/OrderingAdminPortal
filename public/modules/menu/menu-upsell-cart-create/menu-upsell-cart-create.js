angular.module('adminPortal.menu').controller('MenuCartUpsellCreateCtrl', MenuCartUpsellCreateCtrl);

function MenuCartUpsellCreateCtrl(Menu, UtilityService, SweetAlert, $location) {
    var vm = this;

    vm.menu = Menu;

    vm.processing = false;
    vm.selectedItems = [];

    vm.filterItems = filterItems;
    vm.setUpsellItem = setUpsellItem;
    vm.removeUpsellItem = removeUpsellItem;
    vm.submitUpsells = submitUpsells;


    Menu.loadIfUndefined();


    function setUpsellItem(item) {
        UtilityService.moveBetweenWithoutDuplicates(vm.menu.all_items, vm.selectedItems, item, 'item_id');
    }

    function removeUpsellItem(item) {
        UtilityService.moveBetweenWithoutDuplicates(vm.selectedItems, vm.menu.all_items, item, 'item_id');
    }

    function submitUpsells() {
        vm.processing = true;

        Menu.post('create_cart_upsell', {items: vm.selectedItems}).success(function (response) {
            vm.processing = false;
            SweetAlert.swal({
                    title: "Success.",
                    text: "Upsell Successfully Created.",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#488214",
                    confirmButtonText: "Great"
                },
                function () {
                    $location.path('/menu/cart_upsells');
                });
        });
    }

    function filterItems(item) {
        var filter_text_reg_exp = new RegExp(vm.menu.search_text.toUpperCase(), 'g');
        return item.item_name.toUpperCase().match(filter_text_reg_exp);
    }
}
