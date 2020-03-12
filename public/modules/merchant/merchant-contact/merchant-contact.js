angular.module('adminPortal.merchant').controller('MerchantContactCtrl', MerchantContactCtrl);

function MerchantContactCtrl(Merchant, $timeout, UtilityService, $translate, $scope) {
    var vm = this;

    vm.admin_emails = [];
    vm.admin_phones = [];

    vm.new_admin_email = {};
    vm.new_admin_email.weekly = false;
    vm.new_admin_email.admin = false;
    vm.new_admin_email.daily = false;

    vm.new_admin_phone = {};

    //View Model Functions
    vm.shortenText = shortenText;
    vm.createNewAdminEmail = createNewAdminEmail;
    vm.deleteAdmEmailDialog = deleteAdmEmailDialog;
    vm.confirmDeleteEmail = confirmDeleteEmail;
    vm.editEditAdminEmailDialog = editEditAdminEmailDialog;
    vm.updateAdminEmail = updateAdminEmail;
    vm.createNewAdminPhone = createNewAdminPhone;
    vm.deleteAdminPhoneDialog = deleteAdminPhoneDialog;
    vm.confirmDeleteAdminPhone = confirmDeleteAdminPhone;
    vm.editAdminPhoneDialog = editAdminPhoneDialog;
    vm.updateAdminPhone = updateAdminPhone;
    vm.formatPhone = formatPhone;

    //Delete Admin Email Variables
    var delete_admin_email;
    var delete_admin_email_indx;
    vm.delete_admin_email_name = null;

    //Delete Admin Phone Variables
    var delete_admin_phone;
    var delete_admin_phone_indx;
    vm.delete_admin_phone_name = null;

    //Edit Admin Email Variables
    vm.edit_admin_email = {};
    var edit_admin_email_index;

    //Edit Admin Phone Variables
    vm.edit_admin_phone = {};
    var edit_admin_phone_index;

    load();

    function load() {
        Merchant.index('contact').then(function (response) {
            vm.admin_emails = response.data.admin_emails;
            vm.admin_phones = response.data.admin_phones;
        });
    }

    function resetForm() {
        vm.new_admin_email.success = false;
        vm.new_admin_email.submit = false;
        vm.edit_admin_email.success = false;
        vm.edit_admin_email.submit = false;
        vm.new_admin_phone.submit = false;
        vm.new_admin_phone.success = false;
        vm.edit_admin_phone.success = false;
        vm.new_admin_email_error = false;
    }

    vm.new_admin_email_error = false;
    function createNewAdminEmail() {
        vm.new_admin_email.submit = true;
        if (vm.new_admin_email_form.$valid) {
            Merchant.create('contact_admin_email', vm.new_admin_email).then(function (response) {
                vm.admin_emails.push(response.data);
                $timeout(resetForm, 3500);

                vm.new_admin_email_form.email.$faded = false;
                vm.new_admin_email_form.name.$faded = false;
                //Set Admin Email Form to be New Again
                vm.new_admin_email = {};
                vm.new_admin_email.weekly = false;
                vm.new_admin_email.admin = false;
                vm.new_admin_email.daily = false;
                vm.new_admin_email.success = true;

                $("#add-admin-email-modal").modal('toggle');
                Merchant.markProgressMilestoneComplete('admin_email');
            }).catch(function () {
                vm.new_admin_email_error = true;
                $timeout(resetForm, 3500);
            });
        }
    }

    //Opens the Dialog to Delete an Email
    function deleteAdmEmailDialog(admin_email, indx, event) {
        event.stopPropagation();

        delete_admin_email = admin_email;
        delete_admin_email_indx = indx;
        vm.delete_admin_email_name = admin_email.name;

        $("#delete-adm-email-modal").modal('show');
    }

    //Confirmation of Deleting an Email
    function confirmDeleteEmail() {
        Merchant.delete('delete_admin_email', delete_admin_email.id).then(function (response) {
            vm.admin_emails.splice(delete_admin_email_indx, 1);
            $("#delete-adm-email-modal").modal('toggle');
        });
    }

    //Opens Edit Admin Email Dialog
    function editEditAdminEmailDialog(admin_email, index) {
        vm.edit_admin_email = Object.assign({}, admin_email);
        edit_admin_email_index = index;
    }

    //Updates an Admin Email
    function updateAdminEmail() {
        Merchant.update('admin_email', vm.edit_admin_email).then(function (response) {
            //Edit Send Order Variables
            vm.admin_emails[edit_admin_email_index] = vm.edit_admin_email;
            $("#edit-admin-email-modal").modal('toggle');
            vm.edit_admin_email.success = true;
            $timeout(resetForm, 3500);
        }).catch(function () {
            vm.new_admin_email_error = true;
            $timeout(resetForm, 3500);
        });
    }

    //Create New Admin Phone
    function createNewAdminPhone() {
        vm.new_admin_phone.submit = true;
        if (vm.new_admin_phone_form.$valid) {
            Merchant.create('admin_phone', vm.new_admin_phone).then(function (response) {
                vm.admin_phones.push(response.data);

                vm.new_admin_phone_form.name.$faded = false;
                vm.new_admin_phone_form.phone_no.$faded = false;
                //Set Admin Email Form to be New Again
                vm.new_admin_phone = {};
                vm.new_admin_phone.success = true;

                $("#add-admin-phone-modal").modal('toggle');

                $timeout(resetForm, 3500);
            });
        }
    }

    //Opens the Dialog to Delete an Email
    function deleteAdminPhoneDialog(admin_phone, indx, event) {
        event.stopPropagation();

        delete_admin_phone = admin_phone;
        delete_admin_phone_indx = indx;
        vm.delete_admin_phone_name = admin_phone.name;

        $("#delete-admin-phone-modal").modal('show');
    }

    function confirmDeleteAdminPhone() {
        $("#delete-admin-phone-modal").modal('hide');
        Merchant.delete('delete_admin_phone', delete_admin_phone.id).then(function (response) {
            vm.admin_phones.splice(delete_admin_phone_indx, 1);
        });
    }

    //Opens Edit Admin phone Dialog
    function editAdminPhoneDialog(admin_phone, index) {
        vm.edit_admin_phone = Object.assign({}, admin_phone);
        edit_admin_phone_index = index;
    }

    //Updates an Admin phone
    function updateAdminPhone() {
        Merchant.update('admin_phone', vm.edit_admin_phone).then(function (response) {
            //Edit Send Order Variables
            vm.admin_phones[edit_admin_phone_index] = vm.edit_admin_phone;
            $("#edit-admin-phone-modal").modal('toggle');
            vm.edit_admin_phone.success = true;
            $timeout(resetForm, 3500);
        });
    }

    function shortenText(text) {
        return UtilityService.shortenText(text);
    }

    //Updates an Admin phone
    function formatPhone(phone) {
        return UtilityService.formatPhone(phone);
    }

    $scope.$on('current_merchant:updated', function (event, data) {
        load();
    });
}
