#Load Admin Portal Roles
INSERT INTO `portal_roles` (`id`, `name`, `description`, `created_at`, `updated_at`)
VALUES
	(1, 'yourbiz Super User', 'Cary\'s Team, Kendall\'s Team, yourbiz Ops', NULL, NULL),
	(2, 'Partner Admin', 'Epson', NULL, NULL),
	(3, 'Reseller Account Manager', 'Epson VAR', NULL, NULL),
	(5, 'Store Owner Operator', 'Lindsay at Lindsay\'s Deli', NULL, NULL),
	(6, 'Store Manager', 'A manager working under a store owner/operator, with limited permissions', NULL, NULL),
	(7, 'Multi-Location Owner Operator', 'This is an operator that manages multiple locations', NULL, NULL),
	(8, 'Brand Manager', 'Arielle at Goodcents', NULL, NULL),
	(9, 'Store Associate', 'A merchant employee who only managers orders', NULL, NULL);

#Load Admin Portal Permissions
INSERT INTO `portal_permissions` (`id`, `name`, `code`, `description`, `created_at`, `updated_at`)
VALUES
	(100, 'Accounts Navigation', 'accounts_nav', 'The side navigation link to the accounts screen, which lets you search for different merchants.', NULL, NULL),
	(101, 'Customer Service Navigation', 'cs_nav', 'The side navigation to access the Customer Service Screen.', NULL, NULL),
	(102, 'Operator Home', 'op_nav', 'The side navigation for the operator home screen', NULL, NULL),
	(103, 'Full Menu Navigation to Menu Search', 'menu_full_nav', 'This is the side navigation to send a user to the Menu Search/Home Screen.', NULL, NULL),
	(104, 'Full Menu Navigation Directly to Menu', 'menu_full_direct_nav', 'This is the side navigation to send a user directly to the Full Menu Edit for the Menu that their store/merchant is linked to.', NULL, NULL),
	(105, 'Quick Menu Navigation', 'menu_quick_nav', 'This is the quick menu nav that will take a user to the quick menu edit for their store.', NULL, NULL),
	(106, 'Marketing Navigation', 'marketing_nav', 'The link to the Marketing screen on the Side Nav where Promos are made.', NULL, NULL),
	(107, 'Sites/Creative Navigation', 'sites_nav', 'The side nav link to the sites/creative screen.', NULL, NULL),
	(108, 'Reports Navigation', 'reports_nav', 'This is the side nav link for the Reports page.', NULL, NULL),
	(109, 'Order Management Navigation', 'order_mgmt_nav', 'This is the side nav link for the Order Management screen used by Operators', NULL, NULL),
	(110, 'Brand Navigation', 'brand_nav', 'This is the brand nav link that lets a user create brands', NULL, NULL),
	(111, 'Show Live Orders', 'show_live_orders', 'This determines whether live orders appears.', NULL, NULL),
	(112, 'Operator Settings Page', 'operator_settings', 'This determines whether to display the Opeator Settings Page', NULL, NULL),
	(113, 'Customer Service Users', 'customer_service_users', 'This determines whether one can view the customer service users page.', NULL, NULL),
	(114, 'Manage Value Added Reseller', 'mng_vars', 'This determines whether a user can manage value added resellers.', NULL, NULL),
	(115, 'Multi Location Operator Merchant Select', 'op_merch_select', 'This allows a multiple location operator to change merchants in app.', NULL, NULL),
	(116, 'Brand Direct Nav', 'brand_direct_nav', 'This navigation is for a brand manager to be able to directly access Brand Edit', NULL, NULL),
	(117, 'Manage Users', 'mng_usrs', 'The ability to manage users', NULL, NULL),
	(118, 'Home Navigation', 'home_nav', 'The ability to have the Home Navigation', NULL, NULL),
	(119, 'Ordering On Off Navigation', 'order_on_off', 'The ability to have the ordering on/off navigation', NULL, NULL),
	(120, 'Nutrition Navigation', 'nutrition', 'The ability to access the Menu Nutrition Grid', NULL, NULL),
	(121, 'Parking Lot Users', 'pk_lot_users', 'The ability to access the parking lot users page.', NULL, NULL),
	(122, 'Future Orders Screen', 'future_orders', 'The ability access the Future Orders Screen', NULL, NULL),
	(123, 'Loyalty Screen', 'loyalty', 'The ability to access the Loyalty Tab under Marketing.', NULL, NULL),
	(150, 'Brands Filtering/Searching', 'brands_filter', 'Brand Filtering and Searching for Reports', NULL, NULL),
	(151, 'Filtering on Multiple Merchants Functionality', 'multi_merchs_filter', 'The ability to filter on multiple merchants.', NULL, NULL),
	(152, 'Create Super User', 'create_super_user', 'The ability to create a yourbiz Super User', NULL, NULL),
	(153, 'Create Partner Admin', 'create_ptnr_admin', 'The ability to create a Reseller Partner Admin', NULL, NULL),
	(154, 'Create Reseller Account Manager', 'create_var_acct_mngr', 'This is the ability to create a Reseller Account Manager', NULL, NULL),
	(155, 'Create Store Owner Operator', 'create_owner_oper', 'This is the ability to create a store owner operator', NULL, NULL),
	(156, 'Create Store Manager', 'create_store_mngr', 'This is the ability to create a store manager.', NULL, NULL),
	(157, 'Create Brand Manager', 'create_brand_mngr', 'This is the ability to create a brand manager', NULL, NULL),
	(158, 'Show Live Orders', 'show_live_orders', 'This determines whether live orders appears.', NULL, NULL),
	(159, 'Create User Organization Select', 'create_usr_org_select', 'This allows a user to select members of an Organization', NULL, NULL),
	(160, 'Menu Page Onload List', 'onload_menu_list', 'This loads a list of all visibile menus for a user when they go to the Menu Page.', NULL, NULL),
	(161, 'Accounts Page Onload List', 'onload_accounts_list', 'This loads a list of all visible accounts on the Merchant page for a user.', NULL, NULL),
	(162, 'Create Multi Location Owner Operator', 'create_multi_loc_op', 'The ability to create a Multi Location Opreator User.', NULL, NULL),
	(163, 'Create Store Associate', 'create_store_assct', 'The ability to create a Store Associate', NULL, NULL),
	(164, 'Edit Customer Service User', 'cst_srv_usr_edit', 'The ability to edit a customer service user in the Edit User Screen', NULL, NULL),
	(165, 'Log In as User', 'login_as_user', 'The ability to log in as another user. This should only be given to super user for debugging ', NULL, NULL),
	(166, 'Set the default loyalty user account', 'set_default_loyalty', 'Within the user screen, this button sets the default loyalty user account for a brand.', NULL, NULL),
	(167, 'Edit Promo', 'edit_promo', 'The ability to create or edit a promo.', NULL, NULL),
	(168, 'Change User Role', 'change_user_role', 'This is thet ability to be able to change a user\'s role.', NULL, NULL);



