var NavigationHelpers = require('../helpers/navigation_helpers.js');
var ValidatorHelpers = require('../helpers/validator_helpers.js');
var PageHelpers = require('../helpers/page_helpers.js');
var ValueHelpers = require('../helpers/value_helpers.js');

describe('Create User', function() {
    it('Should Be Able to Create Users', function() {

        //Create Super User
        browser.get('/#/user/create');

        PageHelpers.clickButtonById('set-password-checkbox');

        PageHelpers.setInputTextByModel('uc.new_user.first_name', 'Joe');
        PageHelpers.setInputTextByModel('uc.new_user.last_name', 'Tester');
        PageHelpers.setInputTextByModel('uc.new_user.email', 'Joe.Tester@yourcompany.com');

        var role_select = element(by.name('role'));
        role_select.$('[value="number:1"]').click();

        var organization_select = element(by.name('organization'));
        organization_select.$('[value="number:1"]').click();

        PageHelpers.setInputTextByModel('uc.new_user.password', 'Test96321!');
        PageHelpers.setInputTextByModel('uc.new_user.retype_password', 'Test96321!');

        PageHelpers.clickButtonById('create-user-submit');
        //ValidatorHelpers.elementContainsTextById('create-user-success', 'Created!');

        browser.sleep(2000);

        //Create Operator
        browser.get('/#/user/create');

        PageHelpers.setInputTextByModel('uc.new_user.first_name', 'Test');
        PageHelpers.setInputTextByModel('uc.new_user.last_name', 'Operator');
        PageHelpers.setInputTextByModel('uc.new_user.email', 'Test.Operator@yourcompany.com');

        role_select.$('[value="number:5"]').click();

        organization_select.$('[value="number:1"]').click();

        PageHelpers.setInputTextByModel('uc.search_text', 'Tes');

        browser.actions().sendKeys(protractor.Key.ENTER).perform();

        element.all(by.repeater('merchant in uc.selectable_merchants')).then(function(merchants) {
            var merchant_select = merchants[0].element(by.className('add-merchant'));
            merchant_select.click();
        });

        PageHelpers.clickButtonById('set-password-checkbox');

        PageHelpers.setInputTextByModel('uc.new_user.password', 'Test96321!');
        PageHelpers.setInputTextByModel('uc.new_user.retype_password', 'Test96321!');

        PageHelpers.clickButtonById('create-user-submit');

    });
});
