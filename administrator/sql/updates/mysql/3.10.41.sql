TRUNCATE TABLE `#__cf_customfields_setting_seo`;
ALTER TABLE `#__cf_customfields_setting_seo` ADD `created_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `published`;
ALTER TABLE `#__cf_customfields_setting_seo` ADD `sef_url_hash` VARCHAR(255) NOT NULL AFTER `sef_url`, ADD UNIQUE `sef_url_hash` (`sef_url_hash`(255));

