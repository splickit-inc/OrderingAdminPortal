var NavigationHelpers = require('../helpers/navigation_helpers.js');
var PageHelpers = require('../helpers/page_helpers.js');
var ValidatorHelpers = require('../helpers/validator_helpers.js');


describe('Merchant General Info', function() {
    it('Should be able to update Merchant General Info', function() {
        browser.get('#/merchant/general_info');

        PageHelpers.setInputTextByModel('info.location.phone_no', '1234567890');
        PageHelpers.setInputTextByModel('info.location.display_name', 'Test Display');

        PageHelpers.clickButtonById('merchant-location-submit');

        //ValidatorHelpers.elementContainsTextById('location-updated-success', 'Location Updated');

        //Configurations Check
        PageHelpers.setInputTextByModel('info.config.lead_time', '11');
        PageHelpers.clickButtonById('merchant-active-inactive-toggle');
        PageHelpers.clickButtonById('configurations-submit');

        //ValidatorHelpers.elementContainsTextById('configurations-udpated-success', 'Configurations Updated');

        //Messages
        PageHelpers.setInputTextByModel('info.message.custom_order_message', 'Test Order Message');
        PageHelpers.clickButtonById('merchant-messages-submit');

        //ValidatorHelpers.elementContainsTextById('messages-updated-success', 'Messages Updated');

    });
});
