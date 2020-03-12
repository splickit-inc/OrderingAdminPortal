angular.module('shared').factory('BrandLookup',function(Lookup, UtilityService) {

    var brandLookup = {};


    brandLookup.addLookup = addLookup;
    brandLookup.removeLookup = removeLookup;
    brandLookup.setLookupValues = setLookupValues;
    brandLookup.setSelectionsNew = setSelectionsNew;

    function setSelectionsNew() {
        brandLookup.selected = [];
        brandLookup.un_selected = [];
        brandLookup.name = '';

        brandLookup.all_selections = [];

        brandLookup.all_selections['order_del_type'] = {
            un_selected: [],
            selected: [],
            name: 'Order Delivery Methods'
        };

        brandLookup.all_selections['Splickit_Accepted_Payment_Types'] = {
            un_selected: [],
            selected: [],
            name: 'Payment Types'
        };
    }

    brandLookup.loadCreateLookups = function() {
        Lookup.multipleLookup(['order_del_type', 'Splickit_Accepted_Payment_Types']).then(function(response) {
            var all_lookups = response.data;

            for (var key in all_lookups) {
                brandLookup.all_selections[key]['unselected'] = all_lookups[key];
                brandLookup.all_selections[key]['selected'] = [];
            }
        });
    }

    brandLookup.setLookupToActive = function(lookup) {
        brandLookup.un_selected = brandLookup.all_selections[lookup]['unselected'];
        brandLookup.selected = brandLookup.all_selections[lookup]['selected'];
        brandLookup.name = brandLookup.all_selections[lookup]['name'];
        brandLookup.key = lookup;
    }

    function addLookup(lookup) {
        brandLookup.un_selected.splice(brandLookup.un_selected.indexOf(lookup), 1);
        if (!UtilityService.checkIfObjectWithAttributeExistsInArray(brandLookup.selected, 'lookup_id', lookup.lookup_id)) {
            brandLookup.selected.push(lookup);
        }
    }

    function removeLookup(lookup) {
        brandLookup.selected.splice(brandLookup.selected.indexOf(lookup), 1);
        if (!UtilityService.checkIfObjectWithAttributeExistsInArray(brandLookup.un_selected, 'lookup_id', lookup.lookup_id)) {
            brandLookup.un_selected.push(lookup);
        }
    }

    function setLookupValues() {
        brandLookup.all_selections[brandLookup.key]['unselected'] = brandLookup.un_selected;
        brandLookup.all_selections[brandLookup.key]['selected'] = brandLookup.selected;
        var simple_lookup_array = UtilityService.convertArrayObjectsPropertyToArray(brandLookup.all_selections[brandLookup.key]['selected'], 'type_id_name');
        brandLookup.all_selections[brandLookup.key]['display_list'] = UtilityService.convertArrayToGrammarList(simple_lookup_array);
        console.log(brandLookup.all_selections[brandLookup.key]['display_list']);
    }


    return brandLookup;
});