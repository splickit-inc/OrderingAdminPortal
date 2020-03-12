var NavigationHelpers = require('../helpers/navigation_helpers.js');
var ValidatorHelpers = require('../helpers/validator_helpers.js');
var PageHelpers = require('../helpers/page_helpers.js');
var ValueHelpers = require('../helpers/value_helpers.js');

describe('Menu List', function() {
    it('Should Be Able to Create Menu', function() {

        browser.get('/#/menus');

        PageHelpers.clickButtonById('create-menu-button');
        browser.sleep(500);


        PageHelpers.setInputTextByModel('vm.newMenu.name', 'Test Menu');
        PageHelpers.setInputTextByModel('vm.newMenu.description', 'Test Menu Desc');

        var role_select = element(by.name('brand'));
        role_select.$('[value="number:150"]').click();


        element.all(by.repeater('merchant in vm.brandMerchants')).then(function(merchants) {
            var merchant_click_element = merchants[0].element(by.className('add-merchant-icon-menu'));
            merchant_click_element.click();
        });

        PageHelpers.clickButtonById('create-menu-submit');

        browser.sleep(3000);

    });
});
