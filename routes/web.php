<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
Route::get('/', 'UserController@index');
Route::post('login_attempt', 'UserController@loginAttempt');

//Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');

Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
//Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');


Route::get('reset_password', function () {
    return view('reset_password');
});
Route::get('user/session_info', 'UserController@getSession');

/*
| Auth Middleware
|------------------------------------
| These are routes that should only be accessed by logged in Users.
| The middleware will cause an error to be thrown to an AJAX request
| if the user is not logged in.  Angular will then route the user to the login screen.
*/
Route::group(['middleware' => ['auth', 'check_token']], function () {
    Route::get('home', 'UserController@home');

    /******************************
     * MERCHANT ROUTES
     ******************************/
    //Loads Merchant Page/Application
    Route::get('merchant', 'MerchantController@index');

    //Merchant - General Info
    Route::get('merchant/general_info', 'Merchant\GeneralInfoController@index');
    Route::put('merchant/location', 'Merchant\GeneralInfoController@updateLocation');
    Route::put('merchant/config', 'Merchant\GeneralInfoController@updateConfig');
    Route::put('merchant/messages', 'Merchant\GeneralInfoController@updateMessages');
    Route::put('merchant/business_info', 'Merchant\GeneralInfoController@updateBusinessInfo');
    Route::put('merchant/business_banking', 'Merchant\GeneralInfoController@updateBusinessBanking');
    //Merchant - Contact
    Route::get('merchant/contact', 'Merchant\ContactController@index');
    Route::post('merchant/contact_admin_email', 'Merchant\ContactController@createAdminEmail');
    Route::delete('merchant/delete_admin_email/{id}', 'Merchant\ContactController@deleteAdminEmail');
    Route::put('merchant/admin_email', 'Merchant\ContactController@updateAdminEmail');
    Route::post('merchant/admin_phone', 'Merchant\ContactController@createAdminPhone');
    Route::delete('merchant/delete_admin_phone/{id}', 'Merchant\ContactController@deleteAdminPhone');
    Route::put('merchant/admin_phone', 'Merchant\ContactController@updateAdminPhone');
    //Merchant - Hours
    Route::get('merchant/hours', 'Merchant\HoursController@index');
    Route::put('merchant/hours', 'Merchant\HoursController@updateStoreHours');
    Route::put('merchant/delivery_hours', 'Merchant\HoursController@updateDeliveryHours');
    Route::put('merchant/holiday', 'Merchant\HoursController@updateHoliday');
    Route::post('merchant/holiday_hours', 'Merchant\HoursController@createHolidayHours');
    Route::post('merchant/standard_holiday_hours', 'Merchant\HoursController@updateStandardHolidayHours');
    Route::delete('merchant/delete_custom_holiday/{id}', 'Merchant\HoursController@deleteHolidayHours');
    Route::get('merchant/hours/mid_day_hours', 'Merchant\HoursController@getMidDayClosures');
    Route::post('merchant/hours/mid_day_hours', 'Merchant\HoursController@addOrUpdateMidDayClosures');
    Route::delete('merchant/hours/mid_day_hours/{hour_id}', 'Merchant\HoursController@deleteMidDayClosures');

    //Merchant - Order Receiving
    Route::resource('merchant/order_receiving', 'Merchant\OrderReceivingController');
    //Merchant - Tax
    Route::get('merchant/tax', 'Merchant\TaxController@index');
    Route::put('merchant/fixed_tax', 'Merchant\TaxController@updateFixedTax');
    Route::put('merchant/sales_tax', 'Merchant\TaxController@updateSalesTax');
    Route::put('merchant/delivery_tax', 'Merchant\TaxController@updateDeliveryTax');
    //Merchant - Delivery
    Route::get('merchant/delivery', 'Merchant\DeliveryController@index');
    Route::put('merchant/delivery_info', 'Merchant\DeliveryController@updateDeliveryInfo');
    Route::post('merchant/delivery_zone', 'Merchant\DeliveryController@createDeliveryZone');
    Route::post('merchant/delivery/add_door_dash', 'Merchant\DeliveryController@addDoorDash');
    Route::put('merchant/delivery_zone', 'Merchant\DeliveryController@updateDeliveryZone');
    Route::put('merchant/delivery_zone_defined_by', 'Merchant\DeliveryController@updateDeliveryZoneDefinedBy');
    Route::delete('merchant/delivery_zone/{id}', 'Merchant\DeliveryController@deleteDeliveryZone');
    Route::delete('merchant/location', 'Merchant\DeliveryController@getLocation');
    //Merchant - Ordering
    Route::get('merchant/ordering', 'Merchant\OrderingController@index');
    Route::put('merchant/prep_time', 'Merchant\OrderingController@updatePrepTime');
    Route::put('merchant/order_settings', 'Merchant\OrderingController@updateOrderSettings');
    Route::post('merchant/payment_group', 'Merchant\OrderingController@createPaymentGroup');
    Route::delete('merchant/payment_group/{id}', 'Merchant\OrderingController@deletePaymentGroup');
    Route::post('merchant/order_receiving', 'Merchant\OrderingController@createSendOrder');
    Route::put('merchant/order_receiving', 'Merchant\OrderingController@updateSendOrder');
    Route::post('merchant/{merchant_id}/skin/set', 'Merchant\OrderingController@setSkinToMerchant');
    Route::get('merchant/{merchant_id}/lead_times', 'Merchant\OrderingController@getLeadTimeByDay');
    Route::post('merchant/{merchant_id}/lead_times', 'Merchant\OrderingController@setLeadTimeByDay');
    Route::delete('merchant/{merchant_id}/lead_times/{lead_time_id}', 'Merchant\OrderingController@deleteLeadTimeByDay');
    Route::get('merchant/ordering/call_in_history', 'Merchant\OrderingController@getCallInHistory');
    //Merchant - Catering
    Route::get('merchant/catering', 'Merchant\CateringController@getCurrentConfiguration');
    Route::post('merchant/catering', 'Merchant\CateringController@updateCateringInfo');
    //Merchant
    Route::post('merchant/set_current', 'MerchantController@setCurrent');
    Route::get('merchant/get_current', 'MerchantController@getCurrentMerchant');
    Route::get('merchant/load', 'MerchantController@load');
    Route::post('create_merchant', 'MerchantController@createNewMerchant');
    Route::post('merchant_search', 'MerchantController@search');
    Route::post('merchant_search_by_menu', 'MerchantController@searchByMenu');
    Route::get('merchant_search_paginated', 'MerchantController@searchPaginated');
    Route::get('merchant_search_by_property', 'MerchantController@searchByProperties');
    Route::get('merchant/first_letter_filter', 'MerchantController@firstLetterFilter');
    Route::post('merchant/delete_admn_email', 'MerchantController@deleteAdmnEmail');
    Route::post('merchant/delete/holiday_hour', 'MerchantController@deleteHolidayHour');
    Route::post('merchant/update/new_holiday_hours', 'MerchantController@newHolidayHours');
    Route::post('merchant/delete/payment', 'MerchantController@deletePayment');
    Route::post('merchant/update/payments', 'MerchantController@updatePayments');
    Route::post('merchant/create/send_order', 'MerchantController@createSendOrder');
    Route::get('view_merchant/{merchant_id}', 'MerchantController@viewMerchant');

    Route::get('merchant/all', 'MerchantController@all');

    Route::get('merchant/progress_complete', 'MerchantController@getProgressToCompletion');
    Route::post('merchant/set_menus', 'Merchant\UpdateMenuController@setMenuRelated');

    Route::post('merchant/set_multi_brand', 'MerchantController@setMultiBrand');
    Route::post('merchant/multi_create', 'MerchantController@multiMerchantUpload');
    //Brands
    Route::get('brands', 'BrandController@getAllBrands');
    Route::get('brands/{letter}', 'BrandController@getByFirstLetter');
    Route::post('brands', 'BrandController@searchBrands');
    Route::get('/brands/{id}/merchants', 'BrandController@getMerchants');
    Route::post('brand/create', 'BrandController@createBrand');
    Route::get('brand/{brand_id}', 'BrandController@getBrand');
    Route::get('brand', 'BrandController@getCurrentBrand');
    Route::post('brand/{brand_id}', 'BrandController@editBrand');
    Route::get('/brand/{brand_id}/merchant/{merchant_id}/skins', 'BrandController@getSkinRelatedToMerchant');
    Route::get('/brand/{brand_id}/skins', 'BrandController@getSkinsByBrand');
    Route::resource('/sendmail', 'MailController@index');
    Route::resource('merchant_group', 'MerchantGroupsController');
    Route::get('merchant_group/set_current/{merchant_group_id}', 'MerchantGroupsController@setCurrent');
    Route::get('merchant_group/get_current', 'MerchantGroupsController@getCurrentMerchantGroup');
    Route::post('merchant_group/search', 'MerchantGroupsController@search');
    Route::post('merchant_group/search_all', 'MerchantGroupsController@searchAll');
    Route::post('merchant_group/update', 'MerchantGroupsController@update');
    Route::get('merchant_group/{group_id}/merchants', 'MerchantGroupsController@getMerchants');

    Route::get('merchant/operating_on_off', 'Merchant\OperatorActionsController@index');
    Route::post('merchant/operating_on_off/ordering', 'Merchant\OperatorActionsController@ordering');
    Route::post('merchant/operating_on_off/delivery', 'Merchant\OperatorActionsController@delivery');


    /******************************
     * CUSTOMER SERVICE ROUTES
     ******************************/
    Route::get('customer_service', 'CustomerServiceController@index');
    Route::get('order', 'CustomerService\OrdersController@getOrder');
    Route::get('orders_search', 'CustomerService\OrdersController@paginatedSearch');
    Route::post('orders_search', 'CustomerService\OrdersController@search');
    Route::post('order_search_offset', 'CustomerService\OrdersController@searchOffsetResults');
    Route::post('order/set_current', 'CustomerService\OrdersController@setCurrent');
    Route::post('order/resend_order', 'CustomerService\OrdersController@resendOrder');
    Route::post('order/reassign_order', 'CustomerService\OrdersController@reassignOrder');
    Route::post('order/refund_order', 'CustomerService\OrdersController@refundOrder');
    Route::post('order/change_order_status', 'CustomerService\OrdersController@changeOrderStatus');
    Route::get('order/live_orders', 'CustomerService\OrdersController@liveOrders');
    Route::get('user_search', 'CustomerService\CustomerServiceUserController@paginatedSearch');
    Route::post('customer_service/set_current_user', 'CustomerService\CustomerServiceUserController@setCurrentUser');
    Route::post('open_edit_user', 'CustomerService\CustomerServiceUserController@openEditUser');
    Route::get('current_user', 'CustomerService\CustomerServiceUserController@index');
    Route::post('customer_service/edit_user', 'CustomerService\CustomerServiceUserController@updateUser');
    Route::get('customer_service/user_delivery_locations', 'CustomerService\CustomerServiceUserController@getDeliveryLocations');
    Route::get('customer_service/user_brand_loyalty_history', 'CustomerService\CustomerServiceUserController@getBrandLoyaltyHistory');
    Route::post('customer_service/user_brand_loyalty_details', 'CustomerService\CustomerServiceUserController@getLoyaltyDetailsForUserSelection');
    Route::get('customer_service/user_brand_loyalty', 'CustomerService\CustomerServiceUserController@getBrandLoyalty');
    Route::get('customer_service/user_order_history', 'CustomerService\CustomerServiceUserController@getOrderHistory');
    Route::post('customer_service/user_blacklist', 'CustomerService\CustomerServiceUserController@blacklistUser');
    Route::post('customer_service/user_delete', 'CustomerService\CustomerServiceUserController@deleteUser');
    Route::post('customer_service/adjustLoyalty', 'CustomerService\CustomerServiceUserController@adjustLoyalty');
    Route::get('customer_service/refund_history', 'CustomerService\CustomerServiceUserController@getRefundHistory');
    Route::get('user/primary_account', 'CustomerService\CustomerServiceUserController@makeDefaultBrandLoyaltyAccount');
    Route::get('recently_visited_users', 'CustomerService\CustomerServiceUserController@getRecentlyVisited');
    Route::get('customer_service/index', 'CustomerService\OrdersController@index');
    Route::get('order/order_detail', 'CustomerService\OrdersController@getOrderDetail');
    Route::get('customer_service/parking_lot', 'CustomerService\ParkingLotUsersController@getParkingLotUsers');
    Route::get('future_orders/recently_visited', 'CustomerService\OrdersController@getRecentlyVisited');
    Route::get('future_orders', 'CustomerService\OrdersController@getFutureOrders');

    /******************************
     * MENU ROUTES
     ******************************/
    Route::get('menus/{brand_id}', 'MenuController@getMenusByBrandID')->where('brand_id', '[0-9]+');
    Route::get('menus/current_merchant', 'MenuController@getMenusCurrentMerchant');
    Route::get('menu', 'MenuController@index');
    Route::get('menu/load', 'MenuController@load');
    Route::post('menu_search', 'MenuController@search');
    Route::post('menu/create', 'MenuController@create');
    Route::post('menu/basic_update', 'MenuController@basicUpdate');
    Route::get('menu/menu_types', 'Menu\MenuTypeController@index');
    Route::get('menu/export/{merchantId}', 'MenuController@export');
    Route::post('menu/menu_type/priority_order', 'Menu\MenuTypeController@updateMenuTypePriorities');
    Route::post('menu/menu_type/image_upload', 'Menu\MenuTypeController@imageUpload');
    Route::get('menu/item_modifiers', 'Menu\ItemsModifiersController@index');
    Route::get('menu/current_menu_types', 'Menu\MenuTypeController@currentMenuTypes');
    Route::get('menu/current_modifier_groups', 'Menu\ModifierController@currentModifierGroups');
    Route::get('menu/menu_type/{menu_type_id}/item/{item_id}/mod_group_item/{mod_group_item_id}', 'Menu\ItemsModifiersController@setMenuItem');
    Route::get('menu/modifier_group/{modifier_group_id}/modifier_item/{modifier_group_item}', 'Menu\ItemsModifiersController@setModifierItem');
    Route::post('menu/modifier_group/priority_order', 'Menu\ModifierController@reOrderModifierGroups');
    Route::get('menu/current_item', 'Menu\ItemsModifiersController@getItem');
    Route::get('menu/full_item/{item_id}', 'Menu\ItemsModifiersController@fullItem');
    Route::get('menu/edit_menu', 'MenuController@getEditMenu');
    Route::get('menu/promo_bogo_options', 'MenuController@promoBogoOptions');
    Route::get('menu/current_modifier_item', 'Menu\ItemsModifiersController@getModifierItem');
    Route::delete('menu/menu_item/{item_id}', 'Menu\ItemsModifiersController@deleteItem');
    Route::delete('menu/modifier_item/{modifier_item_id}', 'Menu\ItemsModifiersController@deleteModifierItem');
    Route::delete('menu/menu_type/{menu_type_id}', 'Menu\MenuTypeController@deleteMenuType');
    Route::delete('menu/modifier_group/{menu_type_id}', 'Menu\ModifierController@deleteModifierGroup');
    Route::post('menu/create_menu_type', 'Menu\MenuTypeController@createMenuType');
    Route::post('menu/update_menu_type', 'Menu\MenuTypeController@updateMenuType');
    Route::post('menu/create_modifier_group', 'Menu\ModifierController@createModifierGroup');
    Route::post('menu/update_modifier_group', 'Menu\ModifierController@updateModifierGroup');
    Route::post('menu/item', 'Menu\ItemsModifiersController@updateItem');
    Route::post('menu/item/image', 'Menu\ItemsModifiersController@uploadImage');
    Route::post('menu/item/image_upload', 'Menu\ItemsModifiersController@uploadImageTest');
    Route::post('menu/items/priority_order', 'Menu\ItemsModifiersController@reOrderItems');
    Route::post('menu/item/image_upload_s3', 'Menu\ItemsModifiersController@upLoadItemImagesS3');
    Route::post('menu/modifier_item', 'Menu\ItemsModifiersController@updateModifierItem');
    Route::post('menu/modifier_items/priority_order', 'Menu\ItemsModifiersController@reOrderModifierItems');
    Route::post('menu/full_menu_update_item', 'Menu\ItemsModifiersController@updateFullMenuItem');
    Route::post('menu/full_menu_update_modifier_item', 'Menu\ItemsModifiersController@updateFullMenuModifierItem');
    Route::post('menu/create_category_upsell', 'Menu\UpsellsController@createCategoryUpsell');
    Route::post('menu/delete_category_upsell', 'Menu\UpsellsController@deleteCategoryUpsell');
    Route::post('merchant_menu_search', 'MenuController@merchantMenuSearch');
    Route::get('menu/category_upsell', 'Menu\UpsellsController@getCategoryUpsell');
    Route::get('menu/cart_upsells', 'Menu\UpsellsController@getCartUpsellByMenu');
    Route::post('menu/create_cart_upsell', 'Menu\UpsellsController@createCartUpsell');
    Route::post('menu/delete_cart_upsell', 'Menu\UpsellsController@deleteCartUpsell');
    Route::post('menu/pos_import', 'MenuController@posImport');
    Route::get('menu/nutrition', 'Menu\NutritionController@menuNutritionGrid');
    Route::post('menu/nutrition', 'Menu\NutritionController@updateMenuOfferingNutritionInfo');
    Route::get('menu/check_quick_edit', 'MenuController@checkQuickEdit');
    Route::post('menu/copy_merchant_menu', 'MenuController@copyMerchantMenuToMerchant');

    Route::get('set_menu_id/{menu_id}', 'MenuController@setMenuId');
    Route::get('current_menu', 'MenuController@getMenu');
    Route::get('menu/edit_menu_merchant/{merchant_id}', 'MenuController@getMerchantEditMenu');

    /******************************
     * MARKETING
     ******************************/
    Route::get('promos', 'Marketing\PromosController@getActivePromos');
    Route::get('promos/load', 'Marketing\PromosController@index');
    Route::get('promos/recently_visited', 'Marketing\PromosController@getRecentlyVisited');
    Route::post('promos/create', 'Marketing\PromosController@create');
    Route::get('promos/brand_menus/{brand}', 'Marketing\PromosController@getBrandMenus');
    Route::get('promos/current_promo', 'Marketing\PromosController@getCurrentPromo');
    Route::get('set_edit_promo/{promo_id}', 'Marketing\PromosController@setCurrentPromo');
    Route::get('promos/delete_merchant/{merchant_id}', 'Marketing\PromosController@removeMerchant');
    Route::get('promo_search', 'Marketing\PromosController@search');
    Route::post('promo_search', 'Marketing\PromosController@search');
    Route::post('promos/edit_promo/add_merchant', 'Marketing\PromosController@editPromoAddMerchant');
    Route::post('promos/update_promo', 'Marketing\PromosController@updatePromo');
    Route::post('promo/validate_keyword', 'Marketing\PromosController@validateKeywords');
    Route::post('promo/validate_merchants_menu', 'Marketing\PromosController@validateMerchantsMenu');
    Route::get('set_promo_menu_id/{menu_id}', 'Marketing\PromosController@setPromoMenu');
    Route::delete('promos/delete_keyword/{keyword_map_id}', 'Marketing\PromosController@deletePromoKeyword');
    Route::delete('promos/delete_merchant/{merchant_map_id}', 'Marketing\PromosController@deleteMerchant');
    Route::post('promos/add_keyword', 'Marketing\PromosController@addKeyword');
    Route::post('promos/add_merchant', 'Marketing\PromosController@addMerchant');

    /******************************
     * WEB SKIN, "CREATIVE" ROUTES
     ******************************/
    Route::get('web_skin/load', 'WebSkinController@load');
    Route::post('web_skin/create', 'WebSkinController@create');
    Route::get('web_skin/update_skin_attribute', 'WebSkinController@updateSkinAttribute');
    Route::get('web_skin/current', 'WebSkinController@index');
    Route::get('web_skin/push_skin_preview', 'WebSkinController@pushSkinToPreview');
    Route::get('web_skin/push_skin_production', 'WebSkinController@publishSkinToProduction');
    Route::post('web_skin/color_config', 'WebSkinController@saveColor');
    Route::post('web_skin/custom_css_config', 'WebSkinController@saveCustomCSS');
    Route::post('web_skin/set_skin_brand', 'WebSkinController@setBrand');
    Route::get('web_skin/set_web_skin_id/{skin_id}', 'WebSkinController@setSkin');
    Route::get('web_skin/view_brand_skins/{brand_id}', 'WebSkinController@getBrandSkins');
    Route::get('web_skin/get_default_skins', 'WebSkinController@getDefaultSkins');
    Route::post('web_skin/create_skin_template', 'WebSkinController@createSkinTemplate');
    Route::post('web_skin/hero_image_upload', 'WebSkinController@heroImageUpload');
    Route::post('web_skin/logo_image_upload', 'WebSkinController@logoImageUpload');
    /******************************
     * OPERATOR
     ******************************/
    Route::get('operator/order_management', 'Operator\OrderManagementController@currentOrders');
    Route::post('operator/order_management', 'Operator\OrderManagementController@completeOrder');
    Route::get('operator/home_reporting', 'Operator\HomeController@index');
    Route::post('operator/daily_summary_orders', 'Operator\HomeController@dailySummaryDay');
    Route::post('operator/set_refund_order', 'Operator\HomeController@setRefundOrder');
    Route::get('operator/multi_merchants', 'Operator\HomeController@getMultiOperatorMerchants');

    /******************************
     * PORTAL USER MANAGEMENT ROUTES
     ******************************/
    Route::get('/user/create_load', 'UserController@createLoad');
    Route::get('all_users', 'UserController@allUsers');
    Route::post('create_user', 'UserController@createUser');
    Route::get('user/permissions', 'UserController@permissions');
    Route::post('user/delete', 'UserController@delete');
    Route::post('user/edit', 'UserController@editUser');


    Route::get('user/manual_change_password', 'UserController@manualChangeUserPassword');

    /******************************
     * REPORTS
     ******************************/
    Route::post('/reports/overview', 'ReportsController@overview');

    Route::get('/reports/today', 'ReportsController@todayReportData');
    Route::get('/reports/yesterday', 'ReportsController@yesterdayReportData');
    Route::get('/reports/current_week', 'ReportsController@currentWeekData');
    Route::get('/reports/previous_week', 'ReportsController@previousWeekData');
    Route::get('/reports/current_month', 'ReportsController@currentMonthData');
    Route::get('/reports/current_year', 'ReportsController@currentYearData');


    Route::post('/reports/today_filter', 'ReportsController@todayReportData');
    Route::post('/reports/yesterday_filter', 'ReportsController@yesterdayReportData');
    Route::post('/reports/current_week_filter', 'ReportsController@currentWeekData');
    Route::post('/reports/previous_week_filter', 'ReportsController@previousWeekData');
    Route::post('/reports/current_month_filter', 'ReportsController@currentMonthData');
    Route::post('/reports/current_year_filter', 'ReportsController@currentYearData');
    Route::post('/reports/schedule', 'ReportsController@createSchedule');
    // Transactions Report
    Route::get('/reports/transactions', 'Reports\TransactionController@index');
    Route::get('/reports/transactions/exported', 'Reports\TransactionController@exportReport');
    // Sales By Menu Items Report
    Route::get('/reports/sales_menu_items', 'Reports\SalesByMenuItemController@index');
    Route::get('/reports/sales_menu_items/exported', 'Reports\SalesByMenuItemController@exportReport');
    // Sales By Menu Items Report
    Route::get('/reports/merchant_config', 'Reports\MerchantConfigController@index');
    Route::get('/reports/merchant_config/exported', 'Reports\MerchantConfigController@exportReport');
    // All Orders Report
    Route::get('/reports/all_orders', 'Reports\AllOrdersController@index');
    Route::get('/reports/all_orders/exported', 'Reports\AllOrdersController@exportReport');
    // Sales By Menu Items Report
    Route::get('/reports/menu_export', 'Reports\MerchantConfigController@index');
    Route::get('/reports/menu_export/exported', 'Reports\MerchantConfigController@exportReport');
    // Promos Report
    Route::get('/reports/promo', 'Reports\PromosReportController@index');
    Route::get('/reports/promo/exported', 'Reports\PromosReportController@exportReport');
    // Customers Report
    Route::get('/reports/customers', 'Reports\CustomerController@index');
    Route::get('/reports/customers/exported', 'Reports\CustomerController@exportReport');

    Route::get('/reports/statements', 'Reports\StatementController@getStatementsPaginated');
    Route::get('/reports/statements/export', 'Reports\StatementController@exportStatements');

    Route::get('reports/sales', 'Reports\SalesByMenuItemController@getReportByBrandOrMerchant');
    Route::get('reports/sales/export', 'Reports\SalesByMenuItemController@exportReportByBrandOrMerchant');

    Route::resource('scheduled_reports', 'Reports\ScheduledReportsController');

    /******************************
     * VALUE ADDED RESELLERS
     ******************************/
    Route::get('/value_added_resellers/all', 'ValueAddedResellerController@all');
    Route::get('/value_added_resellers/delete/{id}', 'ValueAddedResellerController@destroy');
    Route::post('/value_added_resellers/create', 'ValueAddedResellerController@create');

    Route::get('/logout', 'UserController@logOut');


    Route::get('/map', function () {
        return view('map');
    });

    /******************************
     * LEADS
     ******************************/

    Route::get('/customers', 'LeadController@customers');
    Route::get('/leads/service_types', 'LeadController@serviceTypes');
    Route::get('/leads/payment_types', 'LeadController@paymentTypes');
    Route::post('/leads', 'LeadController@store');
    Route::get('/leads', 'LeadController@index');

    /******************************
     * Statements
     ******************************/
    Route::get('/statements', 'Merchant\StatementController@getStatementsPaginated');
    Route::get('/statements/export', 'Merchant\StatementController@exportStatements');

    Route::get('user/login_as/{user_id}', 'UserController@logInAs');
    /******************************
     * Payment Services
     ******************************/
    Route::get('/merchant/payment', 'Merchant\PaymentController@getPaymentInformation');
    Route::post('/merchant/payment/business_information', 'Merchant\PaymentController@updateBusinessInformation');
    Route::post('/merchant/payment/owner_information', 'Merchant\PaymentController@updateOwnerInformation');
    Route::post('/merchant/payment/owner/upload', 'Merchant\PaymentController@uploadOwnerDocuments');
    Route::get('/merchant/payment/daily_deposit_report', 'Merchant\PaymentController@dailyDepositReport');

    /******************************
     * Loyalty
     ******************************/
    Route::get('/brands/{brand_id}/loyalty', 'Marketing\LoyaltyController@getBrandConfiguration');
    Route::post('/brands/{brand_id}/loyalty', 'Marketing\LoyaltyController@setBrandConfiguration');
    Route::post('/brands/{brand_id}/bonus_points_day', 'Marketing\LoyaltyController@setBonusPointsDayConfiguration');
    Route::delete('/brands/{brand_id}/bonus_points_day/{bonus_id}', 'Marketing\LoyaltyController@deleteBonusPointsDayConfiguration');
    Route::post('/loyalty/upload/logo', 'Marketing\LoyaltyController@uploadLogo');
    Route::post('/loyalty/status', 'Marketing\LoyaltyController@setLoyaltyStatus');
});

