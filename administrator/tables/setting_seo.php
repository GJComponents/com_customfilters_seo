<?php
/**
 * Customfilter table
 *
 * @package		Customfilters
 * @since		1.5
 */

// No direct access.
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

/**
 * Table class Setting_seo
 * @author Gartes
 * @since 3.9
 */
class CustomfiltersTableSetting_seo extends Table{
	/**
	 * @var string Имя таблицы этого класса
	 * @since 3.9
	 */
	protected $table = '#__cf_customfields_setting_seo' ;
	/**
     * @var int
     * @since    1.0.0
     */
    public $vmcategory_id = null;

    public $url_params = null ;
    /**
     *
     * @var string
     * @since    1.0.0
     */
    public $url_params_hash = null ;


    /**
     * @var null
     * @since    1.0.0
     */
    public $selected_filters_table = null;
    /**
     *
     * @var string
     * @since    1.0.0
     */
    public $sef_url = null;
    /**
     *
     * @var int
     * @since    1.0.0
     */
    public $published = null;

    /**
     * @var string[]    Массив имен ключей, которые нужно закодировать в формате json -- к примеру для поля "params".
     *                  An array of key names to be json encoded in the bind function
     * @since    1.0.0
     */
    protected $_jsonEncode = array('selected_filters_table');
    /**
	 * Constructor
	 *
	 * @since	1.5
	 */
	function __construct(&$_db)
	{
		parent::__construct($this->table, 'id', $_db);
	}

	/**
	 * Метод сохранения строки в базе данных из свойств экземпляра таблицы.
	 *
	 * Если задано значение первичного ключа, строка с этим значением первичного ключа будет обновлена значениями свойств экземпляра.
	 * Если значение первичного ключа не задано, в базу данных будет вставлена новая строка со свойствами из экземпляра таблицы.
	 *
	 * Method to store a row in the database from the Table instance properties.
	 *
	 * If a primary key value is set the row with that primary key value will be updated with the instance property values.
	 * If no primary key value is set a new row will be inserted into the database with the properties from the Table instance.
	 *
	 * @param   boolean  $updateNulls  Значение true для обновления полей, даже если они пусты.
	 *                                 True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.7.0
	 */
    public function store ( $updateNulls = false ){
        // дополнительные обработки полей таблицы
	    $this->url_params_hash = md5( $this->url_params ) ;
        parent::store( $updateNulls ) ;
    }


}


/*

-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Июл 28 2022 г., 16:08
-- Версия сервера: 5.7.21-20-beget-5.7.21-20-1-log
-- Версия PHP: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- База данных: `bormandm_test`
--

-- --------------------------------------------------------

--
-- Структура таблицы `gtvxq_cf_customfields_setting_seo`
--
-- Создание: Июл 27 2022 г., 02:18
-- Последнее обновление: Июл 28 2022 г., 11:46
--

DROP TABLE IF EXISTS `gtvxq_cf_customfields_setting_seo`;
CREATE TABLE `gtvxq_cf_customfields_setting_seo` (
  `id` int(11) NOT NULL,
  `vmcategory_id` int(11) NOT NULL,
  `url_params` varchar(512) NOT NULL,
  `url_params_hash` varchar(255) NOT NULL,
  `sef_url` varchar(512) NOT NULL,
  `no_ajax` int(1) NOT NULL,
  `sef_filter_title` text NOT NULL,
  `sef_filter_description` text NOT NULL,
  `sef_filter_keywords` text NOT NULL,
  `selected_filters_table` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `gtvxq_cf_customfields_setting_seo`
--
ALTER TABLE `gtvxq_cf_customfields_setting_seo`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `gtvxq_cf_customfields_setting_seo`
--
ALTER TABLE `gtvxq_cf_customfields_setting_seo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;


 * */


