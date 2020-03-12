var PageHelpers = function helpers(){
    this.selectDropdownbyNum = function(element, optionNum){
        if (optionNum){
            var options = element.all(by.tagName('option'))
                .then(function(options){
                    console.log(options);
                    //options[optionNum].click();
                });
        }
    };

    this.setInputTextByModel = function(model_name, text) {
        var input_element = element(by.model(model_name));
        input_element.clear().then(function() {
            input_element.sendKeys(text);
        })
    }

    this.clickButtonById = function(button_id) {
        var input_form_button = element(by.id(button_id));
        input_form_button.click();
    }

    this.selectInputValueWithName = function(element_name, input_value) {
        var element_input = element(by.name(element_name));
        element_input.$('[value="'+input_value+'"]').click();
    }
};

module.exports = new PageHelpers();
