var NavigationHelpers = require('../helpers/navigation_helpers.js');
var ValidatorHelpers = require('../helpers/validator_helpers.js');
var PageHelpers = require('../helpers/page_helpers.js');
var ValueHelpers = require('../helpers/value_helpers.js');

describe('Merchant Contact', function() {
   it('Should Be Able to Update Merchant Contact', function() {
       browser.get('/#/merchant/contact');

       //PageHelpers.clickButtonById('side-nav-merchant-contact');
       PageHelpers.clickButtonById('open-new-contact-button');

       browser.sleep(500); //Wait for Modal to Load

       PageHelpers.setInputTextByModel('contact.new_admin_email.name', 'Jane Testadmin');

       var test_email = 'AdminContactEmail@yourcompany.com';
       PageHelpers.setInputTextByModel('contact.new_admin_email.email', test_email);

       PageHelpers.clickButtonById('daily-new-admin-toggle');
       PageHelpers.clickButtonById('new-contact-admin-submit');

       //ValidatorHelpers.elementContainsTextById('store-notification-settings-table', test_email);

       PageHelpers.clickButtonById('open-new-additional-contact-info');
       browser.sleep(500); //Wait for Modal to Load

       PageHelpers.setInputTextByModel('contact.new_admin_phone.name', 'Jane AddContact');
       PageHelpers.setInputTextByModel('contact.new_admin_phone.title', 'Manager');
       PageHelpers.setInputTextByModel('contact.new_admin_phone.phone_no', '5555555555');
       var email = 'testadnlcontact@yourcompany.com';
       PageHelpers.setInputTextByModel('contact.new_admin_phone.email', email);

       PageHelpers.clickButtonById('create-new-addtl-contact-submit');

        browser.sleep('10000');
    });
});
