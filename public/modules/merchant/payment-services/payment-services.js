(function () {
    'use strict';

    angular
        .module('adminPortal.merchant')
        .controller('PaymentServicesCtrl', PaymentServicesCtrl);

    function PaymentServicesCtrl(PaymentService, UtilityService, Lookup, $timeout, SweetAlert) {

        var vm = this;

        vm.isValidLogo = isValidLogo;
        vm.uploadFileLicense = uploadFileLicense;
        vm.uploadFileVoidedCheck = uploadFileVoidedCheck;
        vm.updateBusinessInformation = updateBusinessInformation;
        vm.updateOwnerInformation = updateOwnerInformation;

        vm.fileError = false;
        vm.licenseFile = undefined;
        vm.voidedCheckFile = undefined;

        vm.licenseFileRequest = false;
        vm.voidedCheckFileRequest = false;
        vm.businessInformationUpdatedSuccesfully = false;
        vm.loadingBusinessInfo = false;
        vm.loadingOwnerInfo = false;
        vm.fileUploading = false;
        vm.uploadSuccess = false;
        vm.uploadFail = false;
        vm.initial_submission = false;
        vm.form_agreement_opened = false;

        vm.account_type_personal_business = [
            {
                code: 'personal',
                desc: 'Personal'
            },
            {
                code: 'business',
                desc: 'Business'
            }
        ];

        vm.account_type_checking_savings = [
            {
                code: 'checking',
                desc: 'Checking'
            },
            {
                code: 'savings',
                desc: 'Savings'
            }
        ];

        load();

        function isValidLogo(file) {
            if (!!file) {
                vm.fileError = false;
                if (vm.licenseFileRequest) {
                    vm.licenseFile = file;
                }
                if (vm.voidedCheckFileRequest) {
                    vm.voidedCheckFile = file;
                }

                vm.fileUploading = true;
                PaymentService.uploadOwnerDocumentation(vm.licenseFile, vm.voidedCheckFile).success(function (response) {
                    if (typeof response.primary_owner_void_check_url != 'undefined') {
                        vm.business_info.primary_owner_void_check_url = response.primary_owner_void_check_url;
                    }

                    if (typeof response.primary_owner_drivers_license_url != 'undefined') {
                        vm.business_info.primary_owner_drivers_license_url = response.primary_owner_drivers_license_url;
                    }

                    vm.fileUploading = false;
                    vm.uploadSuccess = true;
                    vm.licenseFile = undefined;
                    vm.voidedCheckFile = undefined;


                    $("#upload-modal").modal('hide');

                    $timeout(function () {
                        vm.uploadSuccess = false;
                    }, 3000);
                }).error(function (response, status) {
                    console.log(response);
                    vm.uploadFail = true;
                });


            }
            else {
                vm.fileError = true;
            }
        }

        function load() {
            vm.loadingBusinessInfo = true;
            vm.loadingOwnerInfo = true;
            Lookup.multipleLookup(['state']).success(function (response) {
                vm.states = response.state;
            }).error(function (response, status) {
                console.log(response);
            });

            PaymentService.getPaymentInformation().success(function (response) {
                vm.initial_submission = response.initial_submission;
                vm.business_info = UtilityService.parseStringBooleanValues(response.payment_service_owner);
                vm.owner_info = UtilityService.parseStringBooleanValues(response.payment_service_owner);
                if (!!vm.business_info.primary_owner_dob) {
                    vm.business_info.primary_owner_dob = moment(vm.business_info.primary_owner_dob, "YYYY-MM-DD").toDate();
                }
                vm.loadingBusinessInfo = false;
                vm.loadingOwnerInfo = false;
            }).error(function (response, status) {
                console.log(response);
            });
        }

        function updateBusinessInformation() {

            if (vm.business_form.$valid) {

                vm.business_info.primary_owner_ssn = vm.business_info.primary_owner_ssn.replace(/\D+/g, '');
                vm.business_info.fed_tax_id = vm.business_info.fed_tax_id.replace(/\D+/g, '');
                PaymentService.submitBusinessInformation(vm.business_info).success(function (response) {
                    vm.business_info = UtilityService.parseStringBooleanValues(response);
                    vm.initial_submission = false;
                    vm.businessInformationUpdatedSuccesfully = true;
                    vm.businessInformationUpdatedSuccesfully = true;
                    $timeout(function () {
                        vm.businessInformationUpdatedSuccesfully = false;
                        vm.business_info.successful_api_upload = true;
                    }, 3000);
                }).error(function (response, status) {
                    console.log(response);
                });
            }
        }

        function updateOwnerInformation() {
            vm.uploadFail = false;
            if (vm.business_form.$valid && vm.business_info.form_agreement) {
                PaymentService.submitOwnerInformation(vm.business_info).success(function (response) {

                    vm.business_info = UtilityService.parseStringBooleanValues(response.payment_application);

                    if (!!vm.business_info.primary_owner_dob) {
                        vm.business_info.primary_owner_dob = moment(vm.business_info.primary_owner_dob, "YYYY-MM-DD").toDate();
                    }

                    if (typeof response.smaw_response.error_messages == 'undefined') {
                        SweetAlert.swal({
                            title: "Payment Services Response",
                            text: response.smaw_response.user_message,
                            type: "info",
                            confirmButtonColor: "#0288D1",
                            confirmButtonText: "OK"
                        });
                    }
                    else {
                        SweetAlert.swal({
                            title: "Payment Services Response",
                            text: response.smaw_response.error_message,
                            type: "warning",
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "OK"
                        });
                    }


                    // vm.owner_info = UtilityService.parseStringBooleanValues(response);
                    // vm.initial_submission = false;
                    // if (!!vm.owner_info.primary_owner_dob) {
                    //     vm.owner_info.primary_owner_dob = moment(vm.owner_info.primary_owner_dob, "YYYY-MM-DD").toDate();
                    // }
                    // vm.ownerInformationUpdatedSuccesfully = true;
                    // $timeout(function () {
                    //     vm.ownerInformationUpdatedSuccesfully = false;
                    // }, 3000);

                    // if (!!vm.licenseFile || !!vm.voidedCheckFile) {
                    //
                    // }
                }).error(function (response, status) {
                    console.log(response);
                });
            }
        }

        function uploadFileLicense() {
            vm.licenseFileRequest = true;
            vm.voidedCheckFileRequest = false;
            $("#upload-modal").modal('show');
        }

        function uploadFileVoidedCheck() {
            vm.licenseFileRequest = false;
            vm.voidedCheckFileRequest = true;
            $("#upload-modal").modal('show');
        }
    }
})();

