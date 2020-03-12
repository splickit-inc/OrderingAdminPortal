angular.module('adminPortal.webSkin').factory('WebSkin', function($http, Cookie) {

    var service = {};

    service.current_web_skin_item = {};
    service.current_modifier_item = {};
    service.current_web_skin = null;

    service.current_brand_name = null;
    service.web_skin_types = [];
    service.modifier_groups = [];
    service.search_results = [];

    //Create a New web_skin Attribute
    service.post = function(url, post_data) {
        var post = $http.post('/web_skin/'+url, post_data);

        post.then(function(data){
            return data;
        });
        return post;
    };

    //Delete a web_skin Attribute
    service.delete = function(url, id) {
        var destroy = $http.delete('/web_skin/'+url+'/'+id);

        destroy.then(function(data){
            return data;
        });
        return destroy;
    };

    //Update a web_skin
    service.update = function(url, put_data) {
        var put = $http.put('/web_skin/'+url, put_data);

        put.then(function(data){
            return data;
        });
        return put;
    };

    //Update a web_skin
    service.get = function(url) {
        var get = $http.get('/web_skin/'+url);

        get.then(function(data){
            return data;
        });
        return get;
    };

    service.getSkinsByBrand = function (brand_id) {
        return $http.get('/brand/'+brand_id+'/skins');
    };

    service.getMerchantRelatedSkins = function (brand_id, merchant_id) {
        return $http.get('/brand/'+brand_id+'/merchant/'+merchant_id+'/skins');
    };

    service.getSearchResults = function() {
        return service.search_results;
    };

    service.getCurrentBrand = function() {
        return service.current_brand_name;
    };

    service.uploadHeroFile = function (hero_file) {
        return $http({
            method: 'POST',
            url: 'web_skin/hero_image_upload',
            headers: {
                'Content-Type': undefined
            },
            data: {
                file: hero_file
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

    service.setSkinToMerchant = function (merchant_id, skins){
        return $http.post('merchant/'+merchant_id+'/skin/set',skins);
    };
    return service;
});