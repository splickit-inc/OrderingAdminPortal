angular.module('adminPortal.brand').controller('MerchantGroupsEditCtrl',function($scope, EmbeddedMerchantSearch, $http, MerchantGroup, SweetAlert, $location){
    var vm = this;

    vm.edit_merchant_group = {};
    vm.edit_merchant_group.type = 'merchant';
    EmbeddedMerchantSearch.search_url = 'merchant_search';

    vm.embeddedMerchantSearch = EmbeddedMerchantSearch;
    vm.merchantGroupFactory = MerchantGroup;

    vm.setMerchants = setMerchants;
    vm.setGroups = setGroups;
    vm.updateMerchantGroup = updateMerchantGroup;

    getMerchantGroup();
    
    function getMerchantGroup() {
        $http.get('/merchant_group/get_current').success(function (response) {
            EmbeddedMerchantSearch.selected_merchants = response.merchants;
            vm.edit_merchant_group = response.merchant_group;
            MerchantGroup.selected_merchant_groups = response.groups;

            EmbeddedMerchantSearch.merchantSearch();
            MerchantGroup.merchantGroupSearch();
        });
    }

    function setMerchants() {
        vm.edit_merchant_group.merchants = EmbeddedMerchantSearch.selected_merchants;
    }

    function setGroups() {
        vm.edit_merchant_group.groups = MerchantGroup.selected_merchant_groups;
    }

    function updateMerchantGroup() {
        $http.put('merchant_group/update', vm.edit_merchant_group).success(function (response) {

            vm.edit_merchant_group.processing = false;
            vm.edit_merchant_group.submit = false;
            SweetAlert.swal({
                    title: "The merchant group " + vm.edit_merchant_group.name + " has been updated!",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#488214",
                    confirmButtonText: "Great!"
                },
                function () {
                    $location.path('brands/merchant_groups');
                });

        }).catch(function (error) {
            vm.edit_merchant_group.processing = false;
            vm.edit_merchant_group.submit = false;
            vm.error = true;
        });
    }


});