<?php

/***********************************************************************************************************************
 * ╔═══╗ ╔══╗ ╔═══╗ ╔════╗ ╔═══╗ ╔══╗  ╔╗╔╗╔╗ ╔═══╗ ╔══╗   ╔══╗  ╔═══╗ ╔╗╔╗ ╔═══╗ ╔╗   ╔══╗ ╔═══╗ ╔╗  ╔╗ ╔═══╗ ╔╗ ╔╗
 * ╔════╗
 * ║╔══╝ ║╔╗║ ║╔═╗║ ╚═╗╔═╝ ║╔══╝ ║╔═╝  ║║║║║║ ║╔══╝ ║╔╗║   ║╔╗╚╗ ║╔══╝ ║║║║ ║╔══╝ ║║   ║╔╗║ ║╔═╗║ ║║  ║║ ║╔══╝ ║╚═╝║
 * ╚═╗╔═╝
 * ║║╔═╗ ║╚╝║ ║╚═╝║   ║║   ║╚══╗ ║╚═╗  ║║║║║║ ║╚══╗ ║╚╝╚╗  ║║╚╗║ ║╚══╗ ║║║║ ║╚══╗ ║║   ║║║║ ║╚═╝║ ║╚╗╔╝║ ║╚══╗ ║╔╗ ║
 * ║║
 * ║║╚╗║ ║╔╗║ ║╔╗╔╝   ║║   ║╔══╝ ╚═╗║  ║║║║║║ ║╔══╝ ║╔═╗║  ║║─║║ ║╔══╝ ║╚╝║ ║╔══╝ ║║   ║║║║ ║╔══╝ ║╔╗╔╗║ ║╔══╝ ║║╚╗║
 * ║║
 * ║╚═╝║ ║║║║ ║║║║    ║║   ║╚══╗ ╔═╝║  ║╚╝╚╝║ ║╚══╗ ║╚═╝║  ║╚═╝║ ║╚══╗ ╚╗╔╝ ║╚══╗ ║╚═╗ ║╚╝║ ║║    ║║╚╝║║ ║╚══╗ ║║ ║║
 * ║║
 * ╚═══╝ ╚╝╚╝ ╚╝╚╝    ╚╝   ╚═══╝ ╚══╝  ╚═╝╚═╝ ╚═══╝ ╚═══╝  ╚═══╝ ╚═══╝  ╚╝  ╚═══╝ ╚══╝ ╚══╝ ╚╝    ╚╝  ╚╝ ╚═══╝ ╚╝ ╚╝
 * ╚╝
 *----------------------------------------------------------------------------------------------------------------------
 * @author     Gartes | sad.net79@gmail.com | Telegram : @gartes
 * @date       12.11.22 11:49
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;


class CustomfiltersModelSetting_city extends AdminModel
{
	/**
	 * @since 3.9
	 * @var string Ссылка на файл Json с городами
	 */
	protected $urlCityJson = 'https://gist.githubusercontent.com/gartes/ab9534ac8c6440297b921285264a8dd1/raw/8d3ed3eb3b809a090c9f3f4fc993903c509bcbad/cities.json';
	/**
	 * @since 3.9
	 * @var string Имя таблицы для списка городов
	 */
	protected $cityTableName = '#__cf_customfields_city';
	/**
	 * @since 3.9
	 * @var int Индекс в массиве
	 */
	protected $contentLevel = -1;
	/**
	 * @since 3.9
	 * @var int - Количество населенных пунктов
	 */
	protected $countCity = 0;/**
 * @since 3.9
 * @var
 */
	private $ArrData;

	/**
	 * @since 3.9
	 * @var Registry - Параметры компонента com_customfilters
	 */
	protected $paramsComponent ;
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
		Table::addIncludePath(JPATH_SITE.'/administrator/components/com_customfilters/tables');
		JLoader::register('HelperSetting_city' , JPATH_ADMINISTRATOR . '/components/com_customfilters/helpers/HelperSetting_city.php');
		parent::__construct($config);
		$this->paramsComponent = JComponentHelper::getParams('com_customfilters');
	}

	/**
	 *
	 * @param $ids
	 *
	 * @return bool|Table|JTable
	 * @since 3.9
	 */
	public function loadTableCityFilter( $ids )
	{
		$table = $this->getTable();
		$table->load($ids);

		return $table;

	}

	/**
	 * Получить транслит для строки
	 * @return string
	 * @since 3.9
	 */
	public function getTranslite( $string )
	{
		if ( !$string ) return $string; #END IF
		return \GNZ11\Document\Text::rus2translite($string);
	}

	/**
	 * Получить список городов из таблицы #__cf_customfields_city для родительского регион parentRegion
	 * @param $area_id
	 *
	 * @return array|mixed|void
	 * @throws Exception
	 * @since 3.9
	 */
	public function getListCity( $area_id = false )
	{
		$parentRegion = \Joomla\CMS\Factory::getApplication()->input->get('parentRegion' , 0 , 'INT');

		$db    = JFactory::getDbo();
		$Query = $db->getQuery(true);
		$Query->select('*');
		$Query->from($this->cityTableName);
		$Query->where($db->quoteName('parent_id').'='.$db->quote($parentRegion));
		$db->setQuery($Query);
		try
		{
			// Code that may throw an Exception or Error.
			$cityAssocList = $db->loadAssocList();
			// throw new \Exception('Code Exception '.__FILE__.':'.__LINE__) ;
		}
		catch ( \Exception $e )
		{
			// Если таблицы #__cf_customfields_city не существует
			if ( $e->getCode() == 1146 )
			{
				$this->_loadCityList();
			}#END IF
			// Executed only in PHP 5, will not be reached in PHP 7
			echo 'Выброшено исключение: ' , $e->getMessage() , "\n";
			echo '<pre>'; print_r($e); echo '</pre>'.__FILE__.' '.__LINE__;
			die(__FILE__.' '.__LINE__);
		}

		return $cityAssocList;
	}

	/**
	 * Загрузить список городов из Gist и установить в DB
	 * @return void
	 * @throws Exception
	 * @since 3.9
	 */
	protected function _loadCityList()
	{
		$app          = \Joomla\CMS\Factory::getApplication();
		$contentsJson = file_get_contents($this->urlCityJson);
		$contents     = json_decode($contentsJson);
		$registry     = new JRegistry($contents);
		$contents     = $registry->toArray();

		$this->_getOneLevelArr($contents);
		$app->enqueueMessage('Будет добавлено '.$this->countCity.' городов');
		$this->_createCityTable();
		$this->_loadCityDataInTable();
	}

	/**
	 * Загрузить данные городов в таблицу
	 * @return void
	 * @since 3.9
	 */
	protected function _loadCityDataInTable()
	{
		$columns = [ 'id' , 'parent_id' , 'name' , 'alias' ];
		$db      = JFactory::getDBO();
		$Query   = $db->getQuery(true);
		foreach ( $this->ArrData as $item )
		{
			$values =
				$db->quote($item[ 'id' ]).","
				.$db->quote($item[ 'parent_id' ]).","
				.$db->quote($item[ 'name' ]).","
				.$db->quote($item[ 'alias' ]);

			$Query->values($values);
		}//foreach
		$Query->insert($db->quoteName($this->cityTableName))
			->columns($db->quoteName($columns));
		$db->setQuery($Query);
//		echo $Query->dump();
		$db->execute();
	}

	/**
	 * Создание таблицы для городов
	 * @return void
	 * @since 3.9
	 */
	protected function _createCityTable()
	{
		$db    = JFactory::getDBO();
		$query = "CREATE TABLE IF NOT EXISTS "
			.$db->quoteName($this->cityTableName)
			." ( 
				`id` int(10) NOT NULL, 
				`parent_id` int(10) NOT NULL, 
				`name` varchar(120) NOT NULL, 
				`alias` varchar(120) NOT NULL, 
				
				`lat` varchar(12) NOT NULL, 
				`lng` varchar(12) NOT NULL, 
				
				PRIMARY KEY (`id`),
				KEY `parent_id` (`parent_id`), 
				UNIQUE KEY `alias` (`alias`) 
				) 
				ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$db->setQuery($query);
		$db->execute();

	}

	/**
	 * Подготовить данные в виде одномерного массива
	 *
	 * @param $contents  - Данные в виде многомерного массива
	 *
	 * @return void
	 * @since 3.9
	 */
	public function _getOneLevelArr( $contents )
	{
		$result = [];
		array_walk_recursive($contents , function ( $content , $key ) use ( &$result ) {

			if ( $key == 'id' )
			{
				$this->contentLevel++;
			}#END IF

			$this->ArrData[ $this->contentLevel ][ $key ] = $content;
			// Если название города - делаем Alias
			if ( $key == 'name' )
			{



				$alias = $this->createCityAlias( $content ) ;

				// для того что бы алиас был уникальным
				if ( key_exists($alias , $this->ArrAlias) )
				{
					$this->ArrAlias[ $alias ]++;
					$numIndex = $this->ArrAlias[ $alias ];
					$alias    .= '-'.$numIndex;
				}
				else
				{
					$this->ArrAlias[ $alias ] = 0;
				}#END IF

				$this->ArrData[ $this->contentLevel ][ 'alias' ] = $alias;
			}#END IF

			if ( $key == 'areas' && !empty($content) )
			{
				$this->_getOneLevelArr($content);
			}
			else
			{
				// Считаем населенные пункты
				$this->countCity++;
			}#END IF

		});
	}

	/**
	 * Создание Alias для названия городов
	 *
	 * @param   string  $content
	 *
	 * @return string
	 * @since 3.9
	 */
	protected function createCityAlias( string $content ){
		$alias = \GNZ11\Document\Text::rus2translite($content);
		$alias = preg_replace('/[^A-Z0-9]/ui' , '_' , $alias);
		$alias = str_replace('__' , '_' , $alias);
		$alias = preg_replace('/_$/i' , '' , $alias);

		return mb_strtolower( $alias );
	}
	protected $resArray = [];

	/**
	 * Установить META Params по умолчанию
	 * @param $cityList
	 *
	 * @return array|mixed
	 * @since 3.9
	 */
	public function _setDefaultParams( $cityList ){


		foreach ( $cityList as $item )
		{

			$alias = $item['alias'] ;
			$this->resArray[$alias] = $this->addDefaultParamsCity( $item );

		}#END FOREACH

		return $this->resArray ;
		
	}

	/**
	 * Добавить параметры по умолчанию для одного города
	 * @param $city
	 *
	 * @return void
	 * @since 3.9
	 */
	public function addDefaultParamsCity($city){
		$default_h1_tag = $this->paramsComponent->get('default_h1_tag' , '{{CATEGORY_NAME}} - {{FILTER_VALUE_LIST}}') ;
		$default_title = $this->paramsComponent->get('default_title' , '{{CATEGORY_NAME}} - {{FILTER_VALUE_LIST}}') ;
		$default_description = $this->paramsComponent->get('default_description' , '{{CATEGORY_NAME}} - {{FILTER_VALUE_LIST}}') ;
		$default_keywords = $this->paramsComponent->get('default_keywords' , '{{CATEGORY_NAME}} - {{FILTER_VALUE_LIST}}') ;
		$resArray['use'] = 0  ;
		$resArray['default_h1_tag'] = $default_h1_tag  ;
		$resArray['default_title'] = $default_title  ;
		$resArray['default_description'] = $default_description ;
		$resArray['default_keywords'] = $default_keywords  ;
		return $resArray ;
	}


	/**
	 * Создать одно уровневый массив с настройками для городов|регионов
	 * @param $array
	 * @param $key
	 *
	 * @return array|mixed
	 * @since 3.9
	 */
	public function _getOneLevelParams( $array , $aliasCity = false )
	{
		$this->resArray = HelperSetting_city::getOneLevelParams( $array );
		return  $this->resArray ;

		$default_h1_tag = $this->paramsComponent->get('default_h1_tag' , '{{CATEGORY_NAME}} - {{FILTER_VALUE_LIST}}') ;
		$default_title = $this->paramsComponent->get('default_title' , '{{CATEGORY_NAME}} - {{FILTER_VALUE_LIST}}') ;
		$default_description = $this->paramsComponent->get('default_description' , '{{CATEGORY_NAME}} - {{FILTER_VALUE_LIST}}') ;
		$default_keywords = $this->paramsComponent->get('default_keywords' , '{{CATEGORY_NAME}} - {{FILTER_VALUE_LIST}}') ;

		if ( is_array($array) )
		{
			foreach ( $array as $key => $below )
			{
				if ( !isset(  $below[ 'use' ] ) )
				{
					$below[ 'use' ] = 0 ;
				}#END IF


				$this->resArray[ $key ]['use'] = $below[ 'use' ];
				$this->resArray[ $key ]['default_h1_tag'] = trim( ($below[ 'default_h1_tag' ] ?? $default_h1_tag) ) ;
				$this->resArray[ $key ]['default_title'] = trim( ($below[ 'default_title' ] ?? $default_title) )  ;
				$this->resArray[ $key ]['default_description'] = trim( ($below[ 'default_description' ] ?? $default_description) ) ;
				$this->resArray[ $key ]['default_keywords'] = trim( ($below[ 'default_keywords' ] ?? $default_keywords) ) ;


				echo'<pre>';print_r( $key );echo'</pre>'.__FILE__.' '.__LINE__;
				echo'<pre>';print_r( $below );echo'</pre>'.__FILE__.' '.__LINE__;

				if ( isset( $below[ 'use' ] ) )
				{

				}
				else
				{
//					$this->resArray[ $key ] = 'NOT';
				}#END IF

				$this->_getOneLevelParams($below , $key);

			}
		} else if ( empty($array) )
		{
//			$this->resArray[ $key ]['use'] = 0 ;
//			$this->resArray[ $key ]['default_h1_tag'] = trim( $default_h1_tag ) ;
//			$this->resArray[ $key ]['default_title'] = trim( $default_title )  ;
//			$this->resArray[ $key ]['default_description'] = trim( $default_description ) ;
//			$this->resArray[ $key ]['default_keywords'] = trim( $default_keywords ) ;
		}#END IF

		return $this->resArray;
	}

	/**
	 * Сохранение данных формы Настройка городов
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
		if ( empty($data) )
		{
			$app      = \Joomla\CMS\Factory::getApplication();
			$formData = $app->input->get('jform' , false , 'RAW');
			$data     = array();
			parse_str($formData , $data);
			$data = $data[ 'jform' ];
		}#END IF

//		echo'<pre>';print_r( $data );echo'</pre>'.__FILE__.' '.__LINE__;
//		die(__FILE__ .' '. __LINE__ );


		// Находим сохраненные города - и если они не переданы явно - добавляем из уже сохраненных
		if ( $data[ 'id' ] )
		{
			$table = $this->getTable();
			$table->load($data[ 'id' ]);
			$params = new Joomla\Registry\Registry();
			$params->loadString($table->params);
			$paramsArr        = $params->toArray();
			$use_city_setting = $paramsArr[ 'use_city_setting' ];

			$savedData = $data[ 'params' ][ 'use_city_setting' ];

			foreach ( $savedData as $parenArea => &$savedDatum )
			{
				if ( isset($use_city_setting[ $parenArea ]) && count($savedDatum) == 1 )
				{
					$savedDatum = $use_city_setting[ $parenArea ];

				}#END IF
			}#END FOREACH
			$data[ 'params' ][ 'use_city_setting' ] = $savedData;
		}#END IF

		$test = new  \Joomla\Registry\Registry( ) ;
		$test->loadArray( $data[ 'params' ][ 'use_city_setting' ]  );
		$dataString = $test->toString('JSON') ;
		$dataString = str_replace( ['{{' , '}}'] , ['>>>>' , '<<<<'] , $dataString );

//		echo'<pre>';print_r( $dataString );echo'</pre>'.__FILE__.' '.__LINE__;

		/*$dataString = '{
			"ukraina":{
				"use":"0",
				"default_h1_tag":">>>>CATEGORY_NAME<<<< - >>>>FILTER_VALUE_LIST<<<<" , 
				"default_title":">>>>CATEGORY_NAME<<<< >>>>FILTER_VALUE_LIST<<<<" , 
				"default_description":">>>>CATEGORY_NAME<<<< >>>>FILTER_VALUE_LIST<<<< " ,
				"default_keywords":">>>>CATEGORY_NAME<<<< >>>>FILTER_VALUE_LIST<<<< " 
			}
		}';*/

		$test = new  \Joomla\Registry\Registry(  ) ;
