var NavigationHelpers = require('../helpers/navigation_helpers.js');
var ValidatorHelpers = require('../helpers/validator_helpers.js');
var PageHelpers = require('../helpers/page_helpers.js');
var ValueHelpers = require('../helpers/value_helpers.js');

describe('Item Edit', function() {
    it('Should Be Able to Update Item', function() {

        browser.get('/#/menu/item');

        PageHelpers.clickButtonById('item-modifier-groups-open');
        PageHelpers.clickButtonById('modifier-show-active-toggle');

        element.all(by.repeater('modifier_group in item.current_item.modifier_groups')).then(function(mod_groups_allowed) {
            var mod_group = mod_groups_allowed[0].element(by.className('switch-mod-group-allowed'));
            mod_group.click();
        });

        PageHelpers.setInputTextByModel('item.current_item.item_name', 'Mushroom Swiss2');

        PageHelpers.clickButtonById('submit-update-item');

        browser.sleep(500);

        var modifier_group_select = element(by.name('propagate_option_select'));
        modifier_group_select.$('[value="string:All"]').click();

        PageHelpers.clickButtonById('submit-propagate-options');

        browser.sleep(5000);
    });
});
