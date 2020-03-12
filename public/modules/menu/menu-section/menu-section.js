angular.module('adminPortal.menu').controller('MenuSectionCtrl', MenuSectionCtrl);

function MenuSectionCtrl(MenuSectionFactory, $timeout, $location, SweetAlert, $http, Menu, $scope, Upload, UtilityService) {
    var vm = this;

    vm.submit = false;

    vm.addEditSectionSize = addEditSectionSize;
    vm.updateSection = updateSection;
    vm.setDescription = setDescription;
    vm.uploadFile = uploadFile;
    vm.updateSizePrintName = updateSizePrintName;

    $scope.image_upload_stage = 'file_select';
    $scope.blockingObject = {block: true};

    Menu.getFullMenu();

    vm.page_open_sections = {};

    vm.sectionService = MenuSectionFactory;
    vm.menuService = Menu;

    function addEditSectionSize() {
        var new_section_size = {
            size_name: '',
            size_display_name: '',
            description: '',
            active: true,
            new: true,
            apply_all_items: true
        };
        vm.sectionService.data.sizes.push(new_section_size);
    }

    function updateSection() {
        vm.submit = true;
        if (vm.update_section_form.$valid) {
            Menu.post('update_menu_type', vm.sectionService.data).then(function (response) {
                var section = response.data;

                for (var size_index = 0; size_index < section.sizes.length; size_index++) {
                    section.sizes[size_index]['priority'] = parseInt(section.sizes[size_index]['priority']);
                }

                SweetAlert.swal({
                        title: "Menu section successfully updated!",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#488214",
                        confirmButtonText: "OK"
                    },
                    function () {
                        $location.path('/menu/items');
                    });

                $timeout(function () {
                    // 1 second delay, might not need this long, but it works.
                }, 500);
            });
        }
    }

    function setDescription(name) {
        if (typeof vm.sectionService.data.menu_type_description == 'undefined') {
            vm.sectionService.data.menu_type_description = name;
            return;
        }
    }

    function updateSizePrintName(size) {
        if (typeof size.size_print_name == 'undefined') {
            size.size_print_name = size.size_name;
            size.description = size.size_name;
        }
    }

    $scope.$watch('file', function () {
        if (!!$scope.file) {
            //uploadFile($scope.file[0]);
            handleFileSelect($scope.file[0]);
            $scope.image_upload_stage = 'crop';
        }
    });

    function uploadFile(file) {
        var upload = Upload.upload({
                url: '/menu/menu_type/image_upload',
                data: {
                    key: file,
                    menu_type_id: vm.sectionService.data.menu_type_id
                }
            }
        );

        $scope.image_stage = 'processing';

        upload.then(function (response) {
            $scope.image_stage = 'load';
            var d = new Date();
            var n = d.getTime();
        });
    }

    $scope.uploadFile = function () {
        $scope.blockingObject.render(function (dataURL) {
            var result_image = UtilityService.dataURItoBlob(dataURL);

            MenuSectionFactory.uploadFile(result_image).then(function(response) {
                $("#image-modal").modal('hide');

                $scope.upload_stage = 'file_select';

                console.log(response);
                vm.sectionService.data.image_url = response.data.image;
                MenuSectionFactory.setSection(vm.sectionService.data);

                SweetAlert.swal({
                    title: "Image Uploaded",
                    text: "Your cropped section image has been uploaded to the server.",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#488214",
                    confirmButtonText: "OK"
                });

            });
        });
    }

    var handleFileSelect = function (load_file) {
        var file = load_file;
        var reader = new FileReader();
        reader.onload = function (evt) {

            $scope.$apply(function ($scope) {
                $scope.myImage = evt.target.result;
            });
        };
        reader.readAsDataURL(file);
    };
    angular.element(document.querySelector('#fileInput')).on('change', handleFileSelect);

    vm.sectionService.loadSection();
}