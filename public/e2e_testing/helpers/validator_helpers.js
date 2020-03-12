var ValidatorHelpers = function helpers(){
    this.elementContainsTextById = function(element_id, expected_text){
        var search_results_table = element(by.id(element_id));
        search_results_table.getText().then(function(text){expect(text).toContain(expected_text)});
    };
};

module.exports = new ValidatorHelpers();
