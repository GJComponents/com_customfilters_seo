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
--

DROP TABLE IF EXISTS `#__cf_customfields_setting_seo`;
--
-- Структура таблицы `#__cf_customfields_setting_seo`
--
-- Создание: Окт 10 2022 г., 07:15
-- Последнее обновление: Окт 10 2022 г., 10:33
--

DROP TABLE IF EXISTS `#__cf_customfields_setting_seo`;
CREATE TABLE `#__cf_customfields_setting_seo` (
     `id` int(11) NOT NULL AUTO_INCREMENT ,
     `vmcategory_id` int(11) NOT NULL,
     `url_params` varchar(512) NOT NULL,
     `url_params_hash` varchar(255) NOT NULL,
     `sef_url` varchar(512) NOT NULL,
     `no_ajax` int(1) NOT NULL,
     `sef_filter_title` text NOT NULL,
     `sef_filter_description` text NOT NULL,
     `sef_filter_keywords` text NOT NULL,
     `selected_filters_table` text NOT NULL,
     `published` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `#__cf_customfields_setting_seo`
--
ALTER TABLE `#__cf_customfields_setting_seo`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `url_params_hash` (`url_params_hash`);


COMMIT;

