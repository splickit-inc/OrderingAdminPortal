angular.module('adminPortal.merchant').factory('Merchant', function ($http, $rootScope) {

    var service = {};

    service.progress_milestones = [];
    service.search_results = [];

    service.all_merchants = [];

    service.current_merchant = null;

    service.percent_complete = 0;
    service.merchantPaginator = null;
    //Return Basic Data on Load
    service.index = function (url) {
        var index = $http.get('/merchant/' + url);

        index.then(function (data) {
            return data;
        }, function errorCallback(response) {
            window.location = '/';
        });
        return index;
    };

    //Create a New Merchant Attribute
    service.create = function (url, post_data) {
        var post = $http.post('/merchant/' + url, post_data);

        post.then(function (data) {
            return data;
        });
        return post;
    };

    //Delete a Merchant Attribute
    service.delete = function (url, id) {
        var destroy = $http.delete('/merchant/' + url + '/' + id);

        destroy.then(function (data) {
            return data;
        });
        return destroy;
    };

    //Update a Merchant
    service.update = function (url, put_data) {
        var put = $http.put('/merchant/' + url, put_data);

        put.then(function (data) {
            return data;
        });
        return put;
    };

    //Update a Merchant
    service.get = function (url) {
        var get = $http.get('/merchant/' + url);

        get.then(function (data) {
            return data;
        });
        return get;
    };

    //Update a Merchant
    service.updateMerchant = function (post_data, url) {
        var update = $http.post('/merchant/update/' + url, post_data);

        update.then(function (data) {
            return data;
        });
        return update;
    };

    //Merchant Delete
    service.deleteMerchant = function (post_data, url) {
        var destroy = $http.post('/merchant/delete/' + url, post_data);

        destroy.then(function (data) {
            return data;
        });
        return destroy;
    };

    //Merchant Create
    service.createMerchant = function (post_data, url) {
        var create = $http.post('/merchant/create/' + url, post_data);

        create.then(function (data) {
            return data;
        });
        return create;
    };

    service.getProgressComplete = function () {
        var get = $http.get('/merchant/progress_complete');

        get.then(function (response) {
            service.progress_milestones = response.data;
            service.calculatePercentComplete();
        });
        return get;
    };

    service.calculatePercentComplete = function () {
        var total_percentage = 0;
        var completed_percentage = 0;

        for (var key in service.progress_milestones) {
            total_percentage = total_percentage + parseInt(service.progress_milestones[key].progress_percentage);

            if (service.progress_milestones[key].complete) {
                completed_percentage = completed_percentage + parseInt(service.progress_milestones[key].progress_percentage);
            }

            if (isNaN(parseFloat(total_percentage))) {
                var e = false;
            }
        }
        service.percent_complete = Math.round((completed_percentage / total_percentage) * 100);
        $rootScope.$broadcast('percent_complete:updated', service.percent_complete);
    };

    //This Method is called by controllers when a user completes a milestone
    service.markProgressMilestoneComplete = function (progress_milestone) {
        service.progress_milestones[progress_milestone].complete = true;
        //Update the Progress Bar with after new complete status
        service.calculatePercentComplete();
    };

    service.getSearchResults = function () {
        return service.search_results;
    };

    service.getAllMerchants = function () {
        var all_merchants = service.get('all');
        all_merchants.then(function (response) {
            service.all_merchants = response.data;
        });
        return all_merchants;
    };

    service.post = function (url, post_data) {
        var post = $http.post('/merchant/' + url, post_data);

        post.then(function (data) {
            return data;
        });
        return post;
    };

    service.setCurrentMerchant = function (merchantId) {
        var resp = $http.post('/merchant/set_current', {merchant_id: merchantId})
            .then(function (response) {
                service.current_merchant = response.data;
            })
            .then(service.getProgressComplete)
            .then(function () {
                $rootScope.$broadcast('current_merchant:updated', service.current_merchant);
                return service.current_merchant;
            });
        return resp;
    };

    service.getCurrentMerchant = function () {
        return $http.get('/merchant/get_current').success(function (data) {
            $rootScope.$broadcast('current_merchant:updated', data);
        });
    };


    service.getLeadTimes = function (merchant_id) {
        return $http.get('/merchant/' + merchant_id + '/lead_times');
    };

    service.setLeadTimes = function (merchant_id, leadTime) {
        return $http.post('/merchant/' + merchant_id + '/lead_times', leadTime);
    };

    service.deleteLeadTime = function (merchant_id, leadTime) {
        return $http.delete('/merchant/' + merchant_id + '/lead_times/' + leadTime);
    };

    service.addMidDayHour = function (data) {
        return $http.post('/merchant/hours/mid_day_hours', data);
    };

    service.deleteMidHayHour = function (id) {
        return $http.delete('/merchant/hours/mid_day_hours/' + id);
    };

    service.getCallInHistory = function () {
        return $http.get('/merchant/ordering/call_in_history');
    };

    return service;
});
