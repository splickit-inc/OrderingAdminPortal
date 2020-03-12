angular.module('adminPortal.menu').controller('MenuItemCtrl', MenuItemCtrl);

function MenuItemCtrl(Menu, UtilityService, SweetAlert, $location, Upload, $scope, Lookup, EmbeddedMerchantSearch, MerchantGroup, $http, $route, $timeout, $rootScope) {
    var vm = this;

    vm.current_item = {};
    vm.menu_types = [];
    vm.modifier_groups = {};

    vm.new = false;

    vm.modifier_group_only_allowed = true;

    vm.page_open_sections = {
        sizes: false,
        modifier_groups: false,
        modifier_comes_with: false
    };

    //Methods
    vm.updateItem = updateItem;
    vm.changeAllowed = changeAllowed;
    vm.seeIfComesWith = seeIfComesWith;
    vm.backToItemsModifiers = backToItemsModifiers;
    vm.selectMerchantsToPropagate = selectMerchantsToPropagate;
    vm.setMerchants = setMerchants;
    vm.modGroupsFilter = modGroupsFilter;
    vm.setPrintNameIfBlank = setPrintNameIfBlank;

    $scope.img_preview = {};

    vm.mainCroppedImage = undefined;

    load();
    Menu.saveOpenSectionsAndModifierGroups();

    EmbeddedMerchantSearch.propagate_type = 'subset';
    EmbeddedMerchantSearch.selectable_merchants = [];
    EmbeddedMerchantSearch.selected_merchants = [];

    MerchantGroup.selectable_merchant_groups = [];
    MerchantGroup.selected_merchant_group = null;
    MerchantGroup.search_url = 'merchant_group/search_all';

    function load() {
        $http.get('/current_menu').success(function (response) {
            Menu.current_menu = response;
            loadCurrentItem();
        });

    }

    function loadCurrentItem() {
        Menu.get('current_item').then(function (response) {
            vm.current_item = response.data;
            Menu.last_edit_object.type = 'menu_item';
            Menu.last_edit_object.id = vm.current_item.item_id;

            if (vm.current_item.allowed_modifier_groups.length === 0) {
                vm.current_item.allowed_modifier_groups = {};
            }

            if (vm.current_item.comes_with_modifier_items.length === 0) {
                vm.current_item.comes_with_modifier_items = {};
            }
            vm.menu_types = Menu.getMenuTypes();

            vm.current_item.submit = false;


            for (var index = 0; index < vm.current_item.sizes.length; index++) {
                vm.current_item.sizes[index]['active'] = Lookup.yesNoTrueFalseConversion(vm.current_item.sizes[index]['active']);
            }

            for (index = 0; index < vm.current_item.modifier_groups.length; index++) {
                vm.current_item.modifier_groups[index]['price_max'] = parseFloat(vm.current_item.modifier_groups[index]['price_max']);
                vm.current_item.modifier_groups[index]['price_override'] = parseFloat(vm.current_item.modifier_groups[index]['price_override']);

                vm.current_item.modifier_groups[index]['min'] = parseInt(vm.current_item.modifier_groups[index]['min']);
                vm.current_item.modifier_groups[index]['max'] = parseInt(vm.current_item.modifier_groups[index]['max']);
                vm.current_item.modifier_groups[index]['priority'] = parseInt(vm.current_item.modifier_groups[index]['priority']);
            }

            for (index = 0; index < vm.current_item.sizes.length; index++) {
                //vm.current_item.sizes[index]['price'] = parseFloat(vm.current_item.sizes[index]['price']);
                vm.current_item.sizes[index]['priority'] = parseFloat(vm.current_item.sizes[index]['priority']);
            }

            if (typeof vm.current_item.item_name === "undefined") {
                vm.current_item.item_name = "";
            }

            if (Menu.current_item_index === 'new') {
                vm.new = true;
                vm.current_item.new = true;
                vm.current_item.active = false;
            }
            else {
                vm.current_item.active = Lookup.yesNoTrueFalseConversion(vm.current_item.active);
                vm.current_item.new = false;
            }
            $scope.image_stage = 'load';
        });
    }

    function selectMerchantsToPropagate() {
        if (Menu.current_menu.version === '3.00') {
            $('#merchant-select-modal').modal('toggle');
        }
        else {
            updateItem();
        }
    }

    function updateItem() {
        vm.current_item.processing = true;

        $http.post('/menu/item', vm.current_item).then(function (response) {
            var message_title;

            if (vm.new) {
                var new_item = response.data;

                message_title = 'The menu item "' + vm.current_item.item_name + '" has been created!';
                Menu.menu_types[Menu.current_item_section_index]['menu_items'].push(new_item);
                Menu.all_items.push(new_item);
            }
            else {
                message_title = 'The menu item "' + vm.current_item.item_name + '" has been updated!';
                Menu.menu_types[Menu.current_item_section_index]['menu_items'][Menu.current_item_index] = response.data;

                var all_items_index = UtilityService.findIndexByKeyValue(Menu.all_items, 'item_id', vm.current_item.item_id);
                Menu.all_items[all_items_index] = response.data;
            }

            vm.current_item.processing = false;

            SweetAlert.swal({
                    title: message_title,
                    text: "",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#488214",
                    confirmButtonText: "Great"
                },
                function () {
                    $location.path('/menu/items').replace();
                });
        }).catch(function(response) {
            console.log(response);
            SweetAlert.swal({
                    title: "Warning",
                    text: "The Menu API Service responded with the message: "+response.data.error.error_message,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "OK"
                })
        });
    }

    function changeAllowed(modifier_group, index) {
        if (modifier_group.allowed) {
            var allowed_mod_group = UtilityService.returnObjectOfArrayWithFieldValue(Menu.modifier_groups, 'modifier_group_id', modifier_group.modifier_group_id);

            var i;
            for (i = 0; i < allowed_mod_group.modifier_items.length; i++) {
                allowed_mod_group.modifier_items[i]['comes_with'] = false;
            }
            if (typeof modifier_group.price_max !== 'undefined') {
                vm.current_item.modifier_groups[index]['price_max'] = null;
                vm.current_item.modifier_groups[index]['price_max'] = '88888';
                vm.current_item.modifier_groups[index]['price_override'] = null;
                vm.current_item.modifier_groups[index]['price_override'] = '0';
                vm.current_item.modifier_groups[index]['display_name'] = '';
            }
            vm.current_item.allowed_modifier_groups[modifier_group.modifier_group_id] = allowed_mod_group;
        }
        else {
            delete vm.current_item.allowed_modifier_groups[modifier_group.modifier_group_id];
            //var allowed_mod_group = UtilityService.returnObjectOfArrayWithFieldValue(Menu.modifier_groups, 'modifier_group_id', modifier_group.modifier_group_id);
            //console.log(allowed_mod_group);

            // var i;
            // for (i = 0; i < allowed_mod_group.modifier_items.length; i++) {
            //     allowed_mod_group.modifier_items[i]['comes_with'] = false;
            // }
        }
    }

    function setMerchants() {
        vm.current_item.propagate_type = EmbeddedMerchantSearch.propagate_type;

        if (EmbeddedMerchantSearch.propagate_type === 'group') {
            vm.current_item.merchant_group_id = MerchantGroup.selected_merchant_group.id;
        }

        vm.current_item.propagate_merchants = EmbeddedMerchantSearch.selected_merchants;
        updateItem();
    }

    function seeIfComesWith(modifier_items) {
        var i;
        var no_comes_with_exist = true;

        for (i = 0; i < modifier_items.length; i++) {
            if (modifier_items[i].comes_with) {
                no_comes_with_exist = false;
            }
        }
        return no_comes_with_exist;
    }


    function backToItemsModifiers() {
        $location.path('/menu/items');
    }

    $scope.$watch('file', function () {
        if (!!$scope.file  && $scope.image_stage === 'load') {
            handleFileSelect($scope.file[0]);
            $scope.image_stage = 'main';
        }
    });

    $scope.file_thumb = undefined;
    $scope.$watch('file_thumb', function () {
        if (!!$scope.file_thumb && $scope.image_stage === 'thumb_load') {
            handleFileSelectThumb($scope.file_thumb[0]);
            $scope.image_stage = 'thumb';
        }
    });

    $scope.main_image_size = {w: 500, h: 300};


    $scope.image_stage = 'load';

    $scope.blockingObject = {block: true};
    $scope.blockingObject2 = {block: true};

    $scope.moveToThumbCrop = function () {
        $scope.blockingObject.render(function (dataURL) {
            $("#image-crop-contain").fadeOut(100, function () {
                $("#image-crop-contain").fadeIn(300, function () {
                    vm.MainImage = UtilityService.dataURItoBlob($scope.myCroppedMainImage);
                    handleFileSelectThumb($scope.file[0]);
                    $scope.image_stage = 'thumb';
                });
            });
        });
    };

    $scope.uploadThumb = function () {
        $scope.file_thumb = undefined;
        $scope.image_stage = 'thumb_load';
    };
    $scope.storeCrops = function () {
        $scope.blockingObject2.render(function (dataURL) {
            $("#image-crop-contain").fadeOut(100, function () {
                $("#image-crop-contain").fadeIn(300, function () {
                    vm.ThumbImage = UtilityService.dataURItoBlob($scope.myCroppedThumbImage);
                    $scope.img_preview.main = $scope.myCroppedMainImage;
                    $scope.img_preview.thumb = $scope.myCroppedThumbImage;
                    $rootScope.safeApply(function () {
                        $scope.image_stage = 'preview_confirm_s3';
                    });
                    $scope.image_stage = 'preview_confirm_s3';
                });
            });
        });
    };

    $scope.confirmCropsS3 = function () {
        $scope.image_stage = 'processing';
        if (!!vm.MainImage && !!vm.ThumbImage) {
            Menu.uploadImageWithThumb(vm.MainImage, vm.ThumbImage).then(function (response) {
                $("#image-modal").modal('hide');
                $scope.image_stage = 'load';

                SweetAlert.swal({
                        title: "Your image was successfully uploaded.",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#488214",
                        confirmButtonText: "OK"
                    },
                    function () {
                        vm.current_item.image = response.data;
                    });

                $timeout(function () {
                    // 1 second delay, might not need this long, but it works.
                    $route.reload();
                }, 500);
            }).catch(function (response, status) {
                console.log(response);
            });
        }
    };

    if (Menu.menu_types.length < 1) {
        Menu.getFullMenu();
    }

    function modGroupsFilter(modifier_group) {
        /*        if (modifier_group.allowed !== 0) {
                    console.log(modifier_group);
                }*/
        if (!vm.modifier_group_only_allowed) {
            return true;
        }
        else {
            if (modifier_group.allowed) {
                return true;
            }
            else {
                return false;
            }
        }
    }

    function setPrintNameIfBlank(item_name) {
        if (typeof vm.current_item.item_print_name == 'undefined') {
            vm.current_item.item_print_name = item_name;
            return;
        }
        if (vm.current_item.item_print_name == '' || vm.current_item.item_print_name == null) {
            vm.current_item.item_print_name = item_name;
        }
    }

    $scope.blockingObject.callback = function (dataURL) {

    };

    $scope.cancelImageUpload = function () {
        $('#image-modal').modal('hide');
        $timeout(function () {
            // 1 second delay, might not need this long, but it works.
            $route.reload();
        }, 500);
    };

    var handleFileSelect = function (load_file) {
        var file = load_file;
        if (!!file) {
            var reader = new FileReader();
            reader.onload = function (evt) {
                $scope.$apply(function ($scope) {
                    $scope.myImage = evt.target.result;
                });
            };
            reader.readAsDataURL(file);
        }
    };
    angular.element(document.querySelector('#fileInput')).on('change', handleFileSelect);

    var handleFileSelectThumb = function (load_file) {
        var file = load_file;
        if (!!file) {
            var reader = new FileReader();
            reader.onload = function (evt) {
                $scope.$apply(function ($scope) {
                    $scope.myImageThumb = evt.target.result;
                });
            };
            reader.readAsDataURL(file);
        }
    };
    angular.element(document.querySelector('#fileInputThumb')).on('change', handleFileSelectThumb);
}
