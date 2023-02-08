<?php


/***********************************************************************************************************************
 *  ///////////////////////////╭━━━╮╱╱╱╱╱╱╱╱╭╮╱╱╱╱╱╱╱╱╱╱╱╱╱╭━━━╮╱╱╱╱╱╱╱╱╱╱╱╱╭╮////////////////////////////////////////
 *  ///////////////////////////┃╭━╮┃╱╱╱╱╱╱╱╭╯╰╮╱╱╱╱╱╱╱╱╱╱╱╱╰╮╭╮┃╱╱╱╱╱╱╱╱╱╱╱╱┃┃////////////////////////////////////////
 *  ///////////////////////////┃┃╱╰╯╭━━╮╭━╮╰╮╭╯╭━━╮╭━━╮╱╱╱╱╱┃┃┃┃╭━━╮╭╮╭╮╭━━╮┃┃╱╭━━╮╭━━╮╭━━╮╭━╮////////////////////////
 *  ///////////////////////////┃┃╭━╮┃╭╮┃┃╭╯╱┃┃╱┃┃━┫┃━━┫╭━━╮╱┃┃┃┃┃┃━┫┃╰╯┃┃┃━┫┃┃╱┃╭╮┃┃╭╮┃┃┃━┫┃╭╯////////////////////////
 *  ///////////////////////////┃╰┻━┃┃╭╮┃┃┃╱╱┃╰╮┃┃━┫┣━━┃╰━━╯╭╯╰╯┃┃┃━┫╰╮╭╯┃┃━┫┃╰╮┃╰╯┃┃╰╯┃┃┃━┫┃┃/////////////////////////
 *  ///////////////////////////╰━━━╯╰╯╰╯╰╯╱╱╰━╯╰━━╯╰━━╯╱╱╱╱╰━━━╯╰━━╯╱╰╯╱╰━━╯╰━╯╰━━╯┃╭━╯╰━━╯╰╯/////////////////////////
 *  ///////////////////////////╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱┃┃//  (C) 2023  ///////////////////
 *  ///////////////////////////╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╰╯/////////////////////////////////
 *----------------------------------------------------------------------------------------------------------------------
 * @author     Gartes | sad.net79@gmail.com | Telegram : @gartes
 * @date       07.02.23 19:27
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2023 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Table\Table;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;


class CustomfiltersModelSetting_seo extends \Joomla\CMS\MVC\Model\AdminModel
{

	/**
	 * Constructor.
	 *
	 * @param   array  $config    Необязательный ассоциативный массив настроек конфигурации.
	 *                            An optional associative array of configuration settings.
	 *
	 * @since      1.0
	 * @see        JController
	 */
	public function __construct( $config = [] )
	{
		// Перечислить столбцы доступные для фильтрации 
		if ( empty( $config[ 'filter_fields' ] ) )
		{
			$config[ 'filter_fields' ] = [
				'id' ,
				'items.id' ,
				'title' ,
				'items.title' ,
				'alias' ,
				'items.alias' ,
				'published' ,
				'items.published' ,
			];
		}
		Table::addIncludePath( JPATH_SITE.'/administrator/components/com_customfilters/tables' );
		parent::__construct( $config );
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function populateState( $ordering = null , $direction = null )
	{
		if ( $ordering === null )
		{
			$ordering = 'items.title';
		}

		if ( $direction === null )
		{
			$direction = 'ASC';
		}

		parent::populateState( $ordering , $direction );
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
	 * @param   string  $type
	 * @param   string  $prefix
	 * @param   array   $config
	 *
	 * @return bool|Table|JTable
	 * @since    1.0.0
	 */
	public function getTable( $type = 'Setting_seo' , $prefix = 'customfiltersTable' , $config = [] )
	{
		return Table::getInstance( $type , $prefix , $config );
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
			'com_customfilters.setting_seo' ,
			'setting_seo' ,
			[ 'control' => 'jform' , 'load_data' => $loadData ]
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

	/**
	 * Сохранение данных формы
	 *
	 * @param   array  $data
	 *
	 * @return bool
	 * @throws Exception
	 * @since 3.9
	 */
	public function save( $data = [] ):bool
	{
		// Дополнительная обработка данных
		// ...........
		// если данные оправленные Ajax - парсим строку
		if ( empty( $data ) )
		{
			$app      = \Joomla\CMS\Factory::getApplication();
			$formData = $app->input->get( 'jform' , false , 'RAW' );
			$data     = array();
			parse_str( $formData , $data );
		}#END IF

		$resultSave = parent::save( $data );

		/**
		 * Получаем данные после сохранения
		 * $item->id - ID сохраненной записи
		 */
		$item = $this->getItem();

		if ( $resultSave && $item->id )
		{
			JFactory::getApplication()->input->set( 'id' , $item->id );
		}#END IF
		return $resultSave;
	}


}