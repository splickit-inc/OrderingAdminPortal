angular.module('adminPortal.valueAddedReseller').factory('Var', function($http, Cookie) {

    var service = {};

    service.all_resellers = [];

    service.get = function(path) {
        var get_request  = $http.get('/value_added_resellers/'+path);
        get_request.then(function(response){
            return response;
        });
        return get_request;
    }
    service.post = function(path, data) {
        var post_request  = $http.post('/value_added_resellers/'+path, data);
        post_request.then(function(response){
            return response;
        });
        return post_request;
    }

    service.getAllVars = function() {
        var vars = service.get('all');
        return vars;
    }

    service.create = function(data) {
        var new_var_response = service.post('create', data);
        return new_var_response;
    }

    service.delete = function(id) {
        var delete_var = service.get('delete/'+id);
        return delete_var;
    }

    return service;
});