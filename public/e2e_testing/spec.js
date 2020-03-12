describe('Protractor Demo App', function() {
    var selectDropdownbyNum = function ( element, optionNum ) {
        if (optionNum){
            var options = element.all(by.tagName('option'))
                .then(function(options){
                    console.log(options);
                    //options[optionNum].click();
                });
        }
    };

    it('should have a title', function() {

        expect(browser.getCurrentUrl()).toContain("/#/merchants");

        var create_merchant_popup_button = element(by.id('create-merchant-popup-button'));
        create_merchant_popup_button.click();

        var create_merchant_name = element(by.model('new_merchant.name'));
        create_merchant_name.sendKeys('Test Merchant e2e');

        var create_merchant_brand = element(by.name('brand'));
        create_merchant_brand.$('[value="number:150"]').click();

        var create_merchant_phone = element(by.model('new_merchant.phone_no'));
        create_merchant_phone.sendKeys('5555555555');

        var create_merchant_email = element(by.model('new_merchant.shop_email'));
        create_merchant_email.sendKeys('test.email@yourcompany.com');

        var create_merchant_address1 = element(by.model('new_merchant.address1'));
        create_merchant_address1.sendKeys('123 Main Street');

        var create_merchant_city = element(by.model('new_merchant.city'));
        create_merchant_city.sendKeys('Denver');

        var create_merchant_state = element(by.name('state'));
        create_merchant_state.$('[value="string:CO"]').click();

        var create_merchant_zip = element(by.model('new_merchant.zip'));
        create_merchant_zip.sendKeys('34535');

        var create_merchant_timezone = element(by.name('time_zone'));
        create_merchant_timezone.$('[value="-6"]').click();

        var submit_create_merchant = element(by.id('submit-create-merchant'));
        submit_create_merchant.click();
    });
});