#Insert Portal Roles Permissions Map
INSERT INTO `portal_permissions_roles_map` (`id`, `permission_id`, `role_id`)
VALUES
	(1, 100, 1),
	(2, 100, 2),
	(3, 100, 3),
	(4, 100, 8),
	(5, 101, 1),
	(6, 101, 8),
	(7, 102, 5),
	(8, 102, 6),
	(9, 102, 7),
	(10, 103, 1),
	(11, 103, 2),
	(12, 103, 3),
	(13, 103, 8),
	(14, 105, 5),
	(15, 105, 6),
	(16, 105, 7),
	(17, 106, 1),
	(18, 106, 2),
	(19, 106, 3),
	(20, 106, 8),
	(21, 106, 5),
	(22, 106, 7),
	(23, 107, 1),
	(24, 107, 2),
	(25, 107, 3),
	(26, 108, 1),
	(27, 108, 2),
	(28, 108, 3),
	(29, 108, 8),
	(30, 108, 5),
	(31, 108, 7),
	(32, 109, 5),
	(33, 109, 6),
	(34, 109, 7),
	(35, 109, 9),
	(36, 110, 1),
	(37, 110, 2),
	(38, 110, 3),
	(39, 111, 1),
	(40, 112, 5),
	(41, 112, 7),
	(42, 113, 1),
	(43, 114, 1),
	(44, 115, 7),
	(45, 116, 8),
	(46, 117, 1),
	(47, 117, 2),
	(48, 117, 3),
	(49, 117, 8),
	(50, 117, 5),
	(51, 117, 6),
	(52, 117, 7),
	(53, 118, 1),
	(54, 118, 2),
	(55, 118, 3),
	(56, 118, 8),
	(57, 118, 5),
	(58, 118, 6),
	(59, 118, 7),
	(60, 119, 5),
	(61, 119, 6),
	(62, 150, 1),
	(63, 150, 2),
	(64, 150, 3),
	(65, 151, 1),
	(66, 151, 2),
	(67, 151, 3),
	(68, 151, 8),
	(69, 151, 7),
	(70, 152, 1),
	(71, 153, 1),
	(72, 154, 1),
	(73, 154, 2),
	(74, 155, 1),
	(75, 155, 2),
	(76, 155, 3),
	(77, 155, 8),
	(78, 155, 7),
	(79, 156, 1),
	(80, 156, 2),
	(81, 156, 3),
	(82, 156, 8),
	(83, 156, 5),
	(84, 156, 7),
	(85, 157, 1),
	(86, 157, 2),
	(87, 157, 3),
	(88, 158, 1),
	(89, 158, 2),
	(90, 158, 3),
	(91, 159, 1),
	(92, 160, 1),
	(93, 160, 2),
	(94, 160, 3),
	(95, 160, 8),
	(96, 161, 1),
	(97, 161, 2),
	(98, 161, 3),
	(99, 161, 8),
	(100, 162, 1),
	(101, 162, 2),
	(102, 162, 3),
	(103, 162, 8),
	(104, 163, 1),
	(105, 163, 2),
	(106, 163, 3),
	(107, 163, 8),
	(108, 163, 5),
	(109, 163, 6),
	(110, 113, 8),
	(111, 164, 1),
	(112, 165, 1),
	(114, 120, 1),
	(115, 120, 2),
	(116, 120, 3),
	(117, 120, 8),
	(118, 120, 8),
	(119, 111, 8),
	(120, 166, 8),
	(121, 121, 1),
	(122, 121, 8),
	(123, 122, 1),
	(124, 122, 8),
	(125, 167, 1),
	(126, 167, 2),
	(127, 167, 3),
	(128, 167, 8),
	(130, 123, 1),
	(131, 123, 2),
	(132, 123, 3),
	(133, 123, 8),
	(134, 168, 1);

