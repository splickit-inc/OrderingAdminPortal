angular.module('adminPortal.user').service('Users', function ($http, Cookie, $q, $location, $localStorage, $route, UtilityService, SweetAlert, $routeParams) {

    var service = {};

    service.permissions = [];
    service.visibility = 0;
    service.current_user = {};
    service.role_visibility_mapping = [
        {
            'role_id': 1,
            'visibility': 'global'
        },
        {
            'role_id': 2,
            'visibility': 'all'
        },
        {
            'role_id': 3,
            'visibility': 'mine_only'
        },
        {
            'role_id': 5,
            'visibility': 'operator'
        },
        {
            'role_id': 6,
            'visibility': 'operator'
        },
        {
            'role_id': 8,
            'visibility': 'brand'
        },
        {
            'role_id': 7,
            'visibility': 'operator'
        },
        {
            'role_id': 9,
            'visibility': 'operator'
        },
    ];

    service.user_possible_roles = [];

    service.role_permission_mapping = [{'id': 1, 'name': 'Splickit Super User', 'permission': 'create_super_user'},
        {'id': 2, 'name': 'Partner Admin', 'permission': 'create_ptnr_admin'},
        {'id': 3, 'name': 'Reseller Account Manager', 'permission': 'create_var_acct_mngr'},
        {'id': 5, 'name': 'Store Owner Operator', 'permission': 'create_owner_oper'},
        {'id': 6, 'name': 'Store Manager', 'permission': 'create_store_mngr'},
        {'id': 8, 'name': 'Brand Manager', 'permission': 'create_brand_mngr'}];

    service.session = {};
    service.user_related_data = {};

    service.getAllUsers = function () {
        var all_users = $http.get('/all_users');
        all_users.then(function (response) {
            return response;
        });
        return all_users;
    };

    service.createUser = function (new_user) {
        var create_user = $http.post('/create_user', new_user).success(function (data) {
            return data;
        });
        return create_user;
    };

    service.getRoles = function () {
        var get_roles = $http.get('/get_roles').success(function (data) {
            return data;
        });
        return get_roles;
    };

    service.getPermissions = function () {

        var deferred = $q.defer();

        if (service.permissions.length > 0) {
            deferred.resolve('');
        }
        else {
            $http.get('/user/permissions').success(function (data) {
                service.permissions = data;
                deferred.resolve(data);
            });
        }
        return deferred.promise;
    };

    service.createLoad = function () {
        var load = $http.get('/user/create_load').success(function (data) {
            return data;
        });
        return load;
    };

    service.getUserName = function () {
        var cookieData = Cookie.getCookie('user_data');
        if (!cookieData) {
            return null;
        }
        var userData = JSON.parse(cookieData);
        return userData.user.first_name;
    };

    service.getVisibility = function () {
        var cookieData = Cookie.getCookie('user_data');
        if (!cookieData) {
            return null;
        }
        var userData = JSON.parse(cookieData);
        return userData.user.visibility;
    };

    service.getOperatorMenu = function () {
        var cookieData = Cookie.getCookie('user_data');
        if (!cookieData) {
            return null;
        }
        var userData = JSON.parse(cookieData);
        return userData.menu_id;
    };

    service.visibility = service.getVisibility();

    service.removeUser = function (userId) {
        $http.post('/remove_user', {"userId": userId}).success(function (data) {
            $("#removeUserModal").modal('toggle');
        });
    };

    service.getAuthToken = function () {
        var cookieData = Cookie.getCookie('user_data');
        if (!cookieData) {
            return null;
        }
        var userData = JSON.parse(cookieData);
        var authToken = userData.token;
        if (!!authToken) {
            return authToken;
        }
        return null;
    };

    service.isLoggedIn = function () {
        var authToken = service.getAuthToken();
        return !!authToken;
    };

    service.logOn = function (logOnCriteria) {
        var log_on_attempt = $http.post('/login_attempt', logOnCriteria);
        log_on_attempt.then(function (response) {
            if (response.data.status === 1) {
                Cookie.setCookie('user_data', JSON.stringify(response.data), 1);
            }
        });
        return log_on_attempt;
    };

    service.logOut = function () {
        var log_out = $http.get('/error_clear_session');
        log_out.then(function (response) {
            Cookie.deleteCookie('user_data');
            $localStorage.$reset();
            return response;
        }).catch(function (response) {
            Cookie.deleteCookie('user_data');
            return response;
        });
        return log_out;
    };

    service.logOutOnCorruptSession = function() {
        console.log('route pa', $route.current.publicAccess);
        if (!$route.current.publicAccess) {
            service.logOut();

            if ($location.path() != '/login') {
                SweetAlert.swal({
                        title: "We’re sorry, there appears to be an issue with your current session. Please login to re-authenticate.",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#488214",
                        confirmButtonText: "OK"
                    },
                    function () {
                        window.location="/";
                    });
            }
        }

    }

    service.delete = function (user) {
        var delete_user = $http.post('/user/delete', {"id": user});
        delete_user.then(function (response) {
            return response;
        });
        return delete_user;
    };

    service.getUserSessionInfo = function () {
        var defer = $q.defer();

        $http.get('/user/session_info').then(function (response) {
            service.visibility = response.data.visibility;
            service.permissions = response.data.permissions;

            if (typeof service.permissions == 'undefined') {
                service.logOutOnCorruptSession();
            }

            if (service.permissions.length < 1) {
                service.logOutOnCorruptSession();
            }

            if (typeof response.data.operator_merchant_count != 'undefined') {
                service.operator_merchant_count = response.data.operator_merchant_count;
            }
            else {
                service.operator_merchant_count = 0;
            }

            if (typeof service.operator_merchant === 'undefined') {
                service.operator_merchant = response.data.operator_merchant;
            }
            service.current_user = response.data.user;
            service.user_related_data = response.data.user_related_data;

            defer.resolve(response.data);
            service.setPossibleRoles();
        }, function (response) {
            defer.reject(response);
        });

        return defer.promise;
    };

    service.hasPermission = function (permission) {
        return service.permissions.indexOf(permission) !== -1;
    };

    service.setPossibleRoles = function () {
        for (var i = 0, len = service.role_permission_mapping.length; i < len; i++) {
            if (service.hasPermission(service.role_permission_mapping[i]['permission'])) {
                service.user_possible_roles.push(service.role_permission_mapping[i]);
            }
        }
    };

    service.editUser = function (user) {
        var edit_user = $http.post('/user/edit', user);
        edit_user.then(function (response) {
            return response;
        });
        return edit_user;
    };

    service.logInAsUser = function (user_id) {
        return $http.get('/user/login_as/' + user_id).then(function (response) {
            Cookie.deleteCookie('user_data');
            Cookie.setCookie('user_data', JSON.stringify(response.data), 1);
            if (typeof(response.data.permissions) == 'object') {
                response.data.permissions = UtilityService.convertObjectToArray(response.data.permissions);
            }

            $localStorage.currentUser = response.data;
            window.location = '/';
        });

    };



    service.nonSessionPath = function() {
        var url_path = $location.path();
        console.log('url_path', url_path);
        if (url_path.indexOf('password') != -1) {
            return true;
        }
        else {
            return false;
        }
    }

    service.getUserSessionInfo();


    return service;
});
