exports.config = {
    framework: 'jasmine',
    seleniumAddress: 'http://localhost:4444/wd/hub',
    // specs: ['merchant/CreateMerchantSpec.js', 'merchant/MerchantGeneralInfoSpec.js',
    //         'merchant/MerchantHoursSpec.js', 'promos/CreatePromoSpec.js'],
    // //specs: ['merchant/CreateMerchantSpec.js','users/CreateUserSpec.js', 'merchant/'],
    //Single Test
    // specs: ['merchant/CreateMerchantSpec.js', 'merchant/MerchantContactSpec.js',
    //     'merchant/MerchantDeliverySpec.js','merchant/MerchantGeneralInfoSpec.js','promos/CreatePromoSpec.js',
    //     'menu/CreateMenuSpec.js', 'menu/MenuItemListSpec.js',   'menu/EditItemSpec.js', 'menu/EditModifierItem.js'],
   specs: [
        'merchant/CreateMerchantSpec.js',
       'merchant/MerchantContactSpec.js',
      'merchant/MerchantDeliverySpec.js',
       'merchant/MerchantHoursSpec.js',
         'merchant/MerchantTaxSpec',
         'merchant/MerchantGeneralInfoSpec.js',
        'promos/CreatePromoSpec.js',
       'menu/CreateMenuSpec.js',
       'menu/MenuItemListSpec.js',
       'menu/EditItemSpec.js',
       'menu/EditModifierItem.js',
        'users/CreateUserSpec.js'
   ],
    onPrepare: function() {
        //Super User Login
        browser.get('/#/login');

        var email = element(by.id('inputEmail'));

        var password = element(by.id('inputPassword'));

        email.sendKeys('SuperUser.Tester@yourcompany.com');
        password.sendKeys('Test');

        var login_button = element(by.id('log-in-button'));

        login_button.click();
    },

    jasmineNodeOpts: {
        showColors: true, // Use colors in the command line report.
        defaultTimeoutInterval: 250000
    },
    //baseUrl: 'http://192.168.149.198:30024'
    baseUrl: 'http://localhost:10082'
}