alter table Brand2 add column production varchar(255) default 'Y';
alter table Brand2 add column support_email varchar(255) default NULL;

alter table Merchant add column time_zone_string varchar(255);

INSERT INTO `portal_organizations` (`id`, `name`, `description`, `created_at`, `updated_at`)
VALUES
	(1, 'yourbiz', 'This is the yourbiz organizations', NULL, NULL),
	(2, 'Sodexo', 'Sodexo - Quality of Life Services', NULL, NULL);

INSERT INTO `portal_email_types` (`id`, `type`, `created_at`, `updated_at`)
VALUES
	(1, 'generic_email', '2017-11-23 19:21:19', '2017-11-23 19:21:19'),
	(2, 'reseller_form_1', '2017-11-23 19:21:19', '2017-11-23 19:21:19'),
	(3, 'merchant_form_1', '2017-11-23 19:21:19', '2017-11-23 19:21:19'),
	(4, 'merchant_form_2', '2017-11-23 19:21:19', '2017-11-23 19:21:19'),
	(5, 'reseller_form_1_reminder', '2017-11-23 19:21:19', '2017-11-23 19:21:19'),
	(6, 'merchant_form_1_reminder', '2017-11-23 19:21:19', '2017-11-23 19:21:19');

INSERT INTO `portal_email_templates` (`id`, `key`, `sender`, `sender_name`, `cc`, `bcc`, `subject`, `message`, `email_type_id`, `created_at`, `updated_at`)
VALUES
	(1, 'sign_up_series_email_1', 'support@yourcompany.com', 'Splick.it Team', NULL, NULL, 'Ready to set up your online ordering?', '<p>Ready to get online in days, not months?</p>\n<p>Our full-featured, easy to manage ordering platform helps grow your business while maintaining the direct customer relationships you value.</p>\n<p>Just a few easy steps to go live and begin accepting online orders, with all the great features your customers have come to expect.</p>', 2, '2017-12-27 11:30:22', '2017-12-27 11:30:22'),
	(2, 'sign_up_series_reminder_1', 'support@yourcompany.com', 'Splick.it Team', NULL, NULL, 'Get online fast with Splick.it Digital Ordering', '<p>It’s easy as 1, 2, 3 to sign up today.</p>\n    <table>\n        <tr><td style=\"width: 10px\"></td>\n            <td><img src=\"https://d38o1hjtj2mzwt.cloudfront.net/admin_portal/merchant_form_1/img/email/step-1.png\" style=\"width: 100%; max-width: 100%;\"></td>\n            <td style=\"width: 4px\"></td>\n            <td style=\"font-weight: 300\">Select your online ordering subscription</td></tr>\n        <tr><td style=\"width: 10px\"></td>\n            <td><img src=\"https://d38o1hjtj2mzwt.cloudfront.net/admin_portal/merchant_form_1/img/email/step-2.png\" style=\"width: 100%; max-width: 100%;\"></td>\n            <td style=\"width: 4px\"></td>\n            <td style=\"font-weight: 300\">Provide a little information</td></tr>\n        <tr><td style=\"width: 10px\"></td>\n            <td><img src=\"https://d38o1hjtj2mzwt.cloudfront.net/admin_portal/merchant_form_1/img/email/step-3.png\" style=\"width: 100%; max-width: 100%;\"></td>\n            <td style=\"width: 4px\"></td>\n            <td style=\"font-weight: 300\">We’ll take care of the rest!</td></tr>\n    </table>\n    <p >In just a few days you can start increasing your orders with great features like:</p>\n    <table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\" style=\"background: #F6D268; width: 100%; border: 1px #736256 solid;border-collapse:collapse !important;\n        mso-table-lspace:0pt; mso-table-rspace:0pt;\">\n        <tr ><td height=\"6\" colspan=\"6\" style=\"height: 6px; line-height: 0; font-size: 0\">&nbsp;</td></tr>\n        <tr>\n            <td style=\"width: 20px\"></td>\n            <td> \n                <table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\" style=\"width: 100%\">\n                    <tr><td style=\"text-align: center\" align=\"center\"><b style=\"font-weight: bold;\">Branded Site</b></td></tr>\n                    <tr><td style=\"text-align: center\" align=\"center\"><b style=\"font-weight: bold;\">Promos</b></td></tr>\n                    <tr><td style=\"text-align: center\" align=\"center\"><b style=\"font-weight: bold;\">Loyalty</b></td></tr>\n                    <tr><td style=\"text-align: center\" align=\"center\"><b style=\"font-weight: bold;\">Upsells</b></td></tr>\n                </table>\n            </td>\n            <td style=\"width: 10px\"></td>\n            <td style=\"width: 10px\"></td>\n            <td>\n                <table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\" style=\"width: 100%\">\n                    <tr><td style=\"text-align: center\" align=\"center\"><b style=\"font-weight: bold;\">Reorder</b></td></tr>\n                    <tr><td style=\"text-align: center\" align=\"center\"><b style=\"font-weight: bold;\">Catering</b></td></tr>\n                    <tr><td style=\"text-align: center\" align=\"center\"><b style=\"font-weight: bold;\">Group Orders</b></td></tr>\n                    <tr><td style=\"text-align: center\" align=\"center\"><b style=\"font-weight: bold;\">Advance Orders</b></td></tr>\n                </table>\n            </td>\n            <td style=\"width: 20px\"></td>\n        </tr>\n        <tr ><td height=\"6\" colspan=\"6\" style=\"height: 6px; line-height: 0; font-size: 0\">&nbsp;</td></tr>\n    </table>', 5, '2017-12-27 11:30:22', '2017-12-27 11:30:22'),
	(3, 'sign_up_series_reminder_2', 'support@yourcompany.com', 'Splick.it Team', NULL, NULL, 'Too busy? We’re here to help.', '<p>No one’s busier than a restaurant owner and their staff. That’s why we’ve made it so easy to set up your own branded digital ordering fast.</p>\n<p>Save time by managing your business with our convenient Dashboard including menu changes, receiving and managing live orders, creating promotions and much more. And your reporting Dashboard provides a real time look into how your sales are going and what you can do to optimize revenue.</p>\n<p>Keep customers coming back and ordering more each time, with all the great features they expect from online ordering.</p>', 5, '2017-12-27 11:30:22', '2017-12-27 11:30:22'),
	(4, 'sign_up_series_reminder_3', 'support@yourcompany.com', 'Splick.it Team', NULL, NULL, 'Your branded online ordering solution is waiting for you', '<p>We know you’re busy, so this will be our final reminder. If you’re still interested in our easy to launch and manage digital ordering platform, take action soon.</p>\n<p>Start reaping the benefits of greater sales and profit right away with your branded ordering solution.</p>\n<p>Feel free to reach out to us any time if you’d like more information, or help getting started.</p>', 5, '2017-12-27 11:30:22', '2017-12-27 11:30:22'),
	(5, 'welcome_series_email_1', 'support@yourcompany.com', 'Splick.it Team', NULL, NULL, 'Welcome to Splick.it Digital Ordering!', '<p>Thanks for taking the first step.</p>\r<p>Now we need a little information about your restaurant’s ordering preferences and a copy of your menu. When you’re ready, click the button below.</p>\r', 3, '2017-12-22 16:11:19', '2017-12-22 16:11:19'),
	(6, 'welcome_series_reminder_1', 'support@yourcompany.com', 'Splick.it Team', NULL, NULL, 'Let’s set up your Splick.it Digital Ordering', '<p>Thanks for signing up for our Digital Ordering Platform.</p>\r<p>Click below to fill out a simple form and upload your menu. Then we’ll get you live and accepting orders in no time.</p>', 6, '2017-12-22 17:32:22', '2017-12-22 17:32:22'),
	(7, 'welcome_series_reminder_2', 'support@yourcompany.com', 'Splick.it Team', NULL, NULL, 'Your digital ordering platform awaits', '<p>Don’t forget to finish the setup process for your new online ordering service.</p>\r<p>Click below to get started.</p>', 6, '2017-12-22 17:32:22', '2017-12-22 17:32:22'),
	(8, 'welcome_series_reminder_3', 'support@yourcompany.com', 'Splick.it Team', NULL, NULL, 'Did you forget something?', '<p>Thanks again for signing up. We need a little more information so we can set up your ordering website.</p>\r<p>Please click the link below to fill out a simple form and upload your menu.</p>\n<p>If you need assistance, please reach out to us at <a style=\"text-decoration:none;\" href=\"mailto:support@yourcompany.com\">support@yourcompany.com</a>, <br/>or call us at <a style=\"text-decoration:none;\" href=\"tel:1-800-775-4254\">1-800-775-4254</a>.</p>', 6, '2017-12-22 17:32:22', '2017-12-22 17:32:22'),
	(12, 'welcome_user_email', 'support@yourcompany.com', 'Splick.it Team', NULL, NULL, 'Welcome!, Configure your new account.', '<p>Welcome to the yourbiz Digital Ordering Platform! In order to begin using your new account, please click the button below and follow instructions to set your password. This link will expire in one hour.</p>', 1, '2018-06-22 17:32:22', '2018-06-22 17:32:22');

