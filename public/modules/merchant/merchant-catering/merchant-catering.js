angular.module('adminPortal.merchant').controller('MerchantCateringCtrl', MerchantCateringCtrl);

function MerchantCateringCtrl(UtilityService, MerchantCatering, $rootScope) {
    var vm = this;

    vm.config_processing = false;
    vm.message_processing = false;
    vm.payment_types_processing = false;
    vm.order_dest_processing = false;

    vm.catering_info = {};
    vm.payment_types = [
        'Cash Or Credit Card',
        'Credit Card Only'
    ];
    vm.destination_format = [
        {id: 'E', name: 'Email'},
        {id: 'F', name: 'Fax'}
    ];

    vm.updateConfiguration = updateConfiguration;
    vm.charactersRemaining = charactersRemaining;

    onInit();

    function onInit() {
        MerchantCatering.getCateringConfiguration().then(function (response) {
            vm.catering_info = response.data.catering_info;
            checkDefaultVariables();
        }).catch(function (response) {
            console.log(response);
        });
    }

    function updateConfiguration(panel_name) {
        if (vm.configuration_form.$valid) {
            checkDefaultVariables();
            MerchantCatering.saveCateringConfiguration(vm.catering_info).then(function (response) {
                setSuccess(panel_name);
                vm.catering_info = response.data;
            }).catch(function (response) {
                console.log(response);
            });
        }
        else {
            vm.configuration_form.$setSubmitted();
        }
    }

    function charactersRemaining(field, max) {
        return UtilityService.charactersRemaining(field, max);
    }

    function checkDefaultVariables() {
        if (!vm.catering_info.minimum_tip_percent) {
            vm.catering_info.minimum_tip_percent = 0;
        }
        if (!vm.catering_info.min_lead_time_in_hours_from_open_time) {
            vm.catering_info.min_lead_time_in_hours_from_open_time = 0;
        }
    }

    function setSuccess(panel_name) {
        vm[panel_name] = true;
        setTimeout(function () {
            vm[panel_name] = false;
            $rootScope.safeApply();
        }, 3000);
    }
}
