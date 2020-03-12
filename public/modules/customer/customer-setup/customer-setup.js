angular.module('adminPortal.customer').controller('CustomerSetupCtrl', CustomerSetupCtrl);

function CustomerSetupCtrl($scope, $window, $routeParams, SweetAlert, Leads, Prospects, UtilityService) {
    var vm = this;
    vm.initData = initData;

    vm.customerInfo = {};
    vm.form = {};
    vm.processing = true;
    vm.validGuid = false;
    vm.guid = null;
    vm.menuFiles = [];
    vm.imagesPreview = [];
    vm.pdfsPreview = [];
    vm.maxSizeBytes = 5242880;
    vm.validTypes = ['application/pdf', 'image/jpg', 'image/jpeg',
        'image/png', 'image/bmp'];

    vm.create = create;
    vm.imagesChanged = imagesChanged;
    vm.openFilesDialog = openFilesDialog;
    vm.clearImages = clearImages;
    vm.removeImage = removeImage;
    vm.sizesAreValid = sizesAreValid;
    vm.fileSizeIsValid = fileSizeIsValid;
    vm.typesAreValid = typesAreValid;
    vm.fileTypeIsValid = fileTypeIsValid;
    vm.fileTitle = fileTitle;

    initData();

    ///// Methods
    function initData() {
        vm.guid = $routeParams.guid;
        document.title = 'Customer Setup';
        validateGuid();
    }

    function validateGuid() {
        Leads.getLead(vm.guid).then(function (response) {
            vm.processing = false;
            if (!response.data.first_form_completion) {
                showError("This link is invalid. The customer wasn't registered properly, please fill form 1 first.");
                return;
            }
            if (!!response.data.second_form_completion) {
                showError("This link is no longer valid. The customer was already set up.");
                return;
            }
            vm.validGuid = true;
        }).catch(function () {
            vm.processing = false;
            showError("Invalid customer id. Please verify you are accessing the link that was" +
                " provided in the email.");
        });
    }

    function openFilesDialog() {
        $('#menu_files').click();
    }

    function imagesChanged() {
        parseSelectedFiles(vm.customerInfo.menu_files);
        vm.imagesPreview = [];
        vm.pdfsPreview = [];
        for (var i = 0; i < vm.menuFiles.length; i++) {
            var file = vm.menuFiles[i];
            if (file.type == 'application/pdf') {
                vm.pdfsPreview.push({
                    file: file,
                    thumbnail: "",
                    mainIndex: i
                });
                continue;
            }
            if (!fileTypeIsValid(file)) {
                vm.imagesPreview.push({
                    url: 'https://d38o1hjtj2mzwt.cloudfront.net/admin_portal/merchant_form_2/img/invalid_file.png',
                    file: file,
                    mainIndex: i
                });
                continue;
            }
            var reader = new FileReader();
            reader.onload = (function (theFile, mainIndex) {
                return function (e) {
                    $scope.$apply(function () {
                        vm.imagesPreview.push({
                            url: e.target.result,
                            file: theFile,
                            mainIndex: mainIndex
                        });
                    });
                };
            })(file, i);
            reader.readAsDataURL(file);
        }
        clearFileInput();
    }

    function parseSelectedFiles(files) {
        for (var i = 0; i < files.length; i++) {
            vm.menuFiles.push(files[i]);
        }
    }

    function clearFileInput() {
        document.getElementById('menu_files').value = null;
        vm.customerInfo.menu_files = null;
    }

    function clearImages() {
        clearFileInput();
        vm.menuFiles = [];
        vm.imagesPreview = [];
        vm.pdfsPreview = [];
    }

    function removeImage(mainIndex, index, type) {
        vm.menuFiles.splice(mainIndex, 1);
        updateMainIndexes(mainIndex);
        if (type == 'image') {
            vm.imagesPreview.splice(index, 1);
        }
        if (type == 'pdf') {
            vm.pdfsPreview.splice(index, 1);
        }
    }

    function updateMainIndexes(mainIndex) {
        for (var i = 0; i < vm.imagesPreview.length; i++) {
            var ind = vm.imagesPreview[i].mainIndex;
            if (ind > mainIndex) {
                vm.imagesPreview[i].mainIndex = ind - 1;
            }
        }
        for (var j = 0; j < vm.pdfsPreview.length; j++) {
            var jInd = vm.pdfsPreview[j].mainIndex;
            if (jInd > mainIndex) {
                vm.pdfsPreview[j].mainIndex = jInd - 1;
            }
        }
    }

    function sizesAreValid() {
        for (var i = 0; i < vm.menuFiles.length; i++) {
            if (!fileSizeIsValid(vm.menuFiles[i])) {
                return false;
            }
        }
        return true;
    }

    function fileSizeIsValid(file) {
        return file.size <= vm.maxSizeBytes
    }

    function typesAreValid() {
        for (var i = 0; i < vm.menuFiles.length; i++) {
            if (!fileTypeIsValid(vm.menuFiles[i])) {
                return false;
            }
        }
        return true;
    }

    function fileTypeIsValid(file) {
        return vm.validTypes.indexOf(file.type) != -1;
    }

    function fileTitle(file) {
        return fileSizeIsValid(file) ?
            (fileTypeIsValid(file) ? file.name :
                'File is not in a valid format.')
            : 'File is too big.';
    }


    function filesAreValid() {
        return !!vm.menuFiles.length && vm.menuFiles.length <= 5 &&
            sizesAreValid() && typesAreValid();
    }

    function create() {
        vm.customerInfo.submit = true;
        if (!vm.form.$valid || !filesAreValid()) {
            showError('Please properly complete the form before submitting.');
            return;
        }
        vm.processing = true;
        Prospects.uploadMenuFiles(vm.guid, vm.menuFiles)
            .then(function (response) {
                vm.customerInfo.menu_file_urls = response.data;
                return Prospects.setupCustomer(vm.guid, vm.customerInfo);
            })
            .then(function (response) {
                vm.processing = false;
                SweetAlert.swal({
                        title: "Success!",
                        html: "Thank you for setting up your information! The Splickit" +
                        " Team has been notified, we will reach you out soon.",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#488214",
                        confirmButtonText: "OK"
                    },
                    function () {
                        $window.location.href = 'http://splickit.com/';
                    });
            })
            .catch(function (response) {
                vm.processing = false;
                var errors = "An unexpected error occurred! Please try again later.";
                if (response.status == 422 || response.status == 400) {
                    errors = UtilityService.formatErrors(response.data.errors);
                }
                showError(errors);
            });
    }

    function showError(errorMsg) {
        SweetAlert.swal({
            title: "Error",
            html: errorMsg,
            type: "error",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "OK"
        });
    }
}