CREATE OR REPLACE VIEW `smawv_promo_first_keyword`
AS SELECT
   min(`Promo_Key_Word_Map`.`map_id`) AS `min(map_id)`,
   `Promo_Key_Word_Map`.`promo_id` AS `promo_id`,
   `Promo_Key_Word_Map`.`promo_key_word` AS `promo_key_word`,
   `Promo_Key_Word_Map`.`brand_id` AS `brand_id`
FROM `Promo_Key_Word_Map` group by `Promo_Key_Word_Map`.`promo_id`;

#Create Super User
INSERT INTO `portal_users` (`id`, `first_name`, `last_name`, `email`, `password`, `remember_token`, `token`, `visibility`, `organization_id`, `created_at`, `updated_at`, `logical_delete`)
VALUES
	(1, 'SuperUser', 'Tester', 'SuperUser.Tester@yourcompany.com', '$2y$10$GSdkkGwzWNwq6qQKKXZlCu3dyZ8wEKTfu/qLSGKqXJdPPx0zjHILO', 'sOdqdrhRlj', '', 'global', 1, '2019-12-31 16:16:21', '2019-12-31 16:16:21', 'N');

INSERT INTO `portal_role_user_map` (`id`, `user_id`, `role_id`)
VALUES
	(1, 1, 1);

