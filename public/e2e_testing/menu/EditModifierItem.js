var NavigationHelpers = require('../helpers/navigation_helpers.js');
var ValidatorHelpers = require('../helpers/validator_helpers.js');
var PageHelpers = require('../helpers/page_helpers.js');
var ValueHelpers = require('../helpers/value_helpers.js');

describe('Modifier Item', function() {
    it('Should Be Able to Edit Modifier Item', function() {

        browser.get('/#/menu/items');
        
        browser.sleep(500);
        
        PageHelpers.setInputTextByModel('im.menu.search_text', 'Lettuce');

        element.all(by.repeater('modifier in im.menu.all_modifier_items')).then(function(modifier_groups) {
            var lettuce_click = modifier_groups[0].element(by.className('all-open-mod-item-icon'));
            lettuce_click.click();
        });

        PageHelpers.setInputTextByModel('mi.current_mod_item.modifier_item_name', 'Shredded Lettuce');


        PageHelpers.clickButtonById('modifier-item-update-submit');

        browser.sleep(500);

        var modifier_group_select = element(by.name('propagate_option_select'));
        modifier_group_select.$('[value="string:All"]').click();

        PageHelpers.clickButtonById('submit-propagate-options');
    });
});
