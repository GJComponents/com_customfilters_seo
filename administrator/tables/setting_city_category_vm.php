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

/**
 * Table class Setting_city
 * @author Gartes
 * @since  3.9
 */
class CustomfiltersTableSetting_city_category_vm extends Table
{

	/**
	 * Имя таблицы базы данных для моделирования.
	 * Name of the database table to model.
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	protected $_tbl = '#__cf_customfields_setting_city_category_vm';

	/**
	 * @var string[]    Массив имен ключей, которые нужно закодировать в формате json -- к примеру для поля "params".
	 *                  An array of key names to be json encoded in the bind function
	 * @since    1.0.0
	 */
	protected $_jsonEncode = [];

	/**
	 * Constructor
	 *
	 * @since    1.5
	 */
	function __construct($_db)
	{
		return parent::__construct($this->_tbl, 'id', $_db);
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
	public function store($updateNulls = false): bool
	{
//		echo'<pre>';print_r( $this );echo'</pre>'.__FILE__.' '.__LINE__;
//		die(__FILE__ .' '. __LINE__ );

    	// дополнительные обработки полей таблицы
		// ect./ - $this->url_params_hash = md5( $this->url_params ) ;
		return parent::store($updateNulls);
	}

	/**
	 * Получить категории выбранные в других фильтрах или категории для текущего фильтра если $selected TRUE
	 *
	 * @param   bool  $selected  IF TRUE - select category CityFilter
	 * @param   bool  $filterId
	 *
	 * @return array
	 * @throws Exception
	 * @since 3.9
	 */
	public function getDisabledCategories(bool $selected = false , $filterId = false ): array
	{

		if ( !$filterId )
		{
			$filterId =  \Joomla\CMS\Factory::getApplication()->input->get('id' , false , 'INT');
		}#END IF

		$Query = $this->_db->getQuery(true);
		$Query->select($this->_db->quoteName('id_vm_category'))
			->from($this->_db->quoteName('#__cf_customfields_setting_city_category_vm'));
		if ( !$selected )
		{
			$Query->where($this->_db->quoteName('id_filter_city') . '<>' . $this->_db->quote($filterId) );
		}else{
			$Query->where($this->_db->quoteName('id_filter_city') . '=' . $this->_db->quote($filterId) );
		}#END IF

		$this->_db->setQuery( $Query ) ;
//		echo '<br>------------<br>Query Dump :'.__FILE__ .' '.__LINE__ .$Query->dump().'------------<br>';
		return $this->_db->loadColumn(0);

	}

}