#Create Brand Manager
INSERT INTO `portal_users` (`id`, `first_name`, `last_name`, `email`, `password`, `remember_token`, `token`, `visibility`, `organization_id`, `created_at`, `updated_at`, `logical_delete`)
VALUES
	(2, 'BrandManager', 'Tester', 'BrandManager.Tester@yourcompany.com', '$2y$10$GSdkkGwzWNwq6qQKKXZlCu3dyZ8wEKTfu/qLSGKqXJdPPx0zjHILO', 'sOdqdrhRlj', '', 'brand', 1, '2019-12-31 16:16:21', '2019-12-31 16:16:21', 'N');

INSERT INTO `portal_role_user_map` (`user_id`, `role_id`)
VALUES
	(2, 8);

INSERT INTO `portal_brand_manager_brand_map` (`user_id`, `brand_id`)
VALUES
	(2, 150);


#Create Owner Operator
INSERT INTO `portal_users` (`id`, `first_name`, `last_name`, `email`, `password`, `remember_token`, `token`, `visibility`, `organization_id`, `created_at`, `updated_at`, `logical_delete`)
VALUES
	(3, 'OwnerOperator', 'Tester', 'OwnerOperator.Tester@yourcompany.com', '$2y$10$GSdkkGwzWNwq6qQKKXZlCu3dyZ8wEKTfu/qLSGKqXJdPPx0zjHILO', 'sOdqdrhRlj', '', 'operator', 1, '2019-12-31 16:16:21', '2019-12-31 16:16:21', 'N');

