angular.module('adminPortal.menu').factory('Menu', function ($http, Cookie, $q) {

    var service = {};

    service.processing = false;
    service.resetSelections = function () {
        service.current_menu_item = {};
        service.current_item_id = false;
        service.current_mod_item_id = false;

        service.current_mod_group = {};
        service.current_mod_item = {};

        service.current_modifier_item = {};
        service.current_menu = null;
        service.menu_types = [];
        service.modifier_groups = [];
        service.menu_type_categories = [];

        service.all_items = [];
        service.all_modifier_items = [];

        service.menu_merchant = false;
        service.quick_edit_merchant = false;

        //Used for Promo
        service.menu_types = [];
        service.unselected_menu_categories = [];
        service.selected_menu_categories = [];
        service.unselected_menu_types = [];
        service.selected_menu_types = [];
        service.menu_type_sizes = [];
        service.unselected_menu_type_sizes = [];
        service.selected_menu_type_sizes = [];
        service.menu_type_items_sizes = [];
        service.unselected_menu_type_items_sizes = [];
        service.selected_menu_type_items_sizes = [];

        service.menu_type_items = [];
        service.unselected_menu_type_items = [];
        service.selected_menu_type_items = [];

        service.search_results = [];

        service.search_text = '';
        service.quick_edit_merchant_id = '';

        service.item_modifiers_response = false;

        service.full_menu = {};

        service.promo_menu_selection_types = {};

        service.upsells = [];
        service.menuWithCardUpsells = {};
        service.processing = false;

        service.last_edit_object = {};

        service.open_menu_elements = {};
        service.open_menu_elements.sections = [];
        service.open_menu_elements.modifier_groups = [];
    };

    service.getMerchantByMenu = function (search_text) {
        return $http.post('merchant_search_by_menu', {search_text: search_text});
    };
    //Create a New menu Attribute
    service.post = function (url, post_data) {
        var post = $http.post('/menu/' + url, post_data);

        post.then(function (data) {
            return data;
        });
        return post;
    };

    service.uploadImageWithThumb = function (file, thumb) {
        return $http({
            method: 'POST',
            url: 'menu/item/image_upload_s3',
            headers: {
                'Content-Type': undefined
            },
            data: {
                file: file,
                thumb: thumb
            },
            transformRequest: function (data, headersGetter) {
                var formData = new FormData();
                angular.forEach(data, function (value, key) {
                    formData.append(key, value);
                });
                return formData;
            }
        });
    };

    //Delete a menu Attribute
    service.delete = function (url, id) {
        var destroy = $http.delete('/menu/' + url + '/' + id);

        destroy.then(function (data) {
            return data;
        });
        return destroy;
    };

    //Update a menu
    service.update = function (url, put_data) {
        var put = $http.put('/menu/' + url, put_data);

        put.then(function (data) {
            return data;
        });
        return put;
    };

    //Update a menu
    service.get = function (url) {
        var get = $http.get('/menu/' + url);

        get.then(function (data) {
            return data;
        });
        return get;
    };

    service.checkQuickEditSet = function () {
        var check_quick_edit_set = $http.get('/menu/check_quick_edit');

        check_quick_edit_set.then(function (data) {
            return data;
        });
        return check_quick_edit_set;
    };

    service.getCurrentMenuItem = function () {
        return service.current_menu_item;
    };

    service.getMenuTypes = function () {
        if (service.menu_types.length > 0) {
            return service.menu_types;
        }
        else {
            service.get('current_menu_types').then(function (response) {
                service.menu_types = response.data;
                return service.menu_types;
            });
        }
    };

    service.getModifierGroups = function () {
        var deferred = $q.defer();

        if (service.modifier_groups.length > 0) {
            deferred.resolve();
            return true;
        }
        else {
            service.get('current_modifier_groups').then(function (response) {
                service.modifier_groups = response.data;
                deferred.resolve(response);
                return true;
            });
        }
    };

    service.getCurrentModifierItem = function () {
        return service.current_modifier_item;
    };

    service.getSearchResults = function () {
        return service.search_results;
    };

    service.getSearchText = function () {
        return service.search_text;
    };

    service.getSearchTextLength = function () {
        return service.search_text.length;
    };

    service.getFullMenu = function () {
        var deferred = $q.defer();
        service.processing = true;
        this.get('item_modifiers').then(function (response) {
            service.menu_types = response.data.menu_response.menu.menu_types;
            service.modifier_groups = response.data.menu_response.menu.modifier_groups;

            for (var section_index = 0; section_index < service.menu_types.length; section_index++) {
                for (var size_index = 0; size_index < service.menu_types[section_index]['sizes'].length; size_index++) {
                    service.menu_types[section_index]['sizes'][size_index]['priority'] = parseInt(service.menu_types[section_index]['sizes'][size_index]['priority']);
                }
                if (service.open_menu_elements.sections.indexOf(service.menu_types[section_index]['menu_type_id']) != -1) {
                    service.menu_types[section_index]['opened'] = true;
                }
            }

            for (var mod_group_index = 0; mod_group_index < service.modifier_groups.length; mod_group_index++) {
                if (service.open_menu_elements.modifier_groups.indexOf(service.modifier_groups[mod_group_index]['modifier_group_id']) != -1) {
                    service.modifier_groups[mod_group_index]['opened'] = true;
                }
            }

            service.all_items = response.data.all_menu_items;
            service.all_modifier_items = response.data.all_mod_items;

            service.menu_type_categories = response.data.lookup.cat_id;
            service.processing = false;
            deferred.resolve(response);
        });
        return deferred.promise;
    };

    service.loadUpsells = function () {
        this.get('category_upsell').then(function (response) {
            service.upsells = response.data;
        });
    };

    service.loadCartUpsells = function () {
        this.get('cart_upsells').then(function (response) {
            service.menuWithCardUpsells = response.data;
        });
    };

    service.loadIfUndefined = function () {
        this.getFullMenu();
    };

    service.getPromoBogoOptions = function () {
        this.get('promo_bogo_options').then(function (response) {
            service.menu_type_sizes = response.data.menu_type_sizes;
            service.unselected_menu_type_sizes = response.data.menu_type_sizes;
            service.menu_type_items = response.data.items;
            service.menu_type_items_sizes = response.data.item_sizes;
        });
    };

    service.loadUpsells();
    //service.getFullMenu();

    service.getMenusByBrand = function (brand_id) {
        return $http.get('/menus/' + brand_id).then(function (response) {
            return response.data;
        });
    };


    service.resetSelections();

    service.getMenuList = function (search_text) {
        return $http.post('/menu_search', {
            search_text: search_text,
            order_by: 'Menu.name'
        }).then(function (response) {
            return response.data;
        });
    };

    service.getMenuListForCurrentMerchant = function (search_text) {
        return $http.get('/menus/current_merchant?search_text=' + search_text).then(function (response) {
            return response.data;
        });
    };

    service.setMenusToMerchant = function (menus) {
        return $http.post('/merchant/set_menus', {'selected_menus': menus}).then(function (data) {
            return data;
        });
    };

    service.saveOpenSectionsAndModifierGroups = function() {
        service.open_menu_elements.sections = [];
        service.open_menu_elements.modifier_groups = [];

        for (var section_index = 0; section_index < service.menu_types.length; section_index++) {
            console.log(service.menu_types[section_index]);
            if (service.menu_types[section_index]['opened']) {
                service.open_menu_elements.sections.push(service.menu_types[section_index]['menu_type_id']);
            }
        }

        for (var mod_group_index = 0; mod_group_index < service.modifier_groups.length; mod_group_index++) {
            if (service.modifier_groups[mod_group_index]['opened']) {
                service.open_menu_elements.modifier_groups.push(service.modifier_groups[mod_group_index]['modifier_group_id']);
            }
        }
    }

    return service;
});
