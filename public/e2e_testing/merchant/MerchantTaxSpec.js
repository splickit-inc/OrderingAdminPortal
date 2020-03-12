var NavigationHelpers = require('../helpers/navigation_helpers.js');
var ValidatorHelpers = require('../helpers/validator_helpers.js');
var PageHelpers = require('../helpers/page_helpers.js');
var ValueHelpers = require('../helpers/value_helpers.js');

describe('Merchant Tax', function() {
    it('Should Be Able to Update Merchant Tax Info', function() {

        browser.get('/#/merchant/tax');

        //Regular Tax Panel
        PageHelpers.setInputTextByModel('tax.sales_tax.locale_description', 'Test Sales Tax');
        PageHelpers.setInputTextByModel('tax.sales_tax.rate', '8');

        PageHelpers.clickButtonById('tax-update-submit');
        //ValidatorHelpers.elementContainsTextById('tax-update-success', 'Sales Tax Updated');

        //Fixed Tax Panel
        PageHelpers.setInputTextByModel('tax.fixed_tax.name', 'Fix Tax Test');
        PageHelpers.setInputTextByModel('tax.fixed_tax.description', 'Fix Tax Description');
        PageHelpers.setInputTextByModel('tax.fixed_tax.amount', '2');

        PageHelpers.clickButtonById('fixed-tax-submit');
        //ValidatorHelpers.elementContainsTextById('fixed-tax-success', 'Fixed Tax Updated');

        //Delivery Tax Panel
        PageHelpers.setInputTextByModel('tax.delivery_tax.locale_description', 'Delivery Tax Test');
        PageHelpers.setInputTextByModel('tax.delivery_tax.rate', '3');

        PageHelpers.clickButtonById('delivery-tax-submit');
        //ValidatorHelpers.elementContainsTextById('delivery-tax-success', 'Delivery Tax Updated');

    });
});
