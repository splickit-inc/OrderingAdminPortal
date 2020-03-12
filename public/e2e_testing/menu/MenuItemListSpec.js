var NavigationHelpers = require('../helpers/navigation_helpers.js');
var ValidatorHelpers = require('../helpers/validator_helpers.js');
var PageHelpers = require('../helpers/page_helpers.js');
var ValueHelpers = require('../helpers/value_helpers.js');

describe('Menu Item List', function() {
    it('Should Be Able to Create New Section', function() {

        browser.get('/#/menu/items');

        browser.sleep(3000);
        PageHelpers.clickButtonById('create-new-section');

        browser.sleep(500);

        PageHelpers.setInputTextByModel('im.new_section.menu_type_name', 'Burgers');
        PageHelpers.setInputTextByModel('im.new_section.menu_type_description', 'These are our tasty burgers.');

        browser.sleep(5000);

        var menu_category_select = element(by.name('create_cat_id'));
        menu_category_select.$('[value="string:E"]').click();

        PageHelpers.clickButtonById('create-section-active');

        PageHelpers.setInputTextByModel('im.new_section.items', 'Classic; Mushroom Swiss; Double');
        PageHelpers.setInputTextByModel('im.new_section.sizes', 'Small; Medium; Large');

        PageHelpers.clickButtonById('submit-new-section');

        PageHelpers.clickButtonById('create-new-modifier-group-button');

        browser.sleep(700);

        PageHelpers.setInputTextByModel('im.new_modifier_group.modifier_group_name', 'Burger Toppings');
        PageHelpers.setInputTextByModel('im.new_modifier_group.modifier_description', 'These are toppings for a burger.');

        var modifier_group_select = element(by.name('create_modifier_type'));
        modifier_group_select.$('[value="string:T"]').click();

        PageHelpers.setInputTextByModel('im.new_modifier_group.priority', '101');
        PageHelpers.setInputTextByModel('im.new_modifier_group.item_list', 'Lettuce; Tomato; Onion; Pickles; Cheese; Bacon');
        PageHelpers.setInputTextByModel('im.new_modifier_group.default_item_price', '0');
        PageHelpers.setInputTextByModel('im.new_modifier_group.default_item_max', '4');

        PageHelpers.clickButtonById('create-modifier-group-submit');

        // element.all(by.repeater('section in im.menu.menu_types')).then(function(sections) {
        //     var burger_item = sections[0].element(by.className('section-select'));
        //     burger_item.click();
        // });

        PageHelpers.setInputTextByModel('im.menu.search_text', 'Mushroom Swiss');

        element.all(by.repeater('item in im.menu.all_items')).then(function(sections) {
            var all_mushroom_swiss_item = sections[0].element(by.className('all-list-open-view-item'));
            all_mushroom_swiss_item.click();
        });

    });

    // it('Should Be Able to Create New Modifier Group', function() {
    //
    //
    // });
    //
    // it('Should be able to Open Edit Item', function() {
    //
    //
    //     element.all(by.repeater('section in im.menu.menu_types')).then(function(sections) {
    //        var burger_item = sections[0].element(by.className('section-select'));
    //         burger_item.click();
    //     });
    //
    //     PageHelpers.setInputTextByModel('im.menu.search_text', 'Mushroom Swiss');
    //
    //     element.all(by.repeater('item in im.menu.all_items')).then(function(sections) {
    //         var all_mushroom_swiss_item = sections[0].element(by.className('all-list-open-view-item'));
    //         all_mushroom_swiss_item.click();
    //     });
    //
    // });
});
