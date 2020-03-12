angular.module('adminPortal.customer').controller('CustomerCreateCtrl', CustomerCreateCtrl);

function CustomerCreateCtrl($scope, $location, SweetAlert, Lookup, Leads, UtilityService) {
    var vm = this;
    vm.initData = initData;

    vm.newLead = {}; // patch offer id until they realize how to do it
    vm.createForm = {};
    vm.processing = false;
    vm.states = [];
    vm.countries = [];
    vm.time_zones = [];
    vm.services = [];
    vm.payments = [];

    vm.create = create;

    initData();

    ///// Methods
    function initData() {
        loadLookups();
        loadServiceTypes();
        loadPaymentTypes();
    }

    function loadLookups() {
        Lookup.multipleLookup(['state', 'country', 'time_zone']).then(function (response) {
            vm.states = response.data.state;
            vm.countries = response.data.country;
            vm.time_zones = response.data.time_zone;
        });
    }

    function loadServiceTypes() {
        Leads.serviceTypes().then(function (response) {
            vm.services = response.data;
        });
    }

    function loadPaymentTypes() {
        Leads.paymentTypes().then(function (response) {
            vm.payments = response.data;
        });
    }

    $scope.$watch('vm.newLead.offer', function (current, original) {
        if (!!current) {
            vm.newLead.offer_id = current.originalObject.id;
            return;
        }
        vm.newLead.offer_id = null;
    }, true);

    function create() {
        vm.newLead.submit = true;
        window.createForm = vm.createForm;
        if (!vm.createForm.$valid) {
            showError('Please fix the errors and properly complete the form before submitting!');
            return;
        }
        vm.processing = true;
        Leads.createLead(vm.newLead).then(function (response) {
            vm.processing = false;
            SweetAlert.swal({
                    title: "Success!",
                    html: "The customer of store: <b>" + vm.newLead.store_name +
                    "</b> with contact: <b>" + vm.newLead.contact_name + "</b> has been created!",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#488214",
                    confirmButtonText: "OK"
                },
                function () {
                    $location.path('/');
                });

        }).catch(function (response) {
            vm.processing = false;
            var errors = "An unexpected error occurred! Please try again later.";
            if (response.status == 422) {
                errors = UtilityService.formatErrors(response.data.errors);
            }
            showError(errors);
        });
    }

    function showError(errorMsg) {
        SweetAlert.swal({
            title: "Error",
            type: "error",
            html: errorMsg,
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "OK"
        });
    }
}