Route::get('/error_clear_session', 'UserController@flushEntireExistingSession');

//SendMail routes
Route::resource('/sendmail', 'MailController@index');
Route::post('/mail', 'MailController@sendMail');

//Route to Load Edit Merchant Forms for Angular
Route::get('merchant_form/{form}', function ($form) {
    return view('edit_merchant_forms.' . $form);
});
//Route to Load User Forms for Angular
Route::get('user_form/{form}', function ($form) {
    return view('user_permissions_forms.' . $form);
});


//Offer routes
Route::resource('offers', 'OfferController', ['except' => [
    'create', 'edit'
]]);
//Route to Customers/Leads
Route::get('/leads/{guid}', 'LeadController@show');

//Route to Customers/Prospects
Route::post('/prospects/{leadGuid}/merchants', 'ProspectController@createMerchant');
Route::post('/prospects/{leadGuid}/customer', 'ProspectController@setupCustomer');
Route::post('/prospects/{leadGuid}/menu_files', 'ProspectController@uploadMenu');

Route::get('/test_env_variables', function () {
        echo 'ENV VARIABLE APP_KEY: '.env('APP_KEY');
});

Route::get('user/session_info', 'UserController@getSession');
Route::get('user/manual_change_password', 'UserController@manualChangeUserPassword');

//Lookup Routes
Route::get('/lookup/{type_id_field}', 'LookupController@index');
Route::post('/multi_lookup', 'LookupController@multiple');

//This changes the way Blade renders variables so it does not interfere/cause errors with Angular
\Blade::setContentTags('<%', '%>');
\Blade::setEscapedContentTags('<%%', '%%>');

Route::get('testmail', function () {
    return view('reset.emailer', ['link' => 'http://localhost:8081']);
});