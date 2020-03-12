angular.module('adminPortal.webSkin').controller('WebSkinConfigurationCtrl', WebSkinConfigurationCtrl);

function WebSkinConfigurationCtrl(WebSkin, $location, SweetAlert, UtilityService, $scope, Upload, Menu) {
    var vm = this;

    vm.configs = {};
    vm.color_pick = {};
    var image_field;

    vm.processing_create_skin = false;
    vm.ready_for_production = false;
    vm.color_select_error = false;

    vm.modifyConfig = modifyConfig;
    vm.saveColor = saveColor;
    vm.iconBg = iconBg;
    vm.iconBorder = iconBorder;
    vm.pushWebskinConfirmation = pushWebskinConfirmation;
    vm.lightDarkHex = lightDarkHex;
    vm.buttonStyle = buttonStyle;
    vm.pushWebskinProductionConfirmation = pushWebskinProductionConfirmation;
    vm.hexToRgb = hexToRgb;
    vm.saveCustomCSS = saveCustomCSS;

    $scope.hero_upload_stage = 'file_select';
    $scope.logo_upload_stage = 'file_select';

    vm.image_fields = {
        primary_logo_url: {
            width: 121
        }
    };

    vm.current_image = {};

    vm.current_brand = WebSkin.getCurrentBrand();
    load();

    function load() {
        WebSkin.get('current').then(function (response) {
            vm.configs = response.data;

            for (var key in vm.configs) {
                if (!!vm.configs[key].child_off_color_fields) {
                    vm.configs[key].off_color = UtilityService.getOffColor(vm.configs[key].value);
                }
            }

        });
    }

    function hexToRgb(hex) {
        // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
        var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
        hex = hex.replace(shorthandRegex, function(m, r, g, b) {
            return r + r + g + g + b + b;
        });

        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }

    function modifyConfig(config) {
        if (!!config.color_field) {
            if (config.color_field) {
                vm.color_pick = config;
                $("#color-picker-modal").modal('toggle');
            }
            else {
                vm.current_image = vm.image_fields[config.field_name];
                image_field = config.field_name;
                $("#image-modal").modal('toggle');
            }
        }
        else {

            if (config.field_name == 'primary_logo_url') {
                vm.current_image = vm.image_fields[config.field_name];
                image_field = config.field_name;
                $("#logo-image-modal").modal('toggle');
            }
            else {
                vm.current_image = vm.image_fields[config.field_name];
                image_field = config.field_name;
                $("#image-modal").modal('toggle');
            }

        }
    }

    function saveColor() {
        if (UtilityService.validColorHex(vm.color_pick.value)) {
            vm.configs[vm.color_pick.field_name] = vm.color_pick;

            if (!!vm.configs[vm.color_pick.field_name].child_off_color_fields) {
                vm.configs[vm.color_pick.field_name].off_color = UtilityService.getOffColor(vm.configs[vm.color_pick.field_name].value);
            }

            vm.color_pick.light_dark = lightDarkHex(vm.color_pick.value);

            WebSkin.post('color_config', vm.color_pick).then(function (response) {
                $("#color-picker-modal").modal('toggle');
            });
        }
        else {
            vm.color_select_error = true;
        }

    }

    function saveCustomCSS() {
        WebSkin.post('custom_css_config', {custom_css: vm.configs.custom_css.value}).then(function (response) {
            $("#custom-css-modal").modal('hide');
        });
    }

    function pushWebskinConfirmation() {
        pushWebSkinLive();
    }

    function pushWebskinProductionConfirmation() {
        SweetAlert.swal({
                title: "Are you sure you want to make this your live web site?",
                text: "This will update the live, production version of your site that real customers see.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Push Changes"
            },
            function (isConfirm) {
                if (isConfirm) {
                    pushWebSkinProduction();
                }
            });
    }

    function pushWebSkinLive() {
        vm.processing_create_skin = true;
        WebSkin.get('push_skin_preview').then(function (response) {
            vm.processing_create_skin = false;
            SweetAlert.swal({
                    title: "Live Website Updated",
                    text: "Your freshly updated website can be viewed",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#488214",
                    confirmButtonText: "Preview Skin"
                },
                function () {
                    vm.ready_for_production = true;
                    window.open("https://" + response.data.skin.url, "_blank");
                });
        });
    }

    function pushWebSkinProduction() {
        vm.processing_create_skin = true;
        WebSkin.get('push_skin_production').then(function (response) {
            vm.processing_create_skin = false;
            SweetAlert.swal({
                    title: "Live Website Updated",
                    text: "Your freshly updated website can be viewed",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#488214",
                    confirmButtonText: "View Live Skin"
                },
                function () {
                    vm.ready_for_production = true;
                    window.open("https://" + response.data.skin.url, "_blank");
                });
        });
    }

    function iconBg(config_object) {
        if (config_object.color_field) {
            return config_object.value;
        }
        else {
            return "#666666";
        }
    }

    function iconBorder(config_object) {
        if (!!config_object) {

            if (config_object.color_field) {
                if (config_object.color_field != '') {
                    return UtilityService.colorLuminance(config_object.value, -0.2);
                }
            }
            else {
                return "#666666";
            }
        }
        else {
            return "#666666";
        }
    }

    function lightDarkHex(hex) {
        var luminance = UtilityService.measureLuminance(hex);

        if (luminance < 215) {
            return "#FFFFFF";
        }
        else {
            return "#000000";
        }
    }

    function buttonStyle(config_id) {
        var bg_color;
        var text_color;

        if (Object.keys(vm.configs).length > 10) {
            if (!!vm.configs[config_id]) {
                bg_color = UtilityService.colorLuminance(vm.configs[config_id].value, -0.12);
            }
            else {
                bg_color = vm.configs[config_id].value;
            }

            text_color = vm.lightDarkHex(vm.configs[config_id].value);

            return {
                'background-color': bg_color,
                'color': text_color
            };
        }
        else {
            return "#666666";
        }


    }

    function uploadFile(file) {
        var upload = Upload.upload({
                url: '/web_skin/logo_image_upload',
                data: {
                    key: file,
                    image_field: image_field
                }
            }
        );

        $scope.image_stage = 'processing';

        upload.then(function (response) {
            $scope.image_stage = 'load';
            var d = new Date();
            var n = d.getTime();

            vm.configs[image_field].value = response.data.image_url + "?" + n;
            $("#logo-image-modal").modal('hide');
        });
    }

    $scope.$watch('file', function () {
        if (!!$scope.file) {
            //uploadFile($scope.file[0]);
            handleFileSelect($scope.file[0]);
            $scope.hero_upload_stage = 'crop';
        }
    });

    $scope.$watch('logo_file', function () {
        if (!!$scope.logo_file) {
            //uploadFile($scope.file[0]);
            uploadFile($scope.logo_file[0]);
        }
    });

    $scope.main_image_size = {w: 500, h: 300};

    $scope.myImage = '';

    $scope.image_stage = 'load';

    $scope.blockingObject = {block: true};


    $scope.uploadFile = function () {
        $scope.blockingObject.render(function (dataURL) {
            var result_image = UtilityService.dataURItoBlob(dataURL);

            WebSkin.uploadHeroFile(result_image).then(function(response) {
                $("#image-modal").modal('hide');
                vm.configs.hero_image_url.value = response.data.image;
                $scope.hero_upload_stage = 'file_select';

                SweetAlert.swal({
                        title: "Hero Image Uploaded",
                        text: "Your cropped hero image has been uploaded to the server.",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#488214",
                        confirmButtonText: "OK"
                    });
            });
        });
    }

    $scope.blockingObject.callback = function (dataURL) {

    };

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
}
