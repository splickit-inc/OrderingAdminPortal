/**
 * Created by boneill on 3/9/17.
 */
// Google async initializer needs global function, so we use $window
angular.module('shared').service('EmbeddedMerchantSearch', EmbeddedMerchantSearch);

function EmbeddedMerchantSearch($http, UtilityService, $rootScope) {

    var service = this;

    service.selectable_merchants = [];
    service.selectable_merchant_groups = [];
    service.selected_merchants = [];
    service.selected_merchant_groups = [];
    var result_merchants = [];

    service.search_text = "";
    service.active_only = false;

    service.search_url = 'merchant_menu_search';

    service.processing = false;
    service.initial_search = false;
    service.merchantSearch = merchantSearch;
    service.addMerchant = addMerchant;
    service.removeMerchant = removeMerchant;
    service.merchantSearchGlobal = merchantSearchGlobal;
    service.merchantSearchByBrand = merchantSearchByBrand;
    service.merchantGroupSearch = merchantGroupSearch;
    service.merchantGroupLoad = merchantGroupLoad;
    service.addMerchantGroup = addMerchantGroup;
    service.removeMerchantGroup = removeMerchantGroup;
    service.getMerchantsByGroupID = getMerchantsByGroupID;
    service.addMerchantGroupAsMerchants = addMerchantGroupAsMerchants;
    service.clearSelectable = clearSelectable;
    service.clearSelected = clearSelected;
    service.clearAll = clearAll;
    service.propagate_type = 'subset';

    function clearSelectable() {
        service.selectable_merchants = [];
        service.selectable_merchant_groups = [];
    }

    function clearSelected() {
        service.selected_merchants = [];
        service.selected_merchant_groups = [];
    }

    function clearAll() {
        clearSelectable();
        clearSelected();
        service.result_merchants = [];
    }

    function addMerchant(merchant) {
        service.selectable_merchants.splice(service.selectable_merchants.indexOf(merchant), 1);
        if (!UtilityService.checkIfObjectWithAttributeExistsInArray(service.selected_merchants, 'merchant_id', merchant.merchant_id)) {
            service.selected_merchants.push(merchant);
        }
    }

    function addMerchantGroup(group) {
        service.selectable_merchant_groups.splice(service.selectable_merchant_groups.indexOf(group), 1);
        if (!UtilityService.checkIfObjectWithAttributeExistsInArray(service.selected_merchant_groups, 'id', group.id)) {
            service.selected_merchant_groups.push(group);
        }
    }

    function addMerchantGroupAsMerchants(group) {
        service.processing = true;
        getMerchantsByGroupID(group.id).success(function (response) {
            service.processing = false;
            service.selectable_merchant_groups.splice(service.selectable_merchant_groups.indexOf(group), 1);
            service.selected_merchants = service.selected_merchants.concat(response.merchants);
            $rootScope.safeApply();
        });
    }

    function removeMerchant(merchant) {
        service.selected_merchants.splice(service.selected_merchants.indexOf(merchant), 1);
        if (!UtilityService.checkIfObjectWithAttributeExistsInArray(service.selectable_merchants, 'merchant_id', merchant.merchant_id)) {
            service.selectable_merchants.push(merchant);
        }
    }

    function removeMerchantGroup(group) {
        service.selected_merchant_groups.splice(service.selected_merchant_groups.indexOf(group), 1);
        if (!UtilityService.checkIfObjectWithAttributeExistsInArray(service.selectable_merchant_groups, 'id', group.id)) {
            service.selectable_merchant_groups.push(group);
        }
    }

    function removeSelectedMerchantsFromSearchResults() {
        service.selectable_merchants = [];

        console.log(service.selected_merchants);

        for (var index = 0, len = result_merchants.length; index < len; ++index) {
            var merchant = result_merchants[index];
            var exist = UtilityService.checkIfObjectWithAttributeExistsInArray(service.selected_merchants, 'merchant_id', merchant.merchant_id);
            if (!exist) {
                service.selectable_merchants.push(merchant);
            }
        }
    }

    function merchantSearch() {
        result_merchants = [];

        service.initial_search = true;
        service.processing = true;

        $http.post(service.search_url, {search_text: service.search_text}).success(function (response) {
            result_merchants = response;
            removeSelectedMerchantsFromSearchResults();
            service.processing = false;
        });
    }

    function merchantSearchByBrand(brand_id) {
        result_merchants = [];

        service.initial_search = true;
        service.processing = true;

        $http.post(service.search_url, {
            search_text: service.search_text,
            brand_id: brand_id,
            active_only: service.active_only
        }).success(function (response) {
            result_merchants = response;
            removeSelectedMerchantsFromSearchResults();
            service.processing = false;
        });
    }

    function merchantSearchGlobal() {
        result_merchants = [];

        service.initial_search = true;
        service.processing = true;

        $http.post('/merchant_search', {search_text: service.search_text}).success(function (response) {
            result_merchants = response;
            removeSelectedMerchantsFromSearchResults();
            service.processing = false;
        }).catch(function () {
            service.processing = false;
        });

    }

    function merchantGroupLoad() {
        result_merchants = [];

        service.initial_search = true;
        service.processing = true;

        $http.get('merchant_group').then(function (response) {
            service.selectable_merchant_groups = response.data;
            service.processing = false;
        });
    }

    function merchantGroupSearch() {
        result_merchants = [];

        service.initial_search = true;
        service.processing = true;

        $http.post('merchant_group/search_all', {search_text: service.search_text}).success(function (response) {
            service.selectable_merchant_groups = response;
            service.processing = false;
        });
    }

    function getMerchantsByGroupID(group_id) {
        return $http.get('merchant_group/' + group_id + '/merchants');
    }

    return service;
}
