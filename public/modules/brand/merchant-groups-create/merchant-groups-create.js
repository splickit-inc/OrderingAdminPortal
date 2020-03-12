angular.module('adminPortal.brand').controller('MerchantGroupsCreateCtrl',function($scope, EmbeddedMerchantSearch, $http, MerchantGroup, SweetAlert, $location){
    var vm = this;

    vm.new_merchant_group = {};
    vm.new_merchant_group.type = 'merchant';
    EmbeddedMerchantSearch.search_url = 'merchant_search';
    

    vm.groupTypeClass = groupTypeClass;
    vm.setMerchants = setMerchants;
    vm.setGroups = setGroups;
    vm.create = create;


    function groupTypeClass(value, current_set_variable) {
        if (current_set_variable == value) {
            return 'btn-success';
        }
        else {
            return 'btn-default';
        }
    }

    function setMerchants() {
        vm.new_merchant_group.merchants = EmbeddedMerchantSearch.selected_merchants;
        console.log(vm.new_merchant_group.merchants);
    }

    function setGroups() {
        vm.new_merchant_group.groups = MerchantGroup.selected_merchant_groups;
    }

    function create() {
        $http.post('/merchant_group', vm.new_merchant_group).success(function (response) {

            vm.new_merchant_group.processing = false;
            vm.new_merchant_group.submit = false;
            SweetAlert.swal({
                    title: "The new " + vm.new_merchant_group.name + " merchant group has been created!",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#488214",
                    confirmButtonText: "Great!"
                },
                function () {
                    $location.path('merchant_groups');
                });

        }).catch(function (error) {
            vm.new_merchant_group.processing = false;
            vm.new_merchant_group.submit = false;
            vm.error = true;
        });
    }

    EmbeddedMerchantSearch.merchantSearch();
    MerchantGroup.merchantGroupSearch();
});