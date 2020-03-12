var NavigationHelpers = require('../helpers/navigation_helpers.js');
var ValidatorHelpers = require('../helpers/validator_helpers.js');
var PageHelpers = require('../helpers/page_helpers.js');
var ValueHelpers = require('../helpers/value_helpers.js');

describe('Create Brand', function() {
    it('Should Be Able to Create Brand', function() {

        browser.get('/#/brand/create');


        PageHelpers.setInputTextByModel('brand.new_brand.name', 'Test Brand123ABC');
        PageHelpers.clickButtonById('brand-create-submit');

        ValidatorHelpers.elementContainsTextById('swal2-title', 'BRAND HAS BEEN CREATED!');
    });
});
