(function () {
    'use strict';

    angular
        .module('adminPortal.merchant')
        .controller('MerchantInfoCtrl', MerchantInfoCtrl);

    function MerchantInfoCtrl(Merchant, Lookup, UtilityService, $timeout, RouteChangeCheck, $scope, Users) {

        var vm = this;

        //View Objects
        vm.location = {};
        vm.config = {};
        vm.configurations = {};
        vm.configurations.processing = false;
        vm.store = {};
        vm.message = {};

        //View Lookup Values
        vm.states = [];
        vm.countries = [];
        vm.time_zones = [];
        vm.inactive_reasons = [];
        vm.user = {};
        vm.business_info = {};

        //Forms that we want to check to make sure aren't altered when navigating away
        var forms = [
            {
                name: 'config_form',
                desc: 'Configurations'
            },
            {
                name: 'location_form',
                desc: 'Location'
            },
            {
                name: 'message_form',
                desc: 'Custom Messages'
            }
        ];

        vm.updateLocation = updateLocation;
        vm.updateConfig = updateConfig;
        vm.updateMessages = updateMessages;
        vm.charactersRemaining = charactersRemaining;
        vm.businessUpdateInfo = businessUpdateInfo;
        vm.businessUpdateBanking = businessUpdateBanking;

        loadMerchant();

        //This creates a check for the user when navigating away to other pages
        RouteChangeCheck.checkFormOnStateChange($scope, forms, 'info');

        function loadMerchant() {
            set_loaders();
            Users.getUserSessionInfo().then(function (result) {
                vm.user = result;
            }).catch(function (error) {
                console.log(error);
            });

            Merchant.index('general_info').then(function (response) {
                //Lookup Values
                var lookup = response.data.lookup;

                vm.states = lookup.state;
                vm.countries = lookup.country;
                vm.time_zones = lookup.time_zone;
                vm.inactive_reasons = lookup.inactive_reason;

                //Merchant Data
                var merchant = response.data.merchant;

                vm.location.business_name = merchant.name;
                vm.location.display_name = merchant.display_name;
                vm.location.merchant_external_id = merchant.merchant_external_id;
                vm.location.numeric_id = merchant.numeric_id;
                vm.location.alphanumeric_id = merchant.alphanumeric_id;
                vm.location.address1 = merchant.address1;
                vm.location.address2 = merchant.address2;
                vm.location.city = merchant.city;
                vm.location.state = merchant.state;
                vm.location.zip = merchant.zip;
                vm.location.country = merchant.country;

                vm.location.shop_email = merchant.shop_email;
                vm.location.phone_no = merchant.phone_no;
                vm.location.fax_no = merchant.fax_no;

                var time_zone_index = UtilityService.findIndexByKeyValue(vm.time_zones, 'type_id_value', merchant.time_zone);
                vm.location.time_zone = vm.time_zones[time_zone_index];

                //Configurations Data
                vm.config.delivery = Lookup.yesNoTrueFalseConversion(merchant.delivery);
                vm.config.immediate_message_delivery = Lookup.yesNoTrueFalseConversion(merchant.immediate_message_delivery);
                vm.config.active = Lookup.yesNoTrueFalseConversion(merchant.active);

                vm.config.inactive_reason = merchant.inactive_reason;
                vm.config.ordering_on = Lookup.yesNoTrueFalseConversion(merchant.ordering_on);
                vm.config.show_tip = Lookup.yesNoTrueFalseConversion(merchant.show_tip);
                vm.config.lead_time = merchant.lead_time;

                //Messages Data
                vm.message.custom_order_message = merchant.custom_order_message;
                vm.message.custom_menu_message = merchant.custom_menu_message;

                vm.business_info = merchant.business_info;

                if (!!vm.business_info && !!vm.business_info.EIN_SS && vm.business_info.EIN_SS.length >= 4) {
                    var str = new Array(vm.business_info.EIN_SS.length - 4).join('*');
                    vm.business_info.EIN_SS_TEMP_VIEW = str + vm.business_info.EIN_SS.substr(vm.business_info.EIN_SS.length - 4);
                } else {
                    if (!vm.business_info) {
                        vm.business_info = {};
                    }
                    vm.business_info.EIN_SS_TEMP_VIEW = '00-00000000';
                }

                vm.business_banking = merchant.business_banking;
                if (!!vm.business_banking) {
                    //vm.business_banking.routing = parseInt(vm.business_banking.routing);
                    vm.business_banking.routing = vm.business_banking.routing;
                }

                if (!!vm.business_banking && !!vm.business_banking.account && vm.business_banking.account.length >= 4) {
                    var str2 = new Array(vm.business_banking.account.length - 4).join('*');
                    vm.business_banking.account_TEMP_VIEW = str2 + vm.business_banking.account.substr(vm.business_banking.account.length - 4);
                } else {
                    if (!vm.business_banking) {
                        vm.business_banking = {};
                    }
                    vm.business_banking.account_TEMP_VIEW = '000 000 000';
                }
                clear_loaders();
            });
        }

        //Form Submit For Editing the Location
        function updateLocation() {
            vm.location.submit = true;

            if (vm.location_form.$valid) {
                vm.processing_location = true;
                Merchant.update('location', vm.location).then(function (response) {
                    clear_loaders();
                    vm.location.success = true;
                    $timeout(clear_messages, 3500);
                    Merchant.percent_complete = 80;
                });
            }
        }

        function updateConfig() {
            vm.configurations.submit = true;

            if (vm.config_form.$valid) {
                vm.processing_configuration = true;
                Merchant.update('config', vm.config).success(function (result) {
                    clear_loaders();
                    vm.configurations.success = true;
                    $timeout(clear_messages, 3500);
                }).catch(function (error) {
                    console.log(error);
                    loadMerchant();
                });
            }
        }

        function updateMessages() {
            vm.message.submit = true;

            if (vm.message_form.$valid) {
                vm.processing_messages = true;
                Merchant.update('messages', vm.message).then(function (response) {
                    clear_loaders();
                    vm.message.success = true;
                    $timeout(clear_messages, 3500);
                });
            }
        }

        function charactersRemaining(field, max) {
            return UtilityService.charactersRemaining(field, max);
        }

        function businessUpdateInfo() {
            vm.business_banking_error = false;
            if (vm.businessInfoForm.$valid) {
                vm.processing_business_info = true;
                if (!!vm.business_info.EIN_SS_TEMP && vm.business_info.EIN_SS_TEMP.length > 0) {
                    vm.business_info.EIN_SS = vm.business_info.EIN_SS_TEMP;
                }
                Merchant.update('business_info', vm.business_info).success(function (result) {
                    vm.business_info_success = true;
                    clear_loaders();
                    $timeout(clear_messages, 2000);
                }).catch(function (error) {
                    clear_loaders();
                    console.log(error.data);
                    loadMerchant();
                });
            }
        }

        function businessUpdateBanking() {
            vm.business_banking_error = false;
            if (vm.businessBankingForm.$valid && !vm.businessInfoForm.$valid) {
                vm.business_banking_error = true;
                clear_loaders();
                return;
            }

            if (vm.businessBankingForm.$valid && vm.businessInfoForm.$valid) {
                vm.business_banking.acct_address = vm.business_info.address;
                vm.business_banking.acct_city = vm.business_info.city;
                vm.business_banking.acct_st = vm.business_info.state;
                vm.business_banking.acct_zip = vm.business_info.zip;
                if (!!vm.business_banking.account_TEMP) {
                    vm.business_banking.account = vm.business_banking.account_TEMP;
                }
                vm.processing_business_banking = true;
                Merchant.update('business_banking', vm.business_banking).success(function (result) {
                    vm.business_banking_success = true;
                    clear_loaders();
                    $timeout(clear_messages, 2000);
                }).catch(function (error) {
                    clear_loaders();
                    console.log(error.data);
                    loadMerchant();
                });
            }
        }

        $scope.$on('current_merchant:updated', function (event, data) {
            loadMerchant();
        });

        function clear_messages() {
            vm.business_info_success = false;
            vm.business_banking_success = false;
            vm.location.success = false;
            vm.configurations.success = false;
            vm.message.success = false;
        }

        function clear_loaders() {
            vm.processing_business_info = false;
            vm.processing_business_banking = false;
            vm.processing_configuration = false;
            vm.processing_location = false;
            vm.processing_messages = false;
        }

        function set_loaders() {
            vm.processing_business_info = true;
            vm.processing_business_banking = true;
            vm.processing_configuration = true;
            vm.processing_location = true;
            vm.processing_messages = true;
        }
    }
})();
