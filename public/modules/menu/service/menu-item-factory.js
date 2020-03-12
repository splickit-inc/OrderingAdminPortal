angular.module('adminPortal.menu').factory('MenuItem', function ($http, Cookie, $q, Lookup, UtilityService) {

    var service = {};

    service.data = {};

    service.loadMenuItem = function(item_id) {

            $http.get('menu/full_item/'+item_id).then(function (response) {
                service.data = response.data;

                if (service.data.allowed_modifier_groups.length === 0) {
                    service.data.allowed_modifier_groups = {};
                }

                for (var key in service.data.allowed_modifier_groups) {
                    service.data.allowed_modifier_groups[key]['max'] = parseFloat(service.data.allowed_modifier_groups[key]['max']);
                    service.data.allowed_modifier_groups[key]['price_override'] = parseFloat(service.data.allowed_modifier_groups[key]['price_override']);
                }


                if (service.data.comes_with_modifier_items.length === 0) {
                    service.data.comes_with_modifier_items = {};
                }

                service.data.submit = false;

                for (var index = 0; index < service.data.sizes.length; index++) {
                    service.data.sizes[index]['active'] = Lookup.yesNoTrueFalseConversion(service.data.sizes[index]['active']);
                }

                for (index = 0; index < service.data.modifier_groups.length; index++) {
                    service.data.modifier_groups[index]['price_max'] = parseFloat(service.data.modifier_groups[index]['price_max']);
                    service.data.modifier_groups[index]['price_override'] = parseFloat(service.data.modifier_groups[index]['price_override']);

                    service.data.modifier_groups[index]['min'] = parseInt(service.data.modifier_groups[index]['min']);
                    service.data.modifier_groups[index]['max'] = parseInt(service.data.modifier_groups[index]['max']);
                    service.data.modifier_groups[index]['priority'] = parseInt(service.data.modifier_groups[index]['priority']);
                }

                for (index = 0; index < service.data.sizes.length; index++) {
                    service.data.sizes[index]['priority'] = parseFloat(service.data.sizes[index]['priority']);
                }


                if (typeof service.data.item_name === "undefined") {
                    service.data.item_name = "";
                }

                service.data.active = Lookup.yesNoTrueFalseConversion(service.data.active);

            });

    }

    service.updateItemObjectForAllowedModifierChanges = function() {
        var deferred = $q.defer();

        for (var index = 0; index < service.data.modifier_groups.length; index++) {
            if (typeof service.data.allowed_modifier_group_map_ids[service.data.modifier_groups[index]['modifier_group_id']] != 'undefined') {
                var modifier_group_id = service.data.modifier_groups[index]['modifier_group_id'];

                var allowed_modifier_group = service.data.allowed_modifier_groups[modifier_group_id];

                service.data.modifier_groups[index]['price_override'] = allowed_modifier_group.price_override;

                service.data.modifier_groups[index]['max'] = allowed_modifier_group.max;
            }
            if (index == (service.data.modifier_groups.length-1)) { deferred.resolve(true); }
        }
        return deferred.promise;
    }

    return service;
});
