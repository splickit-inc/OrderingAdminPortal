angular.module('adminPortal.promo').controller('LoyaltyConfigurationCtrl', LoyaltyConfigurationCtrl);

function LoyaltyConfigurationCtrl($http, Loyalty, UtilityService, SweetAlert, $timeout) {
    var vm = this;

    vm.program_setup = {};
    vm.advanced_setup = {};

    vm.brand_list = [];
    /*    vm.program_types = [
            {name: 'Remote', id: 'remote'},
            {name: 'Remote Lite', id: 'remote_lite'},
            {name: 'Splickit Points', id: 'splickit_points'},
            {name: 'Splickit Cliff', id: 'splickit_cliff'},
            {name: 'Splickit Earn', id: 'splickit_earn'},
            {name: 'Splickit Punch Card', id: 'splickit_punchcard'}
        ];*/

    vm.program_types = [
        {name: 'Splickit Cliff', id: 'splickit_cliff'},
        {name: 'Splickit Earn', id: 'splickit_earn'}
    ];

    vm.bonus_points_days = [
        {name: 'Monday', id: 'Monday'},
        {name: 'Tuesday', id: 'Tuesday'},
        {name: 'Wednesday', id: 'Wednesday'},
        {name: 'Thursday', id: 'Thursday'},
        {name: 'Friday', id: 'Friday'},
        {name: 'Saturday', id: 'Saturday'},
        {name: 'Sunday', id: 'Sunday'}
    ];
    vm.bonus_points_days_records = [];
    vm.fileError = false;
    vm.brandConfigurationLoading = false;
    vm.updateSuccess = false;

    vm.showImageModal = showImageModal;
    vm.submitInformation = submitInformation;
    vm.isValidLogo = isValidLogo;
    vm.loadBrandConfiguration = loadBrandConfiguration;
    vm.openPointDaysSelector = openPointDaysSelector;
    vm.addBonusPointsDays = addBonusPointsDays;
    vm.deleteBonusPointsDays = deleteBonusPointsDays;

    load();

    function load() {
        Loyalty.getBrands().success(function (response) {
            vm.brand_list = response;
        }).error(function (response, status) {
            console.log(response);
        });
    }

    function showImageModal() {
        vm.fileError = false;
        $("#image-modal").modal('show');
    }

    function submitInformation() {
        if (!!vm.file) {
            Loyalty.uploadLogo(vm.file, vm.brand.brand_id).success(function (response) {
                vm.logoURL = response.logo_url + '?nocache=' + Math.random();
                vm.file = undefined;
            }).error(function (response, status) {
                console.log(response);
                vm.logoURL = undefined;
                vm.file = undefined;
            });
        }

        if (vm.program_setup_form.$valid) {
            var program_setup = UtilityService.parseBooleanToStringValues(vm.program_setup);
            Loyalty.setBrandConfiguration(vm.brand.brand_id, program_setup).success(function (response) {
                vm.updateSuccess = true;
                $timeout(function () {
                    vm.updateSuccess = false;
                }, 3000);
            }).error(function (response, status) {
                console.log(response);
                SweetAlert.swal({
                    title: "Warning",
                    text: "Sorry, something went wrong. Please try again.",
                    type: "warning",
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Ok"
                });
            });
        }
        else{
            vm.program_setup_form.$submitted = true;
        }
    }

    function isValidLogo(file) {
        if (!!file) {
            $("#image-modal").modal('hide');
        }
        else {
            vm.fileError = true;
        }
    }


    function loadBrandConfiguration() {
        vm.brandConfigurationLoading = true;
        vm.fileError = false;
        vm.logoURL = undefined;
        vm.file = undefined;
        Loyalty.getBrandConfiguration(vm.brand.brand_id).success(function (response) {
            var loyalty_rule = UtilityService.parseStringBooleanValues(response.brand.loyalty_rule);
            vm.program_setup = loyalty_rule;
            vm.program_setup.rules_info = response.rules_info;
            vm.bonus_points_days_records = response.loyalty_amounts;
            if (!!response.logo_url) {
                vm.logoURL = response.logo_url + '?nocache=' + Math.random();
            }
            vm.brandConfigurationLoading = false;
        }).error(function (response, status) {
            console.log(response);
            vm.brandConfigurationLoading = false;
        });
    }

    function openPointDaysSelector() {
        vm.advanced_setup = {};
        vm.advanced_setup_form.$setPristine();
        vm.advanced_setup_form.$setUntouched();
        $("#point-days-selector").modal('show');
    }

    function addBonusPointsDays() {
        if (vm.advanced_setup_form.$valid) {
            $("#point-days-selector").modal('hide');
            Loyalty.setBonusPointsDays(vm.brand.brand_id, vm.advanced_setup).success(function (response) {
                vm.bonus_points_days_records = response.loyalty_amounts;
            }).error(function (response, status) {
                console.log(response);
            });
        }
    }

    function deleteBonusPointsDays(bonusDay) {
        SweetAlert.swal({
                title: "Warning.",
                text: "Are you sure you want to remove the bonus points day record?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Remove"
            },
            function (isConfirm) {
                if (isConfirm) {
                    Loyalty.deleteBonusPointsDays(vm.brand.brand_id, bonusDay.id).success(function (response) {
                        vm.bonus_points_days_records = response.loyalty_amounts;
                    }).error(function (response, status) {
                        console.log(response);
                    });
                }
            });
        event.stopPropagation();
    }
}
