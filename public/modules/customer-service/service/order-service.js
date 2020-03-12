angular.module('adminPortal.customerService').factory('Order', function($http, $q) {

    var service = {};

    service.current_order = null;

    service.search_results = [];

    //Return Basic Data on Load
    service.index = function() {
        var index = $http.get('/order');

        index.then(function(data){
            return data;
        }, function errorCallback(response) {
            window.location='/';
        });
        return index;
    };

    //Post to Order Url
    service.post = function(url, post_data) {
        var post = $http.post('/order/'+url, post_data);

        post.then(function(data){
            return data;
        });
        return post;
    };

    service.get = function(url) {
        var get = $http.get('/order/'+url);

        get.then(function(data){
            return data;
        });
        return get;
    };

    //Delete a Merchant Attribute
    service.delete = function(url, id) {
        var destroy = $http.delete('/order/'+url+'/'+id);

        destroy.then(function(data){
            return data;
        });
        return destroy;
    };

    //Update a Merchant
    service.update = function(url, put_data) {
        var put = $http.put('/order/'+url, put_data);

        put.then(function(data){
            return data;
        });
        return put;
    };

    service.refundOrder = function(refund_order) {
        var deferred = $q.defer();

        this.post('refund_order', refund_order).then(function (response) {
            deferred.resolve(response);
        });
        return deferred.promise;
    };

    service.getFullMenu = function () {
        var deferred = $q.defer();

        this.get('item_modifiers').then(function(response) {
            service.menu_types = response.data.menu_response.menu.menu_types;
            service.modifier_groups = response.data.menu_response.menu.modifier_groups;

            service.all_items = response.data.all_menu_items;
            service.all_modifier_items = response.data.all_mod_items;

            service.menu_type_categories = response.data.lookup.cat_id;
            deferred.resolve(response);
        });
    };

    service.getSearchResults = function() {
        return service.search_results;
    };

    service.setCurrentOrder = function (order_id) {
        return $http.post('/order/set_current', {order_id: order_id});
    };
    return service;
});