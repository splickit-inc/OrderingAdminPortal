angular.module('adminPortal.promo').factory('Promos', function ($http) {

    var service = {};

    service.promo_types = [{
        code: 1,
        value: 'Cart Discount'
    },
    {
        code: 300,
        value: 'Delivery Discount'
    },
    {
        code: 2,
        value: 'BOGO'
    },
    {
        code: 4,
        value: 'Item Discount'
    },
    {
        code: 5,
        value: 'Bundle Discount'
    }];

    service.discount_types = [{
        code: 'percent_off',
        value: 'Percent Off'
    },
        {
            code: 'dollars_off',
            value: 'Dollars Off'
        }];

    service.discount_types_4_5 = [{
        code: 'percent_off',
        value: 'Percent Off'
    },
        {
            code: 'dollars_off',
            value: 'Dollars Off'
        },
        {
            code: 'fixed_price',
            value: 'Fixed Price'
        }];

    service.order_types = [{
        code: 'pickup',
        value: 'Pickup'
    },
        {
            code: 'delivery',
            value: 'Delivery'
        },
        {
            code: 'all',
            value: 'All'
        }];

    service.bogo_menu_levels = [{
        code: 'category',
        value: 'Category'
    },
        {
            code: 'section',
            value: 'Section'
        },
        {
            code: 'section',
            value: 'Section Size'
        },
        {
            code: 'item',
            value: 'Item'
        },
        {
            code: 'item_size',
            value: 'Item Size'
        }];

    //Create a New promos Attribute
    service.post = function (url, post_data) {
        var post = $http.post('/promos/' + url, post_data);

        post.then(function (data) {
            return data;
        });
        return post;
    }

    //Delete a promos Attribute
    service.delete = function (url, id) {
        var destroy = $http.delete('/promos/' + url + '/' + id);

        destroy.then(function (data) {
            return data;
        });
        return destroy;
    }

    //Update a promos
    service.update = function (url, put_data) {
        var put = $http.put('/promos/' + url, put_data);

        put.then(function (data) {
            return data;
        });
        return put;
    }

    //Update a promos
    service.get = function (url) {
        var get = $http.get('/promos/' + url);

        get.then(function (data) {
            return data;
        });
        return get;
    }

    service.getSearchResults = function () {
        return service.search_results;
    }

    service.getCurrentBrand = function () {
        return service.current_brand_name;
    }

    return service;
});