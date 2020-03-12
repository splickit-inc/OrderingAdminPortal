var NavigationHelpers = require('../helpers/navigation_helpers.js');
var ValidatorHelpers = require('../helpers/validator_helpers.js');
var PageHelpers = require('../helpers/page_helpers.js');
var ValueHelpers = require('../helpers/value_helpers.js');

describe('Merchant Hours', function() {
    it('Should Be Able to Update Merchant Hours', function() {

        browser.get('/#/merchant/hours');

        element.all(by.repeater('hour in vm_hour.hours')).then(function(hours) {
            var human_hour_element = hours[0].element(by.className('open-hour-human'));
            var open_close_element = hours[0].element(by.className('store-hour-open-close-toggle'));
            open_close_element.click();
            human_hour_element.clear();
            human_hour_element.sendKeys('6:00');
        });

        PageHelpers.clickButtonById('store-hours-submit-button');

        //Validate Success
        //ValidatorHelpers.elementContainsTextById('store-hours-updated-success', 'Store Hours Updated');

        element.all(by.repeater('hour in vm_hour.delivery_hours')).then(function(delivery_hours) {
            var human_hour_element = delivery_hours[0].element(by.className('open-human-hour'));
            var open_close_element = delivery_hours[0].element(by.className('delivery-hour-closed-toggle'));
            open_close_element.click();
            human_hour_element.clear();
            human_hour_element.sendKeys('8:00');
        });

        //Validate Success
        PageHelpers.clickButtonById('delivery-hours-submit');
        //ValidatorHelpers.elementContainsTextById('delivery-hours-update-success', 'Delivery Hours Updated');

        PageHelpers.clickButtonById('open-custom-holiday-hour');
        browser.sleep(500);
        PageHelpers.clickButtonById('create-custom-holiday-hour-button');

    });
});