INSERT INTO `Merchant` (`merchant_id`, `merchant_user_id`, `merchant_external_id`, `numeric_id`, `alphanumeric_id`, `rewardr_programs`, `shop_email`, `brand_id`, `name`, `display_name`, `address1`, `address2`, `city`, `state`, `zip`, `country`, `lat`, `lng`, `EIN_SS`, `description`, `phone_no`, `fax_no`, `twitter_handle`, `time_zone`, `cross_street`, `trans_fee_type`, `trans_fee_rate`, `show_tip`, `tip_minimum_percentage`, `tip_minimum_trigger_amount`, `donate`, `cc_processor`, `merchant_type`, `active`, `ordering_on`, `billing`, `inactive_reason`, `show_search`, `lead_time`, `immediate_message_delivery`, `delivery`, `advanced_ordering`, `order_del_type`, `order_del_addr`, `order_del_addr2`, `payment_cycle`, `minimum_iphone_version`, `minimum_android_version`, `live_dt`, `facebook_caption_link`, `custom_order_message`, `custom_menu_message`, `created`, `modified`, `logical_delete`, `group_ordering_on`, `time_zone_string`)
VALUES
	(1, 0, '0', 987654321, '987654ABCXYZ', NULL, 'System Default', 150, 'Boulder Snarfs', 'System Default', 'System Default', 'System Default', 'System Default', 'CO', '', 'US', 0.000000, 0.000000, NULL, '', '', '', NULL, '', '', 'F', 0.25, 'Y', 0, 0.00, 0, 'I', 'I', 'N', 'Y', 'N', NULL, 'Y', 0, 'N', 'N', 'N', '0', '9999999999', NULL, 'W', 2.041, 2.040, '0000-00-00', NULL, NULL, NULL, '0000-00-00 00:00:00', '2019-12-31 17:16:31', 'N', 0, NULL);

INSERT INTO `Skin_Merchant_Map` (`skin_id`, `merchant_id`, `created`, `modified`, `logical_delete`)
VALUES
	(5, 1, '2011-11-21 07:00:00', '2011-11-21 07:00:00', 'N');


INSERT INTO `portal_role_user_map` (`user_id`, `role_id`)
VALUES
	(3, 5);

INSERT INTO `portal_operator_merchant_map` (`user_id`, `merchant_id`)
VALUES
	(3, 1);

INSERT INTO `portal_object_ownership` (`user_id`, `organization_id`, `object_type`, `object_id`, `created_at`, `updated_at`)
VALUES
	(3, 1, 'merchant', 1, '2018-01-17 18:08:06', '2018-01-17 18:08:06');

#Create Multi Location Operator
INSERT INTO `portal_users` (`id`, `first_name`, `last_name`, `email`, `password`, `remember_token`, `token`, `visibility`, `organization_id`, `created_at`, `updated_at`, `logical_delete`)
VALUES
	(5, 'MultiLocOperator', 'Tester', 'MultiLocOperator.Tester@yourcompany.com', '$2y$10$GSdkkGwzWNwq6qQKKXZlCu3dyZ8wEKTfu/qLSGKqXJdPPx0zjHILO', 'sOdqdrhRlj', '', 'operator', 1, '2019-12-31 16:16:21', '2019-12-31 16:16:21', 'N');

INSERT INTO `Merchant` (`merchant_id`, `merchant_user_id`, `merchant_external_id`, `numeric_id`, `alphanumeric_id`, `rewardr_programs`, `shop_email`, `brand_id`, `name`, `display_name`, `address1`, `address2`, `city`, `state`, `zip`, `country`, `lat`, `lng`, `EIN_SS`, `description`, `phone_no`, `fax_no`, `twitter_handle`, `time_zone`, `cross_street`, `trans_fee_type`, `trans_fee_rate`, `show_tip`, `tip_minimum_percentage`, `tip_minimum_trigger_amount`, `donate`, `cc_processor`, `merchant_type`, `active`, `ordering_on`, `billing`, `inactive_reason`, `show_search`, `lead_time`, `immediate_message_delivery`, `delivery`, `advanced_ordering`, `order_del_type`, `order_del_addr`, `order_del_addr2`, `payment_cycle`, `minimum_iphone_version`, `minimum_android_version`, `live_dt`, `facebook_caption_link`, `custom_order_message`, `custom_menu_message`, `created`, `modified`, `logical_delete`, `group_ordering_on`, `time_zone_string`)
VALUES
	(3, 0, '0', 987654221, '987654ABC3YZ', NULL, 'System Default', 150, 'Longmont Snarfs', 'System Default', 'System Default', 'System Default', 'System Default', 'CO', '', 'US', 0.000000, 0.000000, NULL, '', '', '', NULL, '', '', 'F', 0.25, 'Y', 0, 0.00, 0, 'I', 'I', 'N', 'Y', 'N', NULL, 'Y', 0, 'N', 'N', 'N', '0', '9999999999', NULL, 'W', 2.041, 2.040, '0000-00-00', NULL, NULL, NULL, '0000-00-00 00:00:00', '2019-12-31 17:16:31', 'N', 0, NULL);

INSERT INTO `Skin_Merchant_Map` (`skin_id`, `merchant_id`, `created`, `modified`, `logical_delete`)
VALUES
	(5, 3, '2011-11-21 07:00:00', '2011-11-21 07:00:00', 'N');


INSERT INTO `portal_role_user_map` (`user_id`, `role_id`)
VALUES
	(5, 7);

INSERT INTO `portal_operator_merchant_map` (`user_id`, `merchant_id`)
VALUES
	(5, 3);

INSERT INTO `portal_operator_merchant_map` (`user_id`, `merchant_id`)
VALUES
	(5, 1);

INSERT INTO `portal_object_ownership` (`user_id`, `organization_id`, `object_type`, `object_id`, `created_at`, `updated_at`)
VALUES
	(5, 1, 'merchant', 3, '2018-01-17 18:08:06', '2018-01-17 18:08:06');

#Create PartnerAdmin Reseller
INSERT INTO `portal_users` (`id`, `first_name`, `last_name`, `email`, `password`, `remember_token`, `token`, `visibility`, `organization_id`, `created_at`, `updated_at`, `logical_delete`)
VALUES
	(4, 'PartnerAdmin', 'Tester', 'PartnerAdmin.Tester@yourcompany.com', '$2y$10$GSdkkGwzWNwq6qQKKXZlCu3dyZ8wEKTfu/qLSGKqXJdPPx0zjHILO', 'sOdqdrhRlj', '', 'all', 1, '2019-12-31 16:16:21', '2019-12-31 16:16:21', 'N');

INSERT INTO `portal_role_user_map` (`user_id`, `role_id`)
VALUES
	(4, 2);