//		$test->loadString( $dataString ) ;
//		$data[ 'params' ][ 'use_city_setting' ] = $test->toArray();
//		echo'<pre>';print_r(  $test );echo'</pre>'.__FILE__.' '.__LINE__;
//		echo'<pre>';print_r(  $data );echo'</pre>'.__FILE__.' '.__LINE__;
//		die(__FILE__ .' '. __LINE__ );


		$resultSave = parent::save( $data );

		/**
		 * Получаем данные после сохранения
		 * $item->id - ID сохраненной записи
		 */
		$item = $this->getItem();

		if ( $resultSave && $item->id && isset($data[ 'vm_categories_id' ]) )
		{
			JFactory::getApplication()->input->set('id' , $item->id);
			$this->saveCategoryVM($item->id , $data[ 'vm_categories_id' ]);
		}#END IF

		return $resultSave;
	}

	/**
	 * Сохранить ссылки - категория + ID фильтра в таблице "#__cf_customfields_setting_city_category_vm"
	 *
	 * @param $cityFilterId
	 * @param $vmCategoryIds
	 *
	 * @return void
	 * @since 3.9
	 */
	public function saveCategoryVM( $cityFilterId , $vmCategoryIds )
	{

		$this->deleteFiltersCategories($cityFilterId);

		foreach ( $vmCategoryIds as $vmCategoryId )
		{

			$table = $this->getTable('Setting_city_category_vm');
			$data  = [
				'id_vm_category' => $vmCategoryId ,
				'id_filter_city' => $cityFilterId ,
			];
			// Load the row if saving an existing record.
			$table->load($data);
//
			$table->bind($data);
			$res = $table->save($data);


		}#END FOREACH

	}

	/**
	 * Удалить ссылки категорий к фильтру
	 *
	 * @param   int|array  $id_filter_city  - ID фильтра
	 *
	 * @return void
	 * @since 3.9
	 */
	public function deleteFiltersCategories( $id_filter_city )
	{
		$Query = $this->_db->getQuery(true);
		$Query->delete('#__cf_customfields_setting_city_category_vm');

		if ( is_array($id_filter_city) )
		{
			$id_filter_city = array_map([ $this->_db , 'quote' ] , $id_filter_city);
			$Query->where(sprintf('id_filter_city IN (%s)' , join(',' , $id_filter_city)));

		}
		else
		{
			$Query->where($where = $this->_db->quoteName('id_filter_city').'='.$id_filter_city);
		}#END IF

		$this->_db->setQuery($Query);
		$this->_db->execute();
	}

	/**
	 * Метод получения одной записи.
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  Идентификатор первичного ключа.
	 *                        The id of the primary key.
	 *
	 * @return  object|bool  Object в случае успеха, false в случае неудачи. / Object on success, false on failure.
	 *
	 * @throws Exception
	 * @since   1.6
	 */
	public function getItem( $pk = null )
	{
		$pk    = ( !empty($pk) ) ? $pk : (int) $this->getState($this->getName().'.id');
		$table = $this->getTable();

		if ( $pk > 0 )
		{
			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ( $return === false && $table->getError() )
			{
				$this->setError($table->getError());

				return false;
			}
		}

		// Convert to the \JObject before adding other data.
		$properties = $table->getProperties(1);
		$item       = ArrayHelper::toObject($properties , '\JObject');

		// TODO INFO - Поле 'params' в таблице #__cf_customfields_setting_city - Должно иметь тип LONGTEXT
		if ( property_exists($item , 'params') )
		{
			$registry     = new Registry($item->params);
			$item->params = $registry->toArray();
		}
		if ( property_exists($item , 'statistic') )
		{
			$registry     = new Registry($item->statistic);
			$item->statistic = $registry->toArray();
		}

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
	public function getTable( $type = 'Setting_city' , $prefix = 'CustomfiltersTable' , $config = [] )
	{
		return Table::getInstance($type , $prefix , $config);
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
			'com_customfilters.setting_city' ,
			'setting_city' ,
			[ 'control' => 'jform' , 'load_data' => $loadData ]
		);

		return !empty($form) ? $form : false;
	}

	/**
	 * Метод для получения данных, которые должны быть введены в форму.
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The default data is an empty array.
	 *
	 * @throws Exception
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		$data = $this->getItem();

		return $data;
	}

	/**
	 * Количество активных дочерних регионов
	 * @since 3.9
	 * @var int
	 */
	public $ActiveChildArea = 0 ;

	/**
	 * Посчитать - количество активных дочерних регионов
	 * @param $area
	 *
	 * @return void
	 * @since 3.9
	 */
	public  function getActiveChildArea( $area )
	{
		foreach (  $area as $keyArea => $itemArea )
		{
			if ( $keyArea == 'use' ) continue ; #END IF

			if ( is_array( $itemArea ) && count($itemArea) == 1 && isset($itemArea['use']) && $itemArea['use'] == 1 )
			{
				$this->ActiveChildArea ++ ;
				continue ; 
			}#END IF

			if ( is_array( $itemArea ) && count($itemArea) > 1  )
			{
				$this->getActiveChildArea( $itemArea );
			}#END IF
			
		}#END FOREACH

	}
	public $ChildrenAreaData = [] ;
	public function getChildrenArea($cityParam , $area ){
		foreach (  $cityParam as $keyArea => $item )
		{
			if ( $area == $keyArea )  {
				$this->ChildrenAreaData = $item ;
				return  ;
			}#END IF
			if ( is_array($item) && count($item) == 1 && isset($item['use']) ) continue ; #END IF

			if ( is_array($item) && count($item) > 1 )
			{
				$this->getChildrenArea( $item , $area );
			}#END IF

		}#END FOREACH
	}

	/**
	 * Получить список подсказок AutoComplete для выбора родительского региона|города
	 *
	 * @param   string  $string  Строка для поиска
	 *
	 * @return array
	 * @since 3.9
	 */
	public function getParentsAreaAutoComplete( string $string):array
	{
		$db = JFactory::getDbo();
		$Query = $db->getQuery(true);
		$Query->select([
			$db->quoteName('id' , 'data'),
			$db->quoteName('name' , 'value'),
		]);
		$Query->from( $db->quoteName( $this->cityTableName ) );
		$Query->where([
			$db->quoteName('name') . 'LIKE ' . $db->quote($string . '%'),
		]);
		$db->setQuery($Query);
		return $db->loadObjectList();
	}

	/**
	 * Сохранить новый регион
	 *
	 * @param   array  $data
	 *
	 * @return bool - TRUE - при удачном сохранении
	 * @throws Exception
	 * @since 3.9
	 */
	public function saveNewArea( array $data = [] ):bool
	{
		$app = \Joomla\CMS\Factory::getApplication();
		$db    = JFactory::getDbo();

		$Query = $db->getQuery( true );
		$Query->select( 'MAX('.$db->quoteName('id').')' );
		$Query->from(  $db->quoteName( $this->cityTableName )  ) ;
		$db->setQuery( $Query );
		$maxCityId = $db->loadResult();
		$maxCityId ++ ;

		if ( !$data['parent_id'] && $data['parent_area']  )
		{
			$app->enqueueMessage('Название родительского региона нужно выбрать из списка!');
			return false ;
		}#END IF

		if ( !$data['parent_id'] && !$data['parent_area']  ) $data['parent_id'] = 0 ; #END IF

		if ( $data['name'] && !$data['alias']  )
		{
			$data['alias'] = $this->createCityAlias(  $data['name'] ) ;
		}else{
			$app->enqueueMessage('Название региона - обязательно!' , 'error' );
			return false ;
		}#END IF


		$Query = $db->getQuery( true );
		$columns = [ 'id' , 'parent_id' , 'name' , 'alias' ];
		$values =
			 $db->quote( $maxCityId  ).","
			.$db->quote( $data[ 'parent_id' ] ).","
			.$db->quote( $data[ 'name' ] ).","
			.$db->quote( $data[ 'alias' ] );

		$Query->values( $values );

		$Query->insert( $db->quoteName( $this->cityTableName ) )
			->columns( $db->quoteName( $columns ) );
		$db->setQuery( $Query );
//		echo $Query->dump();
        try
        {
            // Code that may throw an Exception or Error.
	        $db->execute();
            // throw new \Exception('Code Exception '.__FILE__.':'.__LINE__) ;
        }
        catch (\Exception $e)
        {
	        if ( $e->getCode() == 1062 )
	        {
				$app->enqueueMessage('Поле с псевдонимом "' .$data[ 'alias' ] .'" уже существует.' , 'error' );
				return false ;
	        }#END IF
        }

		return true ;
	}
}