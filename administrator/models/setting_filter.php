<?php


/***********************************************************************************************************************
 *  ///////////////////////////╭━━━╮╱╱╱╱╱╱╱╱╭╮╱╱╱╱╱╱╱╱╱╱╱╱╱╭━━━╮╱╱╱╱╱╱╱╱╱╱╱╱╭╮////////////////////////////////////////
 *  ///////////////////////////┃╭━╮┃╱╱╱╱╱╱╱╭╯╰╮╱╱╱╱╱╱╱╱╱╱╱╱╰╮╭╮┃╱╱╱╱╱╱╱╱╱╱╱╱┃┃////////////////////////////////////////
 *  ///////////////////////////┃┃╱╰╯╭━━╮╭━╮╰╮╭╯╭━━╮╭━━╮╱╱╱╱╱┃┃┃┃╭━━╮╭╮╭╮╭━━╮┃┃╱╭━━╮╭━━╮╭━━╮╭━╮////////////////////////
 *  ///////////////////////////┃┃╭━╮┃╭╮┃┃╭╯╱┃┃╱┃┃━┫┃━━┫╭━━╮╱┃┃┃┃┃┃━┫┃╰╯┃┃┃━┫┃┃╱┃╭╮┃┃╭╮┃┃┃━┫┃╭╯////////////////////////
 *  ///////////////////////////┃╰┻━┃┃╭╮┃┃┃╱╱┃╰╮┃┃━┫┣━━┃╰━━╯╭╯╰╯┃┃┃━┫╰╮╭╯┃┃━┫┃╰╮┃╰╯┃┃╰╯┃┃┃━┫┃┃/////////////////////////
 *  ///////////////////////////╰━━━╯╰╯╰╯╰╯╱╱╰━╯╰━━╯╰━━╯╱╱╱╱╰━━━╯╰━━╯╱╰╯╱╰━━╯╰━╯╰━━╯┃╭━╯╰━━╯╰╯/////////////////////////
 *  ///////////////////////////╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱┃┃//  (C) 2022  ///////////////////
 *  ///////////////////////////╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╰╯/////////////////////////////////
 *----------------------------------------------------------------------------------------------------------------------
 * @author     Gartes | sad.net79@gmail.com | Telegram : @gartes
 * @date       20.12.22 11:15
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class customfiltersModelSetting_filter extends AdminModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  Необязательный ассоциативный массив настроек конфигурации.
	 *                          An optional associative array of configuration settings.
	 *
	 * @since      1.0
	 * @see        JController
	 */
	public function __construct( $config = [] )
	{
		Table::addIncludePath( JPATH_SITE.'/administrator/components/com_customfilters/tables' );
		parent::__construct( $config );
	}

	/**
	 * Получить список значений на настраиваемого поля
	 * @param int|bool $vm_custom_id
	 *
	 * @return array
	 * @throws Exception
	 * @since 3.9
	 */
	public function getCustomFieldValue( $vm_custom_id = false ):array
	{
		$app = \Joomla\CMS\Factory::getApplication();
		if ( !$vm_custom_id )
		{

			$vm_custom_id = $app->input->get( 'custom_id' , false , 'RAW' );
		}#END IF

		if ( !$vm_custom_id )
		{
			$app->enqueueMessage('Не передано ID Custom Field' , 'error');
			return [] ;
		}#END IF
		
		$Query = $this->_db->getQuery(true);
		$select = [
			$this->_db->quoteName('virtuemart_customfield_id'),
			$this->_db->quoteName('customfield_value'),
		];
		$Query->select( $select );
		$Query->from( $this->_db->quoteName('#__virtuemart_product_customfields') );
		$where = [
			$this->_db->quoteName('virtuemart_custom_id') . '=' . $this->_db->quote( $vm_custom_id ),
			$this->_db->quoteName('published') . '=' . $this->_db->quote( 1 ),
		];
		$Query->where( $where );
		$Query->group($this->_db->quoteName('customfield_value') );
		$this->_db->setQuery($Query);
		$result = $this->_db->loadAssocList();

		echo'<pre>';print_r( $result );echo'</pre>'.__FILE__.' '.__LINE__;
		die(__FILE__ .' '. __LINE__ );

	}

	/**
	 * Метод получения одной записи.
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  Идентификатор первичного ключа.
	 *                        The id of the primary key.
	 *
	 * @return  \JObject|boolean  Object в случае успеха, false в случае неудачи. / Object on success, false on failure.
	 *
	 * @throws Exception
	 * @since   1.6
	 */
	public function getItem( $pk = null )
	{
		$app = \Joomla\CMS\Factory::getApplication();
		$pk = $app->input->get('cid' , null , 'INT' ) ;

		$pk    = ( !empty( $pk ) ) ? $pk : (int) $this->getState( $this->getName().'.id' );
		$table = $this->getTable();

		if ( $pk > 0 )
		{
			// Attempt to load the row.
			$return = $table->load( $pk );

			// Check for a table object error.
			if ( $return === false && $table->getError() )
			{
				$this->setError( $table->getError() );

				return false;
			}
		}

		// Convert to the \JObject before adding other data.
		$properties = $table->getProperties( 1 );
		$item       = ArrayHelper::toObject( $properties , '\JObject' );

		if ( property_exists( $item , 'params' ) )
		{
			$registry     = new Registry( $item->params );
			$item->params = $registry->toArray();
		}
//		echo'<pre>';print_r( $item );echo'</pre>'.__FILE__.' '.__LINE__;
//		die(__FILE__ .' '. __LINE__ );

		return $item;
	}

	/**
	 * @param   string
	 * @param   string
	 * @param   array
	 *
	 * @return bool|Table|JTable
	 * @since    1.0.0
	 */
	public function getTable( $type = 'Setting_filter' ,$prefix  = 'customfiltersTable' ,$config = [] )
	{
		return Table::getInstance( $type, $prefix, $config  );
	}



	/**
	 * Способ получения формы записи.
	 * Method to get the record form.
	 *
	 * @param   array    $data      Необязательный массив данных для опроса формы.
	 *                              An optional array of data for the form to interrogate.
	 * @param   boolean  $loadData  Истина, если форма должна загружать свои собственные данные (случай по умолчанию),
	 *                              ложь, если нет.
	 *                              True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form|boolean    Объект Form в случае успеха, false в случае неудачи  / A Form object on success, false
	 *                          on failure
	 * @since   1.0.0
	 */
	public function getForm( $data = [] , $loadData = true )
	{
		$form = $this->loadForm(
			'com_customfilters.setting_filter' ,
			'setting_filter' ,
			[ 'control' => 'jform', 'load_data' => $loadData ]
		);

		return !empty( $form ) ? $form : false;
	}

	/**
	 * Метод для получения данных, которые должны быть введены в форму.
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The default data is an empty array.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		return array();
	}


	protected function getAjaxData(){
		$app      = \Joomla\CMS\Factory::getApplication();
		$formData = $app->input->get( 'formData' , false , 'RAW' );
		$data     = array();
		parse_str( $formData , $data );
		return $data ;
	}
	/**
	 * Сохранение формы настройки фильтра
	 * @return void
	 * @throws Exception
	 * @since 3.9
	 */
	public function save( $data = [] )
	{
		// Дополнительная обработка данных
		// ...........

		$app = \Joomla\CMS\Factory::getApplication();

		// если данные оправленные Ajax - парсим строку
		if ( empty( $data ) )
		{
			$data = $this->getAjaxData();
		}#END IF

		$object = new stdClass();
		// Должно быть допустимое значение первичного ключа.
		$object->id = $data['id'];
		$object->params = json_encode( $data['jform'] );
		// Update their details in the users table using id as the primary key.
		$result = \Joomla\CMS\Factory::getDbo()->updateObject('#__cf_customfields', $object, 'id');
		if ( $result )
		{
			// Удалить КЕШ - на фронте из админ панели для модуля
			/** @var \JCacheControllerCallback $cache */
			$cache = \Joomla\CMS\Cache\Cache::getInstance('callback');
			$cache->options['cachebase'] = JPATH_SITE . '/cache';
			$cache->setCaching(1);
			$cache->clean('mod_cf_filtering' , 'group' );

			$app->enqueueMessage( Text::_('COM_CUSTOMFILTERS_SAVE_SUCCESSFUL'));
			$app->enqueueMessage( Text::_('COM_CUSTOMFILTERS_SETTING_FILTER_ON_CLEAR_CACHE') , 'warning');
			echo new JResponseJson(null, '', false );
			die();
		}#END IF
		$app->enqueueMessage( Text::_('COM_CUSTOMFILTERS_SAVE_ERROR'));
		echo new JResponseJson(null, '', false );
		die();

	}
}