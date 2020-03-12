/**
 * Created by boneill on 3/9/17.
 */
// Google async initializer needs global function, so we use $window
angular.module('shared').service('MerchantGroup', MerchantGroup);

function MerchantGroup($http, UtilityService) {

    var service = this;

    service.selectable_merchant_groups = [];
    service.selected_merchant_groups = [];
    var result_merchant_groups = [];

    service.search_text = "";
    service.search_url = 'merchant_group/search';

    service.selected_merchant_group = null;

    service.processing = false;
    service.initial_search = false;
    service.merchantGroupSearch = merchantGroupSearch;
    service.addGroup = addGroup;
    service.removeGroup = removeGroup;

    service.propagate_type = 'subset';

    function addGroup (group) {
        service.selected_merchant_groups.push(group);
        removeMerchantGroupSearchList(group.id);

        service.selected_merchant_groups = UtilityService.sortArrayByPropertyAlpha(service.selected_merchant_groups, 'name');
    }

    function removeGroup(group) {

        if (!UtilityService.checkIfObjectWithAttributeExistsInArray(result_merchant_groups, 'id', group)) {
            service.selectable_merchant_groups.push(group);
        }
        service.selectable_merchant_groups = UtilityService.sortArrayByPropertyAlpha(service.selectable_merchant_groups, 'name');
        removeMerchantGroupSelected(group.id);
    }

    function removeMerchantGroupSearchList(id) {
        var i = 0;
        while (i < service.selectable_merchant_groups.length) {
            if (service.selectable_merchant_groups[i].id == id) {
                service.selectable_merchant_groups.splice(i, 1);
            }
            i++;
        }
    }

    function removeMerchantGroupSelected(id) {
        var i = 0;
        while (i < service.selected_merchant_groups.length) {
            if (service.selected_merchant_groups[i].id == id) {
                service.selected_merchant_groups.splice(i, 1);
            }
            i++;
        }
    }

    function removeSelectedMerchantGroupsFromSearchResults() {
        service.selectable_merchant_groups = result_merchant_groups;
        for (var i = 0, len = service.selectable_merchant_groups.length; i < len; i++) {
            if (UtilityService.checkIfObjectWithAttributeExistsInArray(service.selected_merchant_groups, 'merchant_id', service.selectable_merchant_groups['merchant_id'])) {
                service.selectable_merchant_groups.splice(i, 1);
            }
        }
    }

    function merchantGroupSearch() {
        result_merchant_groups = [];

        // if (service.search_text.length > 0) {
            service.initial_search = true;
            service.processing = true;

            $http.post(service.search_url, {search_text: service.search_text}).success(function (response) {
                var search_results = response;
                result_merchant_groups = search_results;
                removeSelectedMerchantGroupsFromSearchResults();
                service.processing = false;
            });
        //}
    }

    return service;
}
