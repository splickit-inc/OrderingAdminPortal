angular.module('adminPortal.webSkin').controller('WebSkinCreateCtrl', WebSkinCreateCtrl);

function WebSkinCreateCtrl(WebSkin, $location, SweetAlert, UtilityService, Users) {
    var vm = this;

    vm.new_web_skin = {};
    vm.new_web_skin_template = {};
    vm.recently_visited_web_skins = [];

    vm.brand_web_skins = [];

    vm.user = Users;
    console.log(vm.user);

    vm.brand = null;
    vm.selected_brand_name = "";

    vm.createWebSkin = createWebSkin;
    vm.viewWebSkins = viewWebSkins;
    vm.viewWebSkin = viewWebSkin;
    vm.createNewOrUseTemplate = createNewOrUseTemplate;
    vm.createWebSkinWithTemplate = createWebSkinWithTemplate;
    vm.openModal = openModal;

    vm.optionCreate = true;
    vm.idCheckbox = {};

    load();

    function load() {
        WebSkin.get('load').then(function (response) {
            vm.brands = response.data.brands;
            vm.recently_visited_web_skins = response.data.recently_visited_web_skins;
            if (!vm.user.hasPermission('brands_filter')) {
                viewWebSkins(vm.user.user_related_data.brand_id);
                vm.brand = vm.user.user_related_data.brand_id;
            }
        });
    }

    function createWebSkin() {
        vm.new_web_skin.submit = true;
        if (vm.create_new_web_skin_form.$valid) {
            vm.new_web_skin.processing = true;
            vm.new_web_skin.brand = vm.brand;

            WebSkin.post('create', vm.new_web_skin).then(function (response) {
                $("#create-template-modal").modal('toggle');
                vm.new_web_skin.processing = false;

                SweetAlert.swal({
                        title: "The new " + vm.new_web_skin.name + " site version has been initiated!",
                        text: "You will now be taken to our site version configuration tool.",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#488214",
                        confirmButtonText: "Great, let's go!"
                    },
                    function () {
                        $location.path('/web_skin/configuration');
                    });
            }, function errorCallback(response) {
                vm.new_menu.error = response.statusText;
            });
        }
    }
    
    function createNewOrUseTemplate() {
        if (vm.optionCreate) {
            vm.createWebSkin();
        }
        vm.new_web_skin.submit = true;
        if (!vm.optionCreate && vm.create_new_web_skin_form.$valid) {
            vm.new_web_skin.submit = true;

            //Todo - http get skins templates
            WebSkin.get('get_default_skins').then(function (response) {
                vm.templates = response.data;
            });

            $("#create-template-modal").modal('toggle');
            $("#select-template-modal").modal('toggle');
        }
    }
    
    function createWebSkinWithTemplate() {
        $("#select-template-modal").modal('toggle');

        vm.new_web_skin_template.skin_name = vm.new_web_skin.name;
        vm.new_web_skin_template.template = vm.idCheckbox.template;
        vm.new_web_skin_template.brand = vm.brand;

        console.log(vm.new_web_skin_template.template);

        WebSkin.post('create_skin_template', vm.new_web_skin_template).then(function (response) {
            console.log(response);
            SweetAlert.swal({
                    title: "The new " + vm.new_web_skin.name + " site version has been initiated!",
                    text: "You will now be taken to our site version configuration tool.",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#488214",
                    confirmButtonText: "Great, let's go!"
                },
                function () {
                    $location.path('/web_skin/configuration');
                });
        }, function errorCallback(response) {
            vm.new_web_skin_template.error = response.statusText;
        });
    }

    function openModal() {
        vm.optionCreate = true;
        vm.idCheckbox = {};
        $("#create-template-modal").modal('toggle');
    }

    function viewWebSkins(brand) {
        WebSkin.get('view_brand_skins/' + brand).then(function (response) {
            vm.brand_web_skins = response.data;
            vm.selected_brand_name = UtilityService.returnOneArrayFieldWithAnotherArrayFieldValue(vm.brands, 'brand_id', 'brand_name', brand);
        });
    }

    function viewWebSkin(skin_id) {
        WebSkin.get('set_web_skin_id/' + skin_id).then(function (response) {
            $location.path('/web_skin/configuration');
        });
    }
}