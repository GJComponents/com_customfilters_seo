--
-- Структура таблицы `#__cf_customfields_setting_city`
--
-- Создание: Ноя 11 2022 г., 14:52
--

CREATE TABLE IF NOT EXISTS `#__cf_customfields_setting_city` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `alias` varchar(255) NOT NULL,
    `published` int(11) NOT NULL DEFAULT '1',
    `on_seo` int(11) NOT NULL DEFAULT '1',
    `known_languages` varchar(12) NOT NULL ,
    `type_id` varchar(12) NOT NULL DEFAULT '13',
    `params` text NOT NULL,
    `data_type` varchar(12) NOT NULL DEFAULT 'string',
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
COMMIT;

CREATE TABLE IF NOT EXISTS `#__cf_customfields_setting_city_category_vm` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_vm_category` int(11) NOT NULL,
    `id_filter_city` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id_vm_category` (`id_vm_category`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
COMMIT;