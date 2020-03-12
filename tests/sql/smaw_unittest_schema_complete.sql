ALTER TABLE `Merchant` ADD `tip_minimum_percentage` INT(11) NOT NULL DEFAULT '0' AFTER `show_tip`, ADD `tip_minimum_trigger_amount` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `tip_minimum_percentage`;
UPDATE Merchant SET `tip_minimum_percentage` = 10, `tip_minimum_trigger_amount` = 50.00 WHERE brand_id = 150;
INSERT INTO `Brand2` VALUES(430, 'Goodcents Subs', 'Y', 'N', 'N', NULL, 'apiuser', 'password', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'N', 'Y');


CREATE TABLE `adm_merchant_phone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merchant_id` varchar(14) NOT NULL,
  `name` varchar(254) DEFAULT NULL,
  `title` varchar(254) DEFAULT NULL,
  `phone_no` varchar(10) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `logical_delete` varchar(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  UNIQUE KEY `merchant_phone` (`merchant_id`,`phone_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='To record secondary phones associated with a Merchant Location';

CREATE TABLE `adm_w9` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) DEFAULT NULL,
  `filing_name` varchar(100) DEFAULT NULL,
  `dba_name` varchar(100) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` char(2) NOT NULL,
  `zip` char(5) NOT NULL,
  `EIN_SS` varchar(11) NOT NULL,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  `logical_delete` char(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  UNIQUE KEY `merchant_id_UNIQUE` (`merchant_id`),
  CONSTRAINT `FK_Merchant_id` FOREIGN KEY (`merchant_id`) REFERENCES `Merchant` (`merchant_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3891 DEFAULT CHARSET=latin1;