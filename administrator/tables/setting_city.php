<?php

/***********************************************************************************************************************
 * ╔═══╗ ╔══╗ ╔═══╗ ╔════╗ ╔═══╗ ╔══╗  ╔╗╔╗╔╗ ╔═══╗ ╔══╗   ╔══╗  ╔═══╗ ╔╗╔╗ ╔═══╗ ╔╗   ╔══╗ ╔═══╗ ╔╗  ╔╗ ╔═══╗ ╔╗ ╔╗ ╔════╗
 * ║╔══╝ ║╔╗║ ║╔═╗║ ╚═╗╔═╝ ║╔══╝ ║╔═╝  ║║║║║║ ║╔══╝ ║╔╗║   ║╔╗╚╗ ║╔══╝ ║║║║ ║╔══╝ ║║   ║╔╗║ ║╔═╗║ ║║  ║║ ║╔══╝ ║╚═╝║ ╚═╗╔═╝
 * ║║╔═╗ ║╚╝║ ║╚═╝║   ║║   ║╚══╗ ║╚═╗  ║║║║║║ ║╚══╗ ║╚╝╚╗  ║║╚╗║ ║╚══╗ ║║║║ ║╚══╗ ║║   ║║║║ ║╚═╝║ ║╚╗╔╝║ ║╚══╗ ║╔╗ ║   ║║
 * ║║╚╗║ ║╔╗║ ║╔╗╔╝   ║║   ║╔══╝ ╚═╗║  ║║║║║║ ║╔══╝ ║╔═╗║  ║║─║║ ║╔══╝ ║╚╝║ ║╔══╝ ║║   ║║║║ ║╔══╝ ║╔╗╔╗║ ║╔══╝ ║║╚╗║   ║║
 * ║╚═╝║ ║║║║ ║║║║    ║║   ║╚══╗ ╔═╝║  ║╚╝╚╝║ ║╚══╗ ║╚═╝║  ║╚═╝║ ║╚══╗ ╚╗╔╝ ║╚══╗ ║╚═╗ ║╚╝║ ║║    ║║╚╝║║ ║╚══╗ ║║ ║║   ║║
 * ╚═══╝ ╚╝╚╝ ╚╝╚╝    ╚╝   ╚═══╝ ╚══╝  ╚═╝╚═╝ ╚═══╝ ╚═══╝  ╚═══╝ ╚═══╝  ╚╝  ╚═══╝ ╚══╝ ╚══╝ ╚╝    ╚╝  ╚╝ ╚═══╝ ╚╝ ╚╝   ╚╝
 *----------------------------------------------------------------------------------------------------------------------
 * @author     Gartes | sad.net79@gmail.com | Telegram : @gartes
 * @date       12.11.22 11:49
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;

/**
 * Table class Setting_city
 * @author Gartes
 * @since  3.9
 */
class CustomfiltersTableSetting_city extends Table
{
	/**
	 * @var string Имя таблицы этого класса
	 * @since 3.9
	 */
	protected $table = '#__cf_customfields_setting_city';

	/**
	 * @var string[]    Массив имен ключей, которые нужно закодировать в формате json -- к примеру для поля "params".
	 *                  An array of key names to be json encoded in the bind function
	 * @since    1.0.0
	 */
	protected $_jsonEncode = ['params' ,'params_customs','statistic'];
	/**
	 * @var string - Имя CityFilter
	 * @since 3.9
	 */
	public  $alias ;
	/**
	 * @var string - Системное имя фильтра
	 * @since 3.9
	 */
	public $slug_filter;

	/**
	 * Constructor
	 *
	 * @since    1.5
	 */
	function __construct(&$_db)
	{
		parent::__construct( $this->table, 'id', $_db );
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
	public function store($updateNulls = false)
	{


		// дополнительные обработки полей таблицы
		// ect./ - $this->url_params_hash = md5( $this->url_params ) ;
		return parent::store($updateNulls);
	}
	/**
	 * Метод для загрузки строки из базы данных по первичному ключу и привязки полей к свойствам экземпляра таблицы.
	 * Method to load a row from the database by primary key and bind the fields to the Table instance properties.
	 *
	 * @param   mixed    $keys   Необязательное значение первичного ключа для загрузки строки
	 *                           или массив полей для сопоставления. Если не задано, используется значение свойства экземпляра.
	 *                           An optional primary key value to load the row by, or an array of fields to match.
	 *                           If not set the instance property value is used.
	 * @param   boolean  $reset  Значение true, чтобы сбросить значения по умолчанию перед загрузкой новой строки.
	 *                           True to reset the default values before loading the new row.
	 *
	 * @return  boolean  Правда в случае успеха. False, если строка не найдена. / True if successful. False if row not found.
	 *
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 * @since   1.7.0
	 */
	public function load($keys = null, $reset = true):bool
	{
		$result = parent::load( $keys , $reset );
		/**
		 * @var CustomfiltersTableSetting_city_category_vm
		 */
		$TableVmCat = Table::getInstance( 'Setting_city_category_vm' , 'CustomfiltersTable' , [] );
		$this->vm_categories_id = $TableVmCat->getDisabledCategories( true , $this->id );
		$this->getFilterCitySlug();
		if ( property_exists($this , 'statistic') )
		{
			$registry     = new Registry($this->statistic);
			$this->statistic = $registry->toArray();
		}

		return $result;
	}

	/**
	 * Добавить SLUG Системное имя для названия фильтра
	 * @return void
	 * @since 3.9
	 */
	protected function getFilterCitySlug(){
		$alias = preg_replace('/[^a-zA-Zа-яА-Я\d]+/ui' , '-' , $this->alias );
		$alias = \GNZ11\Document\Text::rus2translite($alias);
		$this->slug_filter   = mb_strtolower($alias);
	}

}