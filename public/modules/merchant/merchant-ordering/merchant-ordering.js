(function () {

    angular
        .module('adminPortal.merchant')
        .controller('MerchantOrderingCtrl', MerchantOrderingCtrl);

    function MerchantOrderingCtrl(
        Merchant,
        $timeout,
        UtilityService,
        Lookup,
        Menu,
        WebSkin,
        $filter,
        SweetAlert,
        $scope) {

        var vm = this;

        vm.prep_time = {};
        vm.new_payment_group = {};
        vm.order_settings = {};

        vm.payment_groups = [];
        vm.call_in_history = {};
        vm.delete_payment_group = {};
        var delete_payment_group_index;

        //Payment Lookup Values
        vm.payment_types = [];
        vm.billing_entities = [];
        //All Methods Used in the UI
        vm.updatePrepTime = updatePrepTime;
        vm.createPaymentGroup = createPaymentGroup;
        vm.deletePaymentGroup = deletePaymentGroup;
        vm.updatePayments = updatePayments;
        vm.deletePaymentGroupDialog = deletePaymentGroupDialog;
        vm.createSendOrder = createSendOrder;
        vm.deleteSendOrderDialog = deleteSendOrderDialog;
        vm.confirmDeleteSendOrder = confirmDeleteSendOrder;
        vm.updateOrderReceivingDialog = updateOrderReceivingDialog;
        vm.updateOrderReceiving = updateOrderReceiving;
        vm.shortenBackend = shortenBackend;
        vm.updateOrderSettings = updateOrderSettings;
        vm.useBillingEntity = useBillingEntity;
        vm.changeSkinButton = changeSkinButton;
        vm.defaultOrderReceivingType = defaultOrderReceivingType;
        //Lead Time Form Status Variables
        vm.prep_time_submit = false;
        vm.update_prep_time_success = false;
        vm.edit_prep_time_processing = false;

        vm.payments_form_updated_success = false;

        vm.getMenuList = Menu.getMenuListForCurrentMerchant;

        //Order Receiving Stuff
        vm.send_orders = [];
        vm.new_send_order = {};
        vm.new_send_order.vivonet = {};
        vm.new_send_order.foundry = {};
        vm.new_send_order.task_retail = {};
        vm.new_send_order.brink = {};
        vm.submit = false;

        vm.clickAddSendOrder = function () {
            vm.new_send_order.delay = 0;
            vm.new_send_order.message_type = 'X';
            vm.new_send_order.info = undefined;
            vm.new_send_order.message_text = undefined;
            vm.new_send_order.message_format = undefined;
            vm.new_send_order.delivery_addr = "";
            vm.new_send_order.vivonet = {};
            vm.new_send_order.foundry = {};
            vm.new_send_order.vivonet.promo_charge_id = 0;
            vm.new_send_order_form.$setPristine();
            vm.new_send_order_form.$setUntouched();
            $("#add-send-order-modal").modal('show');
        };
        //The URL used for all controller AJAX requests
        var url = 'order_receiving';

        //Delete Send Order Variables
        var delete_send_order;
        var delete_send_order_index;

        //Edit Send Order Variables
        vm.edit_send_order = {};
        var edit_send_order_index;
        vm.send_order_update_success = false;

        vm.message_types = [];

        vm.lookup = {};

        //Menu picker
        vm.menuPicker = 'menuPicker1';
        vm.selectedMenu = [];

        vm.changeMerchantButton = function () {
            $("#" + vm.menuPicker).modal('hide');
            Menu.setMenusToMerchant(vm.selectedMenu).then(function (response) {
                load();
                Merchant.markProgressMilestoneComplete('menu');
            });
        };
        vm.loading = true;
        vm.closeModal = function () {
            load();
        };

        //skin selector
        vm.skinSelector = 'skinSelector1';
        vm.skinSelectorBody = 'Choose the site for the current merchant from the following list.';
        vm.skinSelectorTitle = 'Site Selector';
        vm.skinSelected = [];
        vm.lookupSkinFunction = WebSkin.getSkinsByBrand;
        vm.skinTableHeaders = ['Skin ID', 'Name', 'Description'];
        vm.skinTableFields = ['skin_id', 'skin_name', 'skin_description'];
        //Init controller
        load();

        function resetForm() {
            vm.prep_time.success = false;
            vm.order_settings.success = false;
        }

        function load() {
            Merchant.index('ordering').then(function (response) {
                var menu = response.data.menu;
                vm.prep_time = response.data.prep_time;
                vm.payment_types = response.data.payment_types;
                vm.billing_entities = response.data.billing_entities;
                vm.payment_groups = response.data.payment_groups;
                vm.send_orders = response.data.send_orders;

                vm.test = parseFloat(response.data.order_settings.tip_minimum_trigger_amount);

                vm.order_settings = response.data.order_settings;
                vm.order_settings.tip_minimum_trigger_amount = parseFloat(response.data.order_settings.tip_minimum_trigger_amount);

                vm.order_settings.advanced_ordering = Lookup.yesNoTrueFalseConversion(vm.order_settings.advanced_ordering);
                vm.order_settings.tip_minimum_percentage = parseInt(vm.order_settings.tip_minimum_percentage);

                vm.lookup.messages_types = response.data.messages_types;
                vm.lookup.message_formats = response.data.message_formats;
                vm.lookup.menus = menu;
                vm.initSkinSearch = response.data.brand_id;
                vm.current_merchant_id = response.data.merchant_id;

                if (!!response.data.call_in_history) {
                    vm.call_in_history = response.data.call_in_history;
                    vm.call_in_history.last_call_in_date = new Date(response.data.call_in_history.last_call_in);
                    refreshCallInHistory();
                }

                WebSkin.getMerchantRelatedSkins(response.data.brand_id, response.data.merchant_id).success(function (response) {
                    vm.lookup.skins = response;
                    vm.loading = false;
                });
                loadLeadTimeTables();
            });
        }

        vm.loadModalData = loadModalData;

        function loadModalData() {
            $("#" + vm.menuPicker).modal('show');
        }

        function updatePrepTime() {
            vm.prep_time.submit = true;
            if (vm.prep_time_form.$valid) {
                vm.prep_time.processing = true;
                Merchant.update('prep_time', vm.prep_time).then(function (response) {
                    vm.prep_time.processing = false;
                    vm.prep_time.success = true;
                    $timeout(resetForm, 3500);
                });
            }
        }

        //Create New Payment Group
        function createPaymentGroup() {
            if (vm.new_payment_group_form.$valid) {
                Merchant.create('payment_group', vm.new_payment_group).then(function (response) {
                    vm.payment_groups.push(response.data.new_payment_group);
                    $("#add-payment-group-modal").modal("hide");
                    Merchant.markProgressMilestoneComplete('payment_group');
                });
            }
        }

        //Confirmation of Deleting an Email
        function deletePaymentGroupDialog(payment_group, index) {
            vm.delete_payment_group = payment_group;
            delete_payment_group_index = index;
        }

        //Confirmation of Deleting a Payment Group
        function deletePaymentGroup() {
            Merchant.delete('payment_group', vm.delete_payment_group.id).then(function (response) {
                vm.payment_groups.splice(delete_payment_group_index, 1);
                $("#delete-payment_group-modal").modal('hide');
            });
        }

        //Update Payments
        function updatePayments() {
            Merchant.updateMerchant(vm.payment_groups, 'payments').then(function (response) {
                vm.payment_groups = response.data;
                vm.payments_form_updated_success = true;
                $timeout(resetForm, 3500);
            });
        }


        //Opens the Dialog to Delete Send Order
        function deleteSendOrderDialog(send_order, index, event) {
            event.stopPropagation();

            delete_send_order = send_order;
            vm.delete_send_order_desc = send_order.message_text;
            delete_send_order_index = index;

            $("#delete-send-order-modal").modal('show');
        }

        //Confirmation of Deleting Delivery Zone
        function confirmDeleteSendOrder() {
            Merchant.delete(url, delete_send_order.map_id).then(function (response) {
                vm.send_orders.splice(delete_send_order_index, 1);
                $("#delete-send-order-modal").modal('hide');
            });
        }

        //Opens the Dialog to edit a Delivery Zone
        function updateOrderReceivingDialog(send_order, index) {
            vm.edit_send_order = send_order;
            edit_send_order_index = index;
        }

        //Update Order Receiving
        function updateOrderReceiving() {
            if (vm.edit_send_order_form.$valid) {
                $("#edit-send-order-modal").modal('hide');
                Merchant.update('order_receiving', vm.edit_send_order).then(function (response) {
                    //Edit Send Order Variables
                    vm.send_orders[edit_send_order_index] = vm.edit_send_order;
                    Merchant.markProgressMilestoneComplete('order_receiving');
                });
            }
        }

        function updateOrderSettings() {
            vm.order_settings.submit = true;
            if (vm.order_settings_form.$valid) {
                vm.order_settings.processing = true;
                Merchant.update('order_settings', vm.order_settings).then(function (response) {
                    vm.order_settings.processing = false;
                    vm.order_settings.success = true;
                    $timeout(resetForm, 3500);
                });
            }
        }

        //Create Order Receiving
        function createSendOrder() {
            if (vm.new_send_order_form.$valid) {
                $("#add-send-order-modal").modal('hide');
                Merchant.create('order_receiving', vm.new_send_order).then(function (response) {
                    //Edit Send Order Variables
                    vm.new_send_order = {};
                    load();
                });
            }

        }

        function shortenBackend(val) {
            return UtilityService.shortenTextBackend(val);
        }

        function useBillingEntity(payment_type) {
            if (payment_type === '2000') {
                return true;
            }
            else {
                return false;
            }
        }

        function changeSkinButton() {
            $("#" + vm.skinSelector).modal('hide');
            if (vm.skinSelected.length > 0) {
                WebSkin.setSkinToMerchant(vm.current_merchant_id, vm.skinSelected).success(function (response) {
                    load();
                    Merchant.markProgressMilestoneComplete('skin_association');
                });
            }
        }

        /***
         * Lead Time functionality and Modal
         */
        vm.leadTimeModal = 'leadTimeModal1';
        vm.lead_time = {};
        vm.lead_time.current = {};
        vm.lead_time.pickup = [];
        vm.lead_time.delivery = [];
        vm.lead_time_form = {};

        function loadLeadTimeTables() {
            vm.loading = true;
            Merchant.getLeadTimes(vm.current_merchant_id).success(function (result) {
                vm.lead_time.delivery = result.delivery;
                vm.lead_time.pickup = result.pickup;
                vm.loading = false;
            });
        }

        vm.newLeadTime = function () {
            vm.lead_time.current = {};
            vm.lead_time.current.start_time_object = moment('00:00', "HH:mm").toDate();
            vm.lead_time.current.end_time_object = moment('12:00', "HH:mm").toDate();
            vm.lead_time.headerTitle = 'Create new lead time';
            vm.lead_time_form.$setUntouched();
            vm.lead_time_form.$setPristine();
            $("#" + vm.leadTimeModal).modal('show');
        };

        vm.submitLeadModal = function () {
            vm.lead_time.error = undefined;
            if (vm.lead_time_form.$valid) {
                $("#" + vm.leadTimeModal).modal('hide');
                vm.lead_time.current.merchant_id = vm.current_merchant_id;
                vm.lead_time.current.start_time = $filter('date')(vm.lead_time.current.start_time_object, 'HH:mm:ss');
                vm.lead_time.current.end_time = $filter('date')(vm.lead_time.current.end_time_object, 'HH:mm:ss');

                Merchant.setLeadTimes(vm.current_merchant_id, vm.lead_time.current).success(function (result) {
                    setTimeout(function () {
                        loadLeadTimeTables();
                    }, 200);
                }).catch(function (error) {
                    vm.lead_time.error = error.data.error;
                });
            }
        };

        vm.editLeadTime = function (item) {
            vm.lead_time.current = item;
            vm.lead_time.current.start_time_object = moment(vm.lead_time.current.start_time, "HH:mm:ss").toDate();
            vm.lead_time.current.end_time_object = moment(vm.lead_time.current.end_time, "HH:mm:ss").toDate();
            vm.lead_time.headerTitle = 'Edit lead time';
        };

        vm.deleteLeadTime = function (item) {
            SweetAlert.swal({
                    title: "Warning",
                    text: "Are you sure you want to delete the record?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Delete"
                },
                function (isConfirm) {
                    if (isConfirm) {
                        Merchant.deleteLeadTime(vm.current_merchant_id, item.id).success(function (response) {
                            loadLeadTimeTables();
                        });
                    }
                });
        };

        function defaultOrderReceivingType() {
            var custom_message_formats = ['V', 'A','AJ','B', 'BO', 'U'];
            if (custom_message_formats.indexOf(vm.new_send_order.message_format) > -1) {
                return false;
            }
            else {
                return true;
            }
        }

        var timer;

        function refreshCallInHistory() {
            timer = $timeout(function () {
                Merchant.getCallInHistory().then(function (response) {
                    vm.call_in_history = response.data;
                    vm.call_in_history.last_call_in_date = new Date(response.data.last_call_in);
                    refreshCallInHistory();
                });
            }, 60000);
        }

        $scope.$on("$destroy", function () {
            if (!!timer) {
                $timeout.cancel(timer);
            }
        });
    }
})();
