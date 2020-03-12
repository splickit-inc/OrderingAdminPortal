angular.module('adminPortal.customerService').controller('CustomerServiceUserCtrl', CustomerServiceUserCtrl);

function CustomerServiceUserCtrl($http, UtilityService, $location, $timeout, Users, SweetAlert) {
    var vm = this;

    vm.search = {};
    vm.search.text = "";


    vm.result_users = [];

    vm.recently_visited_users = [];

    vm.current_user = {};
    vm.blacklist_reason = '';

    vm.edit_user = {};
    vm.delivery_address = {};
    vm.order_history = {};
    vm.refund_history = {};
    vm.brand_loyalty = {};
    vm.brand_loyalty_details = {};
    vm.brand_loyalty_history = {};

    vm.delivery_address.loading = true;
    vm.order_history.loading = true;
    vm.refund_history.loading = true;
    vm.brand_loyalty.loading = true;
    vm.brand_loyalty_details.loading = true;
    vm.brand_loyalty_history.loading = true;

    vm.result_pages = [];
    vm.total_search_result_count = 0;

    vm.searchusers = searchusers;
    vm.formatphone = formatphone;
    vm.openEditUser = openEditUser;
    vm.updateUser = updateUser;
    vm.getDeliveryAddresses = getDeliveryAddresses;
    vm.getOrderHistory = getOrderHistory;
    vm.getRefundHistory = getRefundHistory;
    vm.getBrandLoyaltyHistory = getBrandLoyaltyHistory;
    vm.getLoyaltyDetails = getLoyaltyDetails;
    vm.getBrandLoyalty = getBrandLoyalty;
    vm.formatPhone = formatPhone;
    vm.viewOrder = viewOrder;
    vm.setPrimaryLoyaltyAccount = setPrimaryLoyaltyAccount;
    vm.openBlacklistUser = openBlacklistUser;
    vm.blacklistUser = blacklistUser;
    vm.openDeleteUserPopup = openDeleteUserPopup;
    vm.deleteUser = deleteUser;
    vm.showBlackListUserReason = showBlackListUserReason;

    vm.current_bottom_panel = "";

    vm.user_service = Users;

    load();

    function load() {
        Users.getUserSessionInfo().then(function (current_user) {

            if (!Users.hasPermission('brands_filter')) {
                vm.loyalty.brand_id = current_user.user_related_data.brand_id;
                return;
            }
        }).catch(function () {
            console.log('unable to get the current user');
        });

        $http.get('/current_user').success(function (response) {
            vm.current_user = response;
            vm.getBrandLoyalty();
        });
    }

    function resetForm() {
        vm.edit_user = {};
        vm.edit_user_form.$setPristine();


    }

    function searchusers() {
        vm.search.submit = true;
        vm.search.result_users = true;
        vm.result_merchants = [];

        if (vm.search_form.$valid) {
            vm.search.processing = true;
            vm.search.initial_search = true;

            $http.post('/user_search', {search_text: vm.search.text}).success(function (response) {
                vm.result_users = response.user_records;

                vm.total_search_result_count = response.full_record_count;

                vm.search.processing = false;
                vm.search.submit = false;
            });
        }
    }

    // function createTotalPagesArray(count) {
    //     var full_pages = Math.floor(count/20);
    //
    //     var page_remainder = count%20;
    //
    //     if (page_remainder > 0) {
    //         full_pages++;
    //     }
    //
    //     var current_page_counter = 1;
    //     while (full_pages > 0) {
    //         vm.result_pages.push(current_page_counter);
    //
    //         current_page_counter++;
    //         full_pages--;
    //     }
    // }

    function formatphone(phone_no) {
        return UtilityService.formatphone(phone_no);
    }

    function openEditUser() {
        vm.edit_user = vm.current_user;
    }

    function updateUser() {
        vm.edit_user.submit = true;

        if (vm.edit_user_form.$valid) {
            vm.edit_user.processing = true;

            $http.post('/customer_service/edit_user', vm.edit_user).success(function (response) {
                $("#edit-user-modal").modal('toggle');
                vm.edit_user.processing = false;
                vm.edit_user.submit = false;
                vm.edit_user.success = true;
                $timeout(resetForm, 3500);
            });
        }
    }

    function getDeliveryAddresses() {
        vm.current_bottom_panel = 'delivery_addresses';


        if (vm.delivery_address.loading) {
            $http.get('customer_service/user_delivery_locations').success(function (response) {
                vm.delivery_address.loading = false;
                vm.delivery_address.addresses = response;
            });
        }
    }

    function getOrderHistory() {
        vm.current_bottom_panel = 'order_history';

        if (vm.order_history.loading) {
            $http.get('customer_service/user_order_history').success(function (response) {
                vm.order_history.loading = false;
                vm.order_history.orders = response;
            });
        }
    }

    function getRefundHistory() {
        vm.current_bottom_panel = 'refund_history';

        if (vm.refund_history.loading) {
            $http.get('customer_service/refund_history').success(function (response) {
                vm.refund_history.loading = false;
                vm.refund_history.refunds = response;
            });
        }
    }

    function getBrandLoyaltyHistory() {
        vm.current_bottom_panel = 'brand_loyalty_history';

        if (vm.brand_loyalty_history.loading) {
            $http.get('customer_service/user_brand_loyalty_history').success(function (response) {
                vm.brand_loyalty_history.loading = false;
                vm.brand_loyalty_history.histories = response;
            });
        }
    }

    function getLoyaltyDetails(item) {
        if (!item.details) {
            item.details = {};
            item.details.show = true;
            item.details.loading = true;
            $http.post('customer_service/user_brand_loyalty_details', {brand_id: item.brand_id}).success(function (response) {
                item.details.loading = false;
                item.details.data = response;
            });
            return;
        }
        item.details.show = !item.details.show;
    }

    function getBrandLoyalty() {
        vm.current_bottom_panel = 'brand_loyalty';

        if (vm.brand_loyalty.loading) {
            $http.get('customer_service/user_brand_loyalty').success(function (response) {
                vm.brand_loyalty.loading = false;
                vm.brand_loyalty.details = response;
            });
        }
    }

    function viewOrder(order) {
        $http.post('/order/set_current', {order_id: order.order_id}).success(function (response) {
            $location.path('/customer_service/order');
        });
    }

    function formatPhone(phone_no) {
        return UtilityService.formatPhone(phone_no);
    }

    vm.user_loyalty_programs = [];
    vm.loyalty = {};
    vm.adjustLoyalty = function () {

        if (Users.hasPermission('brands_filter')) {
            var programs = [];
            vm.brand_loyalty.details.forEach(function (elem) {
                if(!( elem.brand_id in programs )){
                    programs.push({brand_id: elem.brand_id, brand_name: elem.brand.brand_name});
                }
            });
            vm.user_loyalty_programs = programs;
            vm.loyalty.brand_id = programs[0].brand_id;
        }
    };

    vm.adjustLoyaltySubmit = function () {
        if(vm.adjust_loyalty_form.$valid)
        {
            $http.post('customer_service/adjustLoyalty', {brand_id: vm.loyalty.brand_id, points: vm.loyalty.points, note: vm.loyalty.notes}).success(function (response) {
                vm.reset();
                $('#adjust-loyalty-modal').modal('hide');
                vm.brand_loyalty.loading = true;
                vm.getBrandLoyalty();
            }).catch(function (error) {
                console.log(error.data);
                vm.loyalty.error = error.data;
            });
        }
    };

    function setPrimaryLoyaltyAccount() {
        $http.get('user/primary_account').success(function (response) {
            if (response.result == 'sucess') {
                vm.current_user.primary_loyalty_default = true;
                SweetAlert.swal({
                        title: 'The Primary Loyalty Account Has Been Updated.',
                        text: "",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#488214",
                        confirmButtonText: "Great"
                    });
            }
            else {
                SweetAlert.swal({
                        title: "Update Error",
                        text: "There was an error in attempting to update the Primary Loyalty to this User account. Please Contact Splickit to address this issue further.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "OK"
                    });
            }
        });
    }

    function openBlacklistUser() {
        $("#blacklist-user-modal").modal('toggle');
    }
    function openDeleteUserPopup() {
        $("#delete-user-modal").modal('toggle');
    }

    function blacklistUser() {
        if(vm.blacklist_user_form.$valid) {
            $http.post('customer_service/user_blacklist', {blacklist_note: vm.blacklist_reason}).success(function (response) {
                $("#blacklist-user-modal").modal('hide');

                var message = 'The user account '+ vm.current_user.user_id + ' has been blacklisted.';

                if (response.other_blacklisted_accounts.length > 0) {
                    var grammatical_user_ids = UtilityService.convertArrayToGrammarList(response.other_blacklisted_accounts);
                    message = message+' The user accounts '+grammatical_user_ids+' that share the same contact no as '+vm.current_user.user_id+' were also blacklisted.';
                }

                SweetAlert.swal({
                    title: 'User Blacklisted',
                    text: message,
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#488214",
                    confirmButtonText: "OK"
                });
            }).catch(function (error) {
                console.log(error.data);
                vm.loyalty.error = error.data;
            });
        }
    }
    function deleteUser() {

        $http.post('customer_service/user_delete', {delete: true}).success(function (response) {
            $("#delete-user-modal").modal('hide');
            var message;

            if (response.status == 'success') {
                message = 'The user account ' + vm.current_user.user_id + ' has been deleted.';
                SweetAlert.swal({
                    title: 'User Deleted',
                    text: message,
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#488214",
                    confirmButtonText: "OK"
                },function () {
                    $location.path('customer_service/users');
                });
            }else{
                SweetAlert.swal({
                    title: "Delete Error",
                    text: "There was an error in attempting to delete the user. Please Contact Order140 to address this issue further.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "OK"
                });

            }

        }).catch(function (error) {
            console.log(error.data);

        });
    }

    function showBlackListUserReason() {
        SweetAlert.swal({
            title: "User Blacklisted!",
            text: "Blacklist Reason: "+vm.current_user.custom_message,
            type: "warning",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "OK"
        });
    }

    vm.reset = function () {
        vm.loyalty = {};
        vm.adjust_loyalty_form.$setUntouched();
        vm.adjust_loyalty_form.$setPristine();
    };
}
