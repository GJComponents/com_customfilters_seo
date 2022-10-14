DROP TABLE IF EXISTS
    `#__cf_customfields_setting_seo`;
CREATE TABLE `#__cf_customfields_setting_seo`(
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `vmcategory_id` INT(11) NOT NULL,
    `url_params` VARCHAR(512) NOT NULL,
    `url_params_hash` VARCHAR(255) NOT NULL,
    `sef_url` VARCHAR(512) NOT NULL,
    `no_ajax` INT(1) NOT NULL,
    `sef_filter_title` TEXT NOT NULL,
    `sef_filter_description` TEXT NOT NULL,
    `sef_filter_keywords` TEXT NOT NULL,
    `selected_filters_table` TEXT NOT NULL,
    `published` INT(1) NOT NULL DEFAULT '1',
    PRIMARY KEY(`id`),
    UNIQUE `url_params_hash`(`url_params_hash`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
 