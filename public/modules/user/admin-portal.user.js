angular.module('adminPortal.user', ['ui.bootstrap', 'ngRoute', 'ngAnimate']);

angular.module('adminPortal.user').config(function ($routeProvider) {

    $routeProvider.when('/login', {
        templateUrl: 'modules/user/user-login/user-login.html',
        controller: "UserLoginCtrl",
        sideBar: 'none',
        navBar: 'none',
        publicAccess: true
    });
    $routeProvider.when('/users/manage', {
        templateUrl: 'modules/user/users-manage/users-manage.html',
        controller: 'UsersManageCtrl',
        sideBar: 'user'
    });
    $routeProvider.when('/user/create', {
        templateUrl: 'modules/user/user-create/user-create.html',
        controller: 'UserCreateCtrl',
        controllerAs: 'uc',
        sideBar: 'user'
    });

    $routeProvider.when('/user/reset', {
        templateUrl: 'modules/user/user-reset/user-reset.html',
        controller: 'UserResetCtrl',
        controllerAs: 'vm',
        sideBar: 'none',
        navBar: 'none',
        publicAccess: true
    });

    $routeProvider.when('/password/:method/:token', {
        templateUrl: 'modules/user/user-reset-form/user-reset-form.html',
        controller: 'UserResetFormCtrl',
        controllerAs: 'vm',
        sideBar: 'none',
        navBar: 'none',
        publicAccess: true
    });
    /* Add New Routes Above */
});

