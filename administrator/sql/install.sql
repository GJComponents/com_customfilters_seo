CREATE TABLE IF NOT EXISTS `#__cf_customfields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) NOT NULL,
  `vm_custom_id` int(11) NOT NULL COMMENT 'is the key to the custom field id ',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
    `on_seo` int(11) NOT NULL DEFAULT '1' COMMENT 'исключение из seo',
  `type_id` varchar(12) NOT NULL DEFAULT '3' COMMENT 'The display type',
  `order_by` varchar(64) NOT NULL DEFAULT 'custom_title' COMMENT 'The way that the values will be displayed',
  `order_dir` varchar(12) NOT NULL DEFAULT 'ASC' COMMENT 'the direction',
  `params` text NOT NULL,
   `data_type` varchar(12) NOT NULL DEFAULT 'string',
  PRIMARY KEY (`id`),
  UNIQUE KEY `virtuemart_custom_id` (`vm_custom_id`),
  KEY `type_id` (`type_id`)
) DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `#__cf_customfields_setting_seo`
-- Создание: 14.10.22-14:33
--
DROP TABLE IF EXISTS `#__cf_customfields_setting_seo`;
CREATE TABLE `#__cf_customfields_setting_seo` (
     `id` int(11) NOT NULL,
     `vmcategory_id` int(11) NOT NULL,
     `url_params` varchar(512) NOT NULL,
     `url_params_hash` varchar(255) NOT NULL,
     `sef_url` varchar(512) NOT NULL,
     `sef_url_hash` varchar(255) NOT NULL,
     `no_index` int(1) NOT NULL DEFAULT '0' COMMENT 'закрыто от индекса',
     `no_ajax` int(1) NOT NULL,
     `sef_filter_h_tag` text NOT NULL,
     `sef_filter_vm_cat_description` text NOT NULL,
     `sef_filter_title` text NOT NULL,
     `sef_filter_description` text NOT NULL,
     `sef_filter_keywords` text NOT NULL,
     `selected_filters_table` text NOT NULL,
     `published` int(1) NOT NULL DEFAULT '1',
     `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `#__cf_customfields_setting_seo`
--
ALTER TABLE `#__cf_customfields_setting_seo`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `url_params_hash` (`url_params_hash`),
  ADD UNIQUE KEY `sef_url_hash` (`sef_url_hash`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `#__cf_customfields_setting_seo`
--
ALTER TABLE `#__cf_customfields_setting_seo`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;





--
-- Структура таблицы `#__cf_customfields_setting_city_category_vm`
--
-- Создание: Ноя 14 2022 г., 18:51
--


CREATE TABLE IF NOT EXISTS `#__cf_customfields_setting_city_category_vm` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_vm_category` int(11) NOT NULL,
    `id_filter_city` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id_vm_category` (`id_vm_category`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
COMMIT;

--
-- Структура таблицы `#__cf_customfields_setting_city`
--
-- Создание: Ноя 11 2022 г., 14:52
--

CREATE TABLE IF NOT EXISTS `#__cf_customfields_setting_city` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `alias` varchar(255) NOT NULL,
    `slug_filter` varchar(255) NOT NULL COMMENT 'системное имя CityFilter',
    `published` int(11) NOT NULL DEFAULT '1',
    `on_seo` int(11) NOT NULL DEFAULT '1',
    `known_languages` varchar(12) NOT NULL DEFAULT '*' ,
    `type_id` varchar(12) NOT NULL DEFAULT '13',
    `params` longtext NOT NULL,
    `params_customs` longtext NOT NULL,
    `statistic` text NOT NULL COMMENT 'статистика данных',
    `data_type` varchar(12) NOT NULL DEFAULT 'string',
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
COMMIT;