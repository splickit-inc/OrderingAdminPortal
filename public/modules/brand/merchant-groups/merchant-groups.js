angular.module('adminPortal.brand').controller('MerchantGroupsCtrl',function($location, $http, SweetAlert){
    var vm = this;

    vm.results = [];

    vm.createMerchantGroup = createMerchantGroup;
    vm.deleteMerchantGroupDialog = deleteMerchantGroupDialog;
    vm.editMerchantGroup = editMerchantGroup;
    vm.search = search;
    vm.search.text = "";

    load();

    function load() {
        $http.get('merchant_group').then(function (response) {
            vm.results = response.data;
        });
    }

    function createMerchantGroup() {
        $location.path('brands/merchant_groups_create');
    }

    function editMerchantGroup(merchant_group) {
        $http.get('merchant_group/set_current/'+merchant_group.id).then(function (response) {
            $location.path('brands/merchant_groups_edit');
        });
    }

    function deleteMerchantGroupDialog(merchant_group, index) {
        SweetAlert.swal({
                title: "Warning",
                text: "Are you sure you want to delete the merchant group "+merchant_group.id+" - "+merchant_group.name+"?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function (isConfirm) {
                if (isConfirm) {
                    $http.delete('merchant_group/'+merchant_group.id).then(function (response) {
                        vm.results.splice(index, 1);
                    });
                }
            });
    }

    function search() {
        vm.search.text = !vm.search.text ? "":vm.search.text;
         $http.post('merchant_group/search', {search_text: vm.search.text}).then(function (response) {
             vm.results = response.data;
        });
    }
});
