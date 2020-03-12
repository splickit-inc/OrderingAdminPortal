angular.module('adminPortal.brand').controller('BrandEditCtrl', function (Brands, UtilityService) {
    var vm = this;

    vm.processing = true;
    vm.brandModel = {};
    vm.brand_id = undefined;
    vm.brandModel.default_url_customized = false;
    vm.error = false;
    vm.submitButton = submitButton;
    vm.updateDefaultUrl = updateDefaultUrl;

    function load() {
        var currentBrandSelected = Brands.currentBrandSelected;

        if(!!currentBrandSelected)
        {
            vm.brandModel = currentBrandSelected;
            vm.processing = false;
            return;
        }

        Brands.getCurrentBrand().success(function (result) {
            vm.brandModel = UtilityService.parseStringBooleanValues(result.brand);
            vm.processing = false;
        }).catch(function (response) {
            vm.error = true;
            vm.errorMessage = "The brand does not exist.";
            vm.processing = false;
        });
    }

    function submitButton() {
        var brand_info = UtilityService.parseBooleanToStringValues(vm.brandModel);
        vm.error = false;
        vm.processing = true;
        Brands.editBrand(brand_info.brand_id, brand_info).success(function (result) {
            vm.processing = false;
        }).catch(function () {
            vm.error = true;
            vm.errorMessage = "Something went wrong, Please try again.";
        });
    }

    function updateDefaultUrl() {
        if (!vm.brandModel.default_url_customized) {
            var removed_alpha_numeric_name = vm.brandModel.brand_name.replace(/\W/g, '');
            vm.brandModel.default_url = removed_alpha_numeric_name + ".splickit.com";
        }
    }

    load();
});