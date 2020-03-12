var NavigationHelpers = require('../helpers/navigation_helpers.js');
var PageHelpers = require('../helpers/page_helpers.js');
var ValidatorHelpers = require('../helpers/validator_helpers.js');


describe('Create Promo', function() {
    it('Should Create Promo Type 1', function() {
        browser.get('#/promos');
        PageHelpers.clickButtonById('open-create-promo-button');

        var brand_select = element(by.name('brand'));
        brand_select.$('[value="number:150"]').click();
        var promo_type_select = element(by.name('cmb_promo_type'));
        promo_type_select.$('[value="number:1"]').click();

        PageHelpers.setInputTextByModel('new_promo.promo_data.key_word', 'PromoType1Key1,PromoType1Key2');
        PageHelpers.setInputTextByModel('new_promo.promo_data.description', 'E2E Promo Type 1');


        var start_date_element = element(by.model("new_promo.promo_data.start_date"));
        start_date_element.evaluate("new_promo.promo_data.start_date = '08/01/2020';");


        var end_date_element = element(by.model("new_promo.promo_data.end_date"));
        end_date_element.evaluate("new_promo.promo_data.end_date = '08/01/2020';");

        var discount_type_select = element(by.name('discount_type'));
        discount_type_select.$('[value="string:dollars_off"]').click();
        end_date_element.evaluate("new_promo.promo_data.end_date = '08/01/2020';");

        PageHelpers.setInputTextByModel('new_promo.promo_data.fixed_amount_off', '7');
        PageHelpers.setInputTextByModel('new_promo.promo_data.max_amt_off', '7');
        //PageHelpers.setInputTextByModel('new_promo.promo_data.fixed_amount_off', '25');
         //PageHelpers.setInputTextByModel('new_promo.promo_data.max_use', '5');
        //
         PageHelpers.clickButtonById('submit-create-new-promo');
         browser.sleep(600);
         var ok_button = element(by.buttonText('Confirm All Merchants'));
         ok_button.click();
        browser.sleep(600);
    })

    // it('Should Create Promo Type 2', function() {
    //     browser.get('#/promos');
    //     PageHelpers.clickButtonById('open-create-promo-button');
    //
    //     var brand_select = element(by.name('brand'));
    //     brand_select.$('[value="number:150"]').click();
    //     var promo_type_select = element(by.name('cmb_promo_type'));
    //     promo_type_select.$('[value="number:1"]').click();
    //
    //     PageHelpers.setInputTextByModel('new_promo.promo_data.key_word', '75OFF, TESTABC');
    //     PageHelpers.setInputTextByModel('new_promo.promo_data.description', 'E2E Promo Type 2');
    //
    //
    //     var start_date_element = element(by.model("new_promo.promo_data.start_date"));
    //     start_date_element.evaluate("new_promo.promo_data.start_date = '08/01/2020';");
    //
    //
    //     var end_date_element = element(by.model("new_promo.promo_data.end_date"));
    //     end_date_element.evaluate("new_promo.promo_data.end_date = '08/01/2020';");
    //
    //     var discount_type_select = element(by.name('discount_type'));
    //     discount_type_select.$('[value="string:dollars_off"]').click();
    //     end_date_element.evaluate("new_promo.promo_data.end_date = '08/01/2020';");
    //
    //     PageHelpers.setInputTextByModel('new_promo.promo_data.promo_amt', '2');
    //     PageHelpers.setInputTextByModel('new_promo.promo_data.max_amt_off', '2');
    //     //PageHelpers.setInputTextByModel('new_promo.promo_data.fixed_amount_off', '25');
    //      //PageHelpers.setInputTextByModel('new_promo.promo_data.max_use', '5');
    //     //
    //      PageHelpers.clickButtonById('submit-create-new-promo');
    //      browser.sleep(600);
    //      var ok_button = element(by.buttonText('Confirm All Merchants'));
    //      ok_button.click();
    //     browser.sleep(600);
    // });
});
