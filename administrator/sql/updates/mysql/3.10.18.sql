DROP TABLE IF EXISTS
    `#__cf_customfields_setting_seo`;
CREATE TABLE `#__cf_customfields_setting_seo`(
     `id` INT(11) NOT NULL AUTO_INCREMENT,
     `vmcategory_id` int(11) NOT NULL,
     `url_params` varchar(512) NOT NULL,
     `url_params_hash` varchar(255) NOT NULL,
     `sef_url` varchar(512) NOT NULL,
     `no_index` int(1) NOT NULL DEFAULT '0' COMMENT 'закрыто от индекса',
     `no_ajax` int(1) NOT NULL,
     `sef_filter_title` text NOT NULL,
     `sef_filter_description` text NOT NULL,
     `sef_filter_keywords` text NOT NULL,
     `selected_filters_table` text NOT NULL,
     `published` int(1) NOT NULL DEFAULT '1',
     PRIMARY KEY(`id`),
     UNIQUE `url_params_hash`(`url_params_hash`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- ALTER TABLE `#__cf_customfields`
--     ADD `known_languages` VARCHAR(12) NOT NULL DEFAULT '*' COMMENT 'Выбор языка для поля' AFTER `on_seo`;

 