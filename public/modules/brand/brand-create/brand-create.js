angular.module('adminPortal.brand').controller('BrandCreateCtrl', BrandCreateCtrl);

function BrandCreateCtrl($http, SweetAlert, Lookup, BrandLookup, $location) {
    var vm = this;
    vm.error = false;
    setNewBrand();
    vm.brand_lookup = BrandLookup;

    function setNewBrand() {
        vm.new_brand = {};
        vm.new_brand.active = true;
        vm.new_brand.allows_tipping = true;
        vm.new_brand.nutrition_flag = false;
        vm.new_brand.production = true;
        vm.new_brand.last_orders_displayed = '';
        vm.new_brand.support_email = '';
        vm.new_brand.allows_in_store_payments = true;
        vm.new_brand.last_orders_displayed = 1;
        vm.new_brand.order_delivery_methods = [];
        vm.new_brand.payment_types = [];

        vm.new_brand.default_url_customized = false;
        BrandLookup.setSelectionsNew();
    }

    vm.createNewBrand = createNewBrand;
    vm.updateDefaultUrl = updateDefaultUrl;
    vm.selectOrderDeliveryMethods = selectOrderDeliveryMethods;
    vm.selectPaymentTypes = selectPaymentTypes;

    function createNewBrand() {
        vm.new_brand.submit = true;

        validateOrderDeliveryTypes();
        validatePaymentTypes();

        if (vm.new_brand_form.$valid) {
            vm.new_brand.processing = true;
            vm.error = false;
            $http.post('/brand/create', vm.new_brand).success(function (response) {

                vm.new_brand.processing = false;
                vm.new_brand.submit = false;
                SweetAlert.swal({
                        title: "The new " + vm.new_brand.name + " brand has been created!",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#488214",
                        confirmButtonText: "Great!"
                    },
                    function () {
                        $location.path('brands');
                    });

            }).catch(function (error) {
                vm.new_brand.processing = false;
                vm.new_brand.submit = false;
                vm.error = true;
            });
        }
    }

    function validateOrderDeliveryTypes() {
        if (vm.brand_lookup.all_selections.order_del_type.selected.length > 0) {
            vm.new_brand_form.production.$setValidity("order_del_types", true);
            vm.new_brand.order_delivery_methods = vm.brand_lookup.all_selections.order_del_type.selected;
        }
        else {
            vm.new_brand_form.production.$setValidity("order_del_types", false);
        }
    }

    function validatePaymentTypes() {
        if (vm.brand_lookup.all_selections.Splickit_Accepted_Payment_Types.selected.length > 0) {
            vm.new_brand_form.production.$setValidity("payment_types", true);
            vm.new_brand.payment_types = vm.brand_lookup.all_selections.Splickit_Accepted_Payment_Types.selected;
        }
        else {
            vm.new_brand_form.production.$setValidity("payment_types", false);
        }
    }

    function updateDefaultUrl() {
        if (!vm.new_brand.default_url_customized) {
            var removed_alpha_numeric_name = vm.new_brand.name.replace(/\W/g, '');
            vm.new_brand.default_url = removed_alpha_numeric_name + ".splickit.com";
        }
    }

    function selectOrderDeliveryMethods() {
        BrandLookup.setLookupToActive('order_del_type');
        $("#lookup-select-modal").modal('toggle');
    }

    function selectPaymentTypes() {
        BrandLookup.setLookupToActive('Splickit_Accepted_Payment_Types');
        $("#lookup-select-modal").modal('toggle');
    }

    BrandLookup.loadCreateLookups();
}