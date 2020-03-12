var NavigationHelpers = require('../helpers/navigation_helpers.js');
var ValidatorHelpers = require('../helpers/validator_helpers.js');
var PageHelpers = require('../helpers/page_helpers.js');
var ValueHelpers = require('../helpers/value_helpers.js');

describe('Merchant Delivery', function() {
    it('Should Be Able to Update Merchant Delivery', function() {

        browser.get('/#/merchant/delivery');

        //Lead Times
        PageHelpers.setInputTextByModel('delivery.delivery_info.max_days_out', '7');
        PageHelpers.setInputTextByModel('delivery.delivery_info.minimum_delivery_time', '10');
        PageHelpers.setInputTextByModel('delivery.delivery_info.delivery_increment', '5');

        PageHelpers.clickButtonById('delivery-increments-submit');

        //ValidatorHelpers.elementContainsTextById('delivery-increments-success', 'Delivery Configuration Updated');

        var delivery_zone_type = element(by.name('delivery_zone_type'));
        delivery_zone_type.$('[value="string:driving"]').click();

        PageHelpers.clickButtonById('confirm-delivery-zone-definition-change');

        browser.sleep(500);

        PageHelpers.clickButtonById('open-delivery-zone-create');

        browser.sleep(500);

        PageHelpers.setInputTextByModel('delivery.new_delivery_zone.name', 'Test DZ Create');
        PageHelpers.setInputTextByModel('delivery.new_delivery_zone.price', '5');
        PageHelpers.setInputTextByModel('delivery.new_delivery_zone.minimum_order_amount', '15');

        var create_dz_type_select = element(by.name('dz_type'));
        create_dz_type_select.$('[value="string:Regular"]').click();

        PageHelpers.clickButtonById('create-dz-submit');

        browser.sleep(5000);
    });
});
