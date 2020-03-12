/**
 * Created by boneill on 12/15/16.
 */
angular.module('adminPortal').controller('SideNavController', function ($scope, $location, $http, $localStorage, Users, Merchant, Menu, Order) {
    $scope.location = 'home';
    $scope.current_merchant = null;
    $scope.menu_factory = Menu;
    $scope.order_factory = Order;
    $scope.user_info = Users;

    $scope.active = {};
    $scope.active.merchant = true;

    Users.getUserSessionInfo().then(function () {
        $scope.user_info = Users;
    });

    $scope.$on('current_merchant:updated', function (event, data) {
        $scope.current_merchant = data;
    });

    $scope.parent_nav = {
        merchant: {
            open: false
        },
        menu: {
            open: false
        },
        customer_service: {
            open: false
        },
        reports: {
            open: false
        },
        brand: {
            open: false
        },
        marketing: {
            open: false
        }
    };

    //On Load Adjust Menu Accordingly

    if ($location.path().includes("merchant") && !$location.path().includes("merchants")) {
        $scope.parent_nav['merchant'].open = true;
    }

    function showSectionHideOthers(nav_section) {
        $.each($scope.parent_nav, function (k, v) {
            if (k === nav_section) {
                $scope.parent_nav[nav_section].open = true;
            }
            else {
                $scope.parent_nav[k].open = false;
            }
        });
    }

    $scope.$watch(function () {
        return Merchant.current_merchant;
    }, function (new_value, old_value) {

        if (!Users.hasPermission('op_merch_select')) {
            if (new_value !== old_value) {
                showSectionHideOthers('merchant');
            }
        }

    });

    $scope.$watch(function () {
        return Menu.current_menu;
    }, function (new_value, old_value) {
        if (!!new_value && new_value !== old_value) {
            showSectionHideOthers('menu');
        }
    });

    $scope.parentLink = function (url) {
        $location.path('/' + url);
    };

    $scope.user_name = Users.getUserName();

    $scope.closeSections = function () {
        $.each($scope.parent_nav, function (k, v) {
            $scope.parent_nav[k].open = false;
        });
    };

    $scope.set_hover = function (name) {
        $scope.closeSections();
        $scope.set_hover_submenu(name);
    };

    $scope.set_hover_submenu = function (name) {
        $scope.active = {};
        $scope.active[name] = true;
    };

    $scope.merchantQuickEditMenu = function () {
        if (!!$localStorage.currentMerchantSelected) {
            var merchant = $localStorage.currentMerchantSelected;
            Merchant.setCurrentMerchant(merchant.merchant_id).then(function (response) {
                $location.path('/merchant/' + merchant.merchant_id + '/medit');
            });
        }
    };

    $scope.menuQuickEdit = function () {
        if (!!$localStorage.currentMenuSelected) {
            var menu = $localStorage.currentMenuSelected;
            return '#/menu/' + menu.menu_id + '/medit';
        }
        else {
            return '#/menu/edit';
        }
    };

    $scope.open_brand = function () {
        $scope.open('brand');
    };

    $scope.open = function (nav) {
        var currently_open = $scope.parent_nav[nav].open;
        $scope.set_hover(nav);
        showSectionHideOthers(name);
        if (nav === 'merchant') {
            if (!!$scope.current_merchant && !currently_open) {
                showSectionHideOthers(nav);
            }
            else if (!!$scope.current_merchant && currently_open) {
                $scope.parent_nav[nav].open = false;
            }
        }
        else if (nav === 'menu') {
            if (!$scope.menu_factory) {
                return;
            }
            if (!!$scope.menu_factory.current_menu && !currently_open) {
                showSectionHideOthers(nav);
            }
            else if (!!$scope.menu_factory.current_menu && currently_open) {
                $scope.parent_nav[nav].open = false;
            }
        }
        else if (nav === 'customer_service') {
            if (!currently_open) {
                showSectionHideOthers(nav);
            }
            else {
                $scope.parent_nav[nav].open = false;
            }
        }
        if (nav === 'reports' || nav === 'brand') {
            if (!currently_open) {
                showSectionHideOthers(nav);
                return;
            }
            $scope.parent_nav[nav].open = false;
        }

        if (nav === 'marketing') {
            if (!currently_open) {
                showSectionHideOthers(nav);
                return;
            }
            $scope.parent_nav[nav].open = false;
        }
    };

    $scope.merchantNavDisplay = function () {
        if (!!$scope.current_merchant) {
            var text = $scope.current_merchant.name.substring(0, 14) + ' ' + $scope.current_merchant.merchant_id;
            return text;
        }
        else {
            return "";
        }
    };

    $scope.orderDisplay = function () {
        if (!!$scope.order_factory.current_order && !!$scope.order_factory.current_order.name) {
            var text = $scope.order_factory.current_order.order_id + ' - ' + $scope.order_factory.current_order.name.substring(0, 14);
            return text;
        }
        else {
            return "";
        }
    };

    $scope.showOrder = function () {
        if (!!$scope.order_factory.current_order) {
            return true;
        }
        else {
            return false;
        }
    };
    $scope.init = function () {
        if ($location.path() === '/merchants' || $location.path().includes('merchant')) {
            $scope.set_hover('merchant');
        }
        if ($location.path().includes('customer_service')) {
            $scope.set_hover('customer_service');
            showSectionHideOthers('customer_service');
        }
        if ($location.path() === '/menus' || $location.path().includes('menu')) {
            $scope.set_hover('menu');
        }
        if ($location.path() === '/promos' || $location.path().includes('promo')) {
            $scope.set_hover('marketing');
        }
        if ($location.path() === '/web_skin/create' || $location.path().includes('web_skin')) {
            $scope.set_hover('sites');
        }
        if ($location.path().includes('/reports')) {
            $scope.set_hover('reports_nav');
        }
        if ($location.path() === '/operator/home') {
            $scope.set_hover('home');
        }
        if ($location.path() === '/menu/edit') {
            $scope.set_hover('menu');
        }
        if ($location.path() === '/operator/order_management') {
            $scope.set_hover('order_management');
        }
        if ($location.path() === '/brands' ||
            $location.path() === '/brands/' ||
            $location.path() === '/brands/merchant_groups' ||
            $location.path().includes('/brand')) {
            $scope.set_hover('brand');
        }
    };
    $scope.init();

    function load() {
        if ($location.path() === '/merchants') {
            $scope.active = {};
            $scope.active['merchant'] = true;
        }

        if ($location.path().includes('/reports')) {
            showSectionHideOthers('reports_nav');
            $scope.active = {};
            $scope.active['reports_nav'] = true;
            $scope.open('reports');
        }

        if ($location.path() === '/merchant/operator_ordering_on_off') {
            $scope.closeSections();
            $scope.active = {};
            $scope.active['ordering_on_off'] = true;
        }

        if ($location.path().includes('promos')) {
            $scope.closeSections();
            $scope.active = {};
            $scope.active['marketing'] = true;
            $scope.open('marketing');
        }
    }

    $scope.$on('$locationChangeSuccess', function () {
        if (!$location.path().includes('menu')) {
            $scope.menu_factory.resetSelections();
        }
        load();
    });

    load();
});
