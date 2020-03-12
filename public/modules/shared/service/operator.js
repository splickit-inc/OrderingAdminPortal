/**
 * Created by boneill on 3/9/17.
 */
// Google async initializer needs global function, so we use $window
angular.module('shared').service('Operator', Operator);

function Operator($http, Users, Merchant) {

    var service = this;
    service.operator_merchants = [];
    service.merchant_filter = '';
    service.current_merchant = false;

    service.getMultiOperatorMerchants = function() {
        $http.get('/operator/multi_merchants').then(function(response) {
            service.operator_merchants = response.data;
        });
    }

    service.setMerchant = function(merchant) {
        $http.post('merchant/set_current', {merchant_id: merchant.merchant_id}).then(function() {
            service.current_merchant = merchant.merchant_id;
            Merchant.getProgressComplete();
        });
    }

    service.filterMerchantSelect = function(merchant) {
        if (service.merchant_filter.length < 3) {
            return true;
        }
        var filter_text_reg_exp = new RegExp(service.merchant_filter.toUpperCase(), 'g');

        if (merchant.name.toUpperCase().match(filter_text_reg_exp) || merchant.address1.toUpperCase().match(filter_text_reg_exp) || merchant.state.toUpperCase().match(filter_text_reg_exp) || merchant.city.toUpperCase().match(filter_text_reg_exp) || merchant.merchant_id == service.merchant_filter) {
            return true;
        }
        else {
            return false;
        }
    }

    return service;
}