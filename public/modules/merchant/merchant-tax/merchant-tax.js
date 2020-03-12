angular.module('adminPortal.merchant').controller('MerchantTaxCtrl', MerchantTaxCtrl);

function MerchantTaxCtrl(Merchant, $timeout, $scope) {
    var vm = this;

    vm.sales_tax = {};
    vm.fixed_tax = {};
    vm.delivery_tax = {};

    vm.updateFixTax = updateFixTax;
    vm.updateSalesTax = updateSalesTax;
    vm.updateDeliveryTax = updateDeliveryTax;

    load();

    function load() {
        Merchant.index('tax').then(function (response) {
            response.data.fixed_tax.amount = parseFloat(response.data.fixed_tax.amount);
            response.data.sales_tax.rate = parseFloat(response.data.sales_tax.rate);
            response.data.delivery_tax.rate = parseFloat(response.data.delivery_tax.rate);

            vm.sales_tax = response.data.sales_tax;
            vm.fixed_tax = response.data.fixed_tax;
            vm.delivery_tax = response.data.delivery_tax;
            vm.delivery_tax.locale_description = !!vm.delivery_tax.locale_description ? vm.delivery_tax.locale_description.trim() : vm.delivery_tax.locale_description;
        });
    }

    function resetForm() {
        vm.sales_tax.success = false;
        vm.fixed_tax.success = false;
        vm.delivery_tax.success = false;
    }

    function updateFixTax() {
        vm.fixed_tax_submit = true;

        if (vm.fixed_tax_form.$valid) {
            vm.fixed_tax.processing = true;

            Merchant.update('fixed_tax', vm.fixed_tax).then(function (response) {
                vm.fixed_tax.processing = false;
                vm.fixed_tax.success = true;
                $timeout(resetForm, 3500);
            });
        }
    }

    function updateSalesTax() {
        vm.sales_tax.submit = true;

        if (vm.sales_tax_form.$valid) {
            vm.sales_tax.processing = true;

            Merchant.update('sales_tax', vm.sales_tax).then(function (response) {
                vm.sales_tax.processing = false;
                vm.sales_tax.success = true;
                $timeout(resetForm, 3500);
                Merchant.markProgressMilestoneComplete('sales_tax');
            });
        }
    }

    function updateDeliveryTax() {
        vm.delivery_tax.submit = true;

        if (vm.delivery_tax_form.$valid) {
            vm.delivery_tax.processing = true;
            if (!vm.delivery_tax.locale_description || vm.delivery_tax.locale_description === '') {
                vm.delivery_tax.locale_description = ' ';
            }

            Merchant.update('delivery_tax', vm.delivery_tax).then(function (response) {
                vm.delivery_tax.processing = false;
                vm.delivery_tax.success = true;
                $timeout(resetForm, 3500);
            });
        }
    }

    $scope.$on('current_merchant:updated', function (event, data) {
        load();
    });
}