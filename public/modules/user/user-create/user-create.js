angular.module('adminPortal.user').controller('UserCreateCtrl', UserCreateCtrl);

function UserCreateCtrl($scope, Users, $timeout, Merchant, $http, UtilityService, $location, SweetAlert) {

    var vm = this;

    vm.validation = 1;
    vm.email_exist = false;

    vm.new_user = {};
    vm.new_user.merchants = [];
    vm.new_user_processing = true;
    vm.success_new_user = "";

    vm.submit = false;

    vm.create_user_success = false;

    vm.roles = [];
    vm.user_brands = [];

    vm.visibilities = [];

    vm.selectable_merchants = [];
    vm.merchant_factory = Merchant;

    vm.merchants_filter = "";
    vm.search_text = "";

    vm.user = Users;
    vm.current_user = {};

    vm.setPassword = false;
    vm.currentRolePermissions = [];

    vm.searchMerchantByProperty = searchMerchantByProperty;
    vm.createNewUser = createNewUser;
    vm.addMerchant = addMerchant;
    vm.removeMerchant = removeMerchant;
    vm.merchantFilteredUser = merchantFilteredUser;
    vm.setUserOperatorMerchant = setUserOperatorMerchant;
    vm.setVisibility = setVisibility;
    vm.getCurrentRolePermissions = getCurrentRolePermissions;
    vm.checkEnabledRoles = checkEnabledRoles;

    function createNewUser() {
        vm.submit = true;
        vm.show_selected_merchant_error = false;
        if ((vm.new_user.visibility === 'mine_only' || vm.new_user.visibility === 'operator') && vm.new_user.merchants.length <= 0) {
            vm.show_selected_merchant_error = true;
            return;
        }
        if (vm.create_user_form.$valid) {
            vm.new_user_processing = true;
            vm.new_user.selected_brands = [];

            if (!vm.new_user.password && !vm.new_user.retype_password) {
                var text = UtilityService.getRamdomString();
                vm.new_user.password = text;
                vm.new_user.retype_password = text;
            }

            Users.createUser(vm.new_user).then(function (response) {
                if (response.data === 'success') {
                    vm.new_user_processing = false;
                    vm.create_user_success = true;
                    vm.success_new_user = vm.new_user.first_name + " " + vm.new_user.last_name;
                    vm.new_user = {};
                    vm.submit = false;
                    SweetAlert.swal({
                            title: "The user " + vm.success_new_user + " has been created!",
                            type: "success",
                            showCancelButton: false,
                            confirmButtonColor: "#488214",
                            confirmButtonText: "OK"
                        },
                        function () {
                            $location.path('users/manage');
                        });
                }
                else if (response.data === 'email_registered') {
                    vm.email_exist = true;
                }
            });
        }
    }

    Users.createLoad().then(function (response) {
        vm.brands = response.data.brands;
        vm.visibilities = response.data.visibilities;
        vm.user_brands = vm.brands;
        vm.organizations = response.data.organizations;
        vm.roles = response.data.roles;
        Users.getUserSessionInfo().then(function (response) {
            vm.current_user = response;
            if (vm.current_user.user.visibility == 'brand') {
                vm.new_user.brand = vm.current_user.user_related_data.brand_id;
            }
            vm.new_user_processing = false;
        });
    });


    function addMerchant(merchant) {
        if (!UtilityService.checkIfMerchantIsDuplicated(vm.new_user.merchants, merchant.merchant_id)) {
            vm.new_user.merchants.push(merchant);
        }
        removeMerchantSearchList(merchant.merchant_id);

        vm.new_user.merchants = UtilityService.sortArrayByPropertyAlpha(vm.new_user.merchants, 'name');
    }

    function removeMerchant(merchant) {


        if (!UtilityService.checkIfObjectWithAttributeExistsInArray(vm.result_merchants, 'merchant_id', merchant)) {
            vm.selectable_merchants.push(merchant);
        }
        vm.selectable_merchants = UtilityService.sortArrayByPropertyAlpha(vm.selectable_merchants, 'name');

        removeMerchantSelected(merchant.merchant_id);
    }

    function removeMerchantSearchList(id) {
        var i = 0;
        while (i < vm.selectable_merchants.length) {
            if (vm.selectable_merchants[i].merchant_id === id) {
                vm.selectable_merchants.splice(i, 1);
            }
            i++;
        }
    }

    function removeMerchantSelected(id) {
        var i = 0;
        while (i < vm.new_user.merchants.length) {
            if (vm.new_user.merchants[i].merchant_id === id) {
                vm.new_user.merchants.splice(i, 1);
            }
            i++;
        }
    }

    function merchantFilteredUser() {

        var merchant_filtered_roles = [4, 6, 7];

        if (merchant_filtered_roles.indexOf(vm.new_user.role) !== -1) {
            return true;
        }
        else {
            return false;
        }
    }

    function setUserOperatorMerchant(merchant) {
        vm.new_user.operated_merchant = merchant;
    }

    function setVisibility() {
        vm.new_user.visibility = UtilityService.returnOneArrayFieldWithAnotherArrayFieldValue(Users.role_visibility_mapping, 'role_id', 'visibility', vm.new_user.role);
        vm.currentRolePermissions = getCurrentRolePermissions(vm.new_user.role);
    }

    function removeSelectedMerchantsFromSearchResults() {
        vm.selectable_merchants = vm.result_merchants;
        for (var i = 0, len = vm.selectable_merchants.length; i < len; i++) {
            if (UtilityService.checkIfObjectWithAttributeExistsInArray(vm.new_user.merchants, 'merchant_id', vm.selectable_merchants['merchant_id'])) {
                vm.selectable_merchants.splice(i, 1);
            }
        }
    }

    function searchMerchantByProperty() {
        vm.show_selected_merchant_error = false;
        vm.result_merchants = [];

        if (vm.search_text.length > 0) {
            vm.initial_search = true;
            vm.merchant_search_processing = true;

            $http.get('/merchant_search_by_property', {params: {search_text: vm.search_text}}).success(function (response) {
                Merchant.search_results = response;
                vm.result_merchants = Merchant.getSearchResults();
                removeSelectedMerchantsFromSearchResults();
                vm.merchant_search_processing = false;
            });
        }
    }

    function getCurrentRolePermissions(id) {
        try {
            var role = findObjectByKey(vm.roles, 'id', id);
            if (!!role) {
                return role.permissions_table;
            }
            else {
                return [];
            }
        } catch (e) {
            console.log(e);
        }
        return [];
    }

    function checkEnabledRoles(role) {
        var current_role = findObjectByKey(vm.roles, 'name', role);
        if (!!current_role) {
            return true;
        }
        return false;
    }

    function findObjectByKey(array, key, value) {
        for (var i = 0; i < array.length; i++) {
            if (array[i][key] === value) {
                return array[i];
            }
        }
        return undefined;
    }
}
