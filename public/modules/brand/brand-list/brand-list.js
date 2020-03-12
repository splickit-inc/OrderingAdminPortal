angular.module('adminPortal.brand').controller('BrandListCtrl', function (Brands, UtilityService, $location, SweetAlert) {
    var vm = this;

    vm.processing = false;
    vm.recently_visited = [];
    vm.search_result = [];
    vm.search_text = "";
    vm.first_search = false;
    vm.isUserOperator = function () {

    };

    vm.brandsSearch = function () {
        vm.processing = true;
        Brands.searchBrands(vm.search_text).success(function (result) {
            vm.first_search = true;
            vm.search_result = result.brands;
            vm.processing = false;
        }).error(function (response, error) {
            vm.processing = false;
            SweetAlert.swal({
                title: "Warning",
                text: "Sorry, your search timed out. Please try again.",
                type: "warning",
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ok"
            });
        });
    };

    var active_letter;
    vm.currentLetter = function (letter) {
        if (letter === active_letter) {
            return "alphabet-active";
        }
        else {
            return "alphabet";
        }
    };

    vm.brandLetter = function (letter) {
        vm.search_text = letter;
        if (active_letter !== letter) {
            active_letter = letter;
            vm.result_merchants = [];
            vm.processing = true;
            Brands.getBrandsWithFirstLetter(letter).success(function (result) {
                vm.first_search = true;
                vm.search_result = result.brands;
                vm.processing = false;
            });
        }
    };
    vm.hasSearchResult = function () {

    };

    vm.viewRecentlyVisited = function () {

    };

    /**
     * Edit Modal
     */
    vm.editModalName = 'editModal1';
    vm.current_brand_selected = {};

    vm.viewBrand = function (current_brand_selected) {
        vm.current_brand_selected = UtilityService.parseStringBooleanValues(current_brand_selected);
        Brands.currentBrandSelected = vm.current_brand_selected;
        $location.path('/brand');
    };

    vm.submitEditBrand = function () {
        $('#' + vm.editModalName).modal('hide');
        vm.current_brand_selected = UtilityService.parseBooleanToStringValues(vm.current_brand_selected);
        vm.processing = true;
        Brands.editBrand(vm.current_brand_selected.brand_id, vm.current_brand_selected).success(function (result) {
            vm.current_brand_selected = result.brand;
            vm.search_result.forEach(function (item, i) {
                if (item.brand_id === result.brand.brand_id) {
                    vm.search_result[i] = result.brand;
                }
            });
            vm.processing = false;
        });
    };

    vm.brandsSearch();
});
