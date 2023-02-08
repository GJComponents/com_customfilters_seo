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
 * @date       07.02.23 14:39
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2023 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Table\Table;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;


class CustomfiltersModelSetting_seo_list extends Joomla\CMS\MVC\Model\ListModel
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
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string $ordering  An optional ordering field.
	 * @param   string $direction An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		if ($ordering === null)
		{
			$ordering = 'items.title';
		}

		if ($direction === null)
		{
			$direction = 'ASC';
		}

		parent::populateState($ordering, $direction);
	}

	/**
	 * Элементы поиска и сортировки для представления списка
	 * Get the filter form
	 *
	 * @param   array    $data      data
	 * @param   boolean  $loadData  load current data
	 *
	 * @return  JForm|false  the JForm object or false
	 *
	 * @since   3.7.0
	 */
	public function getFilterForm($data = array(), $loadData = true)
	{
		$form = parent::getFilterForm($data, $loadData);

		if ($form)
		{
			$form->setValue('search', 'filter', $this->getState('filter.search'));
//			$form->setFieldAttribute('group_id', 'context', $this->getState('filter.context'), 'filter');
//			$form->setFieldAttribute('assigned_cat_ids', 'extension', $this->state->get('filter.component'), 'filter');
		}

		return $form;
	}

	/**
	 * Method to get a \JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery  A \JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   1.0.0
	 */
	protected function getListQuery()
	{
		$db    = $this->getDbo();
		$query = parent::getListQuery()
			->select(
				// Перечислить поля для выборки
				$db->quoteName(
					[
						'items.id',
						'items.vmcategory_id',
						'items.url_params',
						'items.url_params_hash',
						'items.sef_url',
						'items.no_ajax',
						'items.published',

					]
				)
			)
			->from($db->quoteName('#__cf_customfields_setting_seo', 'items'));

		$search = $this->getState('filter.search');

		if ($search)
		{
			if (strpos($search, ':') !== false)
			{
				$itemId = substr($search, 3);
				$query->where($db->quoteName('items.id') . ' = ' . (int) $itemId);
			}
			else
			{
				$query->where($db->quoteName('items.sef_url') . ' LIKE ' . $db->quote('%' . $search . '%'));
			}
		}

		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where($db->quoteName('items.published') . ' = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(' . $db->quoteName('items.published') . ' = 0 OR ' . $db->quoteName('items.published') . ' = 1)');
		}

		// Add the list ordering clause.
		$orderCol       = $this->state->get('list.ordering', 'items.sef_url');

		// TODO - Доработать правильное определение сортировки
		if ( $orderCol == 'items.title')
		{
			$orderCol = 'items.sef_url' ;
		}#END IF

		$orderDirection = $this->state->get('list.direction', 'ASC');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirection));

       //  echo $query->dump();

//        echo'<pre>';print_r( $query );echo'</pre>'.__FILE__.' '.__LINE__;
//        die(__FILE__ .' '. __LINE__ );


		return $query;
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
	public function getTable( $type = 'Setting_seo_list' , $prefix = 'customfiltersTable' , $config = [] )
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
			'com_customfilters.setting_seo_list' ,
			'setting_seo_list' ,
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
	 * @return bool
	 * @throws Exception
	 * @since 3.9
	 */
	public function save( $data = [] )
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