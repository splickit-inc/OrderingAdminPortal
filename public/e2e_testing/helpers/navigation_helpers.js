var NavigationHelpers = function helpers(){
    this.merchantsNav = function(){
        var merchant_nav_button = element(by.id('side-nav-merchants'));
        merchant_nav_button.click();
    };

    this.navIntoMerchant = function() {
        this.merchantsNav();

        var merchant_search_text = element(by.id('merchant-search-text'));
        merchant_search_text.sendKeys('Test Merchant e2e');

        var merchant_search_button = element(by.id('merchant-search-button'));
        merchant_search_button.click();

        var first_merchant_result = element(by.repeater('merch in merchant.result_merchants').row(0));
        first_merchant_result.click();
    }
};

module.exports = new NavigationHelpers();
