<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

/**
 * @since       3.9
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 * @package     ${NAMESPACE}
 */
class seoTools_uri
{
	/**
	 * @since 3.9
	 * @var int ItemID пункта меню для option=com_customfilters & view=products
	 */
	protected static $customfiltersItemId;

	/**
	 * @since version
	 * @var array SEF URL - для опций фильтра и для ссылок пагинации
	 */
	public static $arrUrlSef = [];
	public static $UrlNoIndex = false;
	/**
	 * @since version
	 * @var seoTools_uri
	 */
	public static $instance;

	/**
	 * helper constructor.
	 * @throws Exception
	 * @since 3.9
	 */
	private function __construct( $options = array() )
	{
		self::checkUrlNoIndex();

		return $this;
	}#END FN

	/**
	 * @param   array  $options
	 *
	 * @return seoTools_uri
	 * @throws Exception
	 * @since 3.9
	 */
	public static function instance( $options = array() ):seoTools_uri
	{
		if ( self::$instance === null )
		{
			self::$instance = new self($options);
		}

		return self::$instance;
	}#END FN

	/**
	 * Создать объект SEF URL - для опций фильтра и для ссылок пагинации
	 *
	 * @param   string  $option_url  URL Query -
	 *                               /filtr/vodostochnye-sistemy/?custom_f_23[0]=d093d0bbd18fd0bdd186d0b5d0b2d0b0d18f&custom_f_10...
	 *
	 * @throws Exception
	 * @since 3.9
	 */
	public static function getSefUlrOption( string $option_url ):stdClass
	{
		// Если имеем кэшированную версию
		if ( isset(self::$arrUrlSef [ $option_url ]) ) return self::$arrUrlSef [ $option_url ]; #END IF

		$app                       = JFactory::getApplication();
		$resultData                = new stdClass();
		$resultData->vmcategory_id = $app->input->get('virtuemart_category_id' , 0 , 'INT');

		$seoTools_filters = \seoTools_filters::instance();
		$uri              = \Joomla\CMS\Uri\Uri::getInstance($option_url);
		$path             = $uri->getPath();
		$uriQuery         = $uri->getQuery(true);

		$pageStart = false;
		if ( isset($uriQuery[ 'start' ]) )
		{
			$pageStart = $uriQuery[ 'start' ];
			unset($uriQuery[ 'start' ]);
		}#END IF

		// Проверить на NO-INDEX - Option
		$resultData->no_index = seoTools::checkOffFilters($uriQuery);

		$settingSeoOrdering = [];
		$i_filterCount      = 0;
		foreach ( $uriQuery as $fieldId => $valueCustomHashArr )
		{


			$filter = $seoTools_filters->_getFilterById($fieldId);

			$filter->sef_url = self::getStringSefUrl($filter->alias);


			$i_optionCount = 0;


			// Подготовить массив со значениями
			$valueCustomHashArr = seoTools::prepareHex2binArr($valueCustomHashArr);

			foreach ( $valueCustomHashArr as $i => $valueCustom )
			{

				if ( $i_optionCount ) $filter->sef_url .= '-and';

				$valueCustomTranslite = \GNZ11\Document\Text::rus2translite($valueCustom);
				$valueCustomTranslite = mb_strtolower($valueCustomTranslite);
				$valueCustomTranslite = str_replace(' ' , '_' , $valueCustomTranslite);

				$filter->sef_url .= '-'.$valueCustomTranslite.'';
				$i_optionCount++;
			}

			$i_filterCount++;
			$settingSeoOrdering[ $filter->ordering ] = $filter;
		}

		ksort($settingSeoOrdering);

		$resultData->url_params = $option_url;
		$resultData->sef_url    = '';
		$iArrCount              = 0;

		foreach ( $settingSeoOrdering as $ordering => $filter )
		{
			if ( $iArrCount ) $resultData->sef_url .= '-and-';
			$resultData->sef_url .= $filter->sef_url;
			$iArrCount++;
		}
		$resultData->no_ajax = false;
		/**
		 * Если Sef link не пустой - Добавляем путь и в конец слэш
		 */
		if ( strlen($resultData->sef_url) )
		{
			$resultData->sef_url .= '/';
		}
		else
		{
			//  $path = str_replace('/filter/' , '/catalog/' , $path );
			$resultData->no_ajax = 1;
			$path                = seoTools::getPatchToVmCategory($resultData->vmcategory_id);

		}

		$resultData->sef_url = $path.$resultData->sef_url;

		// Очистим от не нужных символов
		$resultData->sef_url = seoTools::cleanSefUrl($resultData->sef_url);

		if ( $pageStart )
		{
			$resultData->url_params .= '&start='.$pageStart;
			$resultData->sef_url    .= 'start='.$pageStart;
		}
		$resultData->url_params_hash = md5($resultData->url_params);

		self::$arrUrlSef[ $option_url ] = $resultData;

		return $resultData;

	}

	/**
	 * Создать SEF-URL - Для названия фильтра
	 *
	 * @param   string  $alias  Строка алиас фильтра
	 *
	 * @return string
	 * @throws Exception
	 * @since version
	 */
	public static function getStringSefUrl( string $alias ):string
	{
		$alias = \GNZ11\Document\Text::rus2translite($alias);
		$alias = mb_strtolower($alias);
		$alias = str_replace(' ' , '_' , $alias);
		$alias = seoTools::cleanSefUrl($alias);

		return $alias;
	}

	/**
	 * Получить ссылку на текущую категорию Vm option=com_virtuemart view=category virtuemart_category_id= ***
	 *
	 * @param   bool|int  $category_id
	 *
	 * @return string
	 * @throws Exception
	 * @since    1.0.0
	 */
	public static function getPatchToVmCategory( $category_id = false ):string
	{
		if ( !$category_id )
		{
			$app         = JFactory::getApplication();
			$category_id = $app->input->get('virtuemart_category_id' , 0 , 'INT');
		}#END IF
		return JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$category_id.'&virtuemart_manufacturer_id=0');
	}

	/**
	 * Проверить - нужен ли Редирект на страницу категории - Если не выбрана ни одна опция фильтра - но ссылка ведет
	 * на компонент фильтрации.
	 *
	 * @param   array  $category_ids   - массив категорий VM
	 * @param   array  $findResultArr  - массив выбранных опций фильтра
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function checkRedirectToCategory( $category_ids , $findResultArr )
	{
		$app    = JFactory::getApplication();
		$option = $app->input->get('option' , false , 'STRING');

		$juri = \Joomla\CMS\Uri\Uri::getInstance();
		$path = $juri->getPath();

		// Если массив категорий - пустой -- ищем по alias категории в таблице #__menu
		if ( empty($category_ids) )
		{
			$catName = null;
			preg_match('/^\/.+\/(.+)\//i' , $path , $matches);
			if ( isset($matches[ 1 ]) )
			{
				$catName = $matches[ 1 ];
			}
			else
			{
				if ( CF_FLT_DEBUG )
				{
					seoTools_logger::add('-- Не удалось найти название категории из пути ('.$path.')');
				}
			}#END IF


			$db    = JFactory::getDbo();
			$Query = $db->getQuery(true);
			$Query->select([ $db->quoteName('link') ])
				->from($db->quoteName('#__menu'))
				->where($db->quoteName('alias').'='.$db->quote($catName));
			$db->setQuery($Query);
			$category_link  = $db->loadResult();
			$juri           = \Joomla\CMS\Uri\Uri::getInstance($category_link);
			$queryUrl       = $juri->getQuery(true);
			$category_ids[] = $queryUrl[ 'virtuemart_category_id' ];
			if ( $_SERVER[ 'REMOTE_ADDR' ] == DEV_IP )
			{
				echo '<pre>';
				print_r($queryUrl[ 'virtuemart_category_id' ]);
				echo '</pre>'.__FILE__.' '.__LINE__;
			}

		}#END IF

		if ( empty($findResultArr) )
		{
			$findResultArr = self::findCityFilters($category_ids , $findResultArr);
		}#END IF


		// Если ссылка не имеет выбранных опций фильтра - а только категория - перенаправляем в категорию
		if ( empty($findResultArr) && $option == 'com_customfilters' && count($category_ids) == 1 )
		{
			$juri        = JUri::getInstance();
			$catUrl      = seoTools_uri::getPatchToVmCategory($category_ids[ 0 ]);
			$catUrl      = preg_replace('/^\//' , '' , $catUrl);
			$redirectUrl = $juri::root().$catUrl;

			$app->redirect($redirectUrl , 301);
		}#END IF
	}

	/**
	 * Поиск в URL значений для генератора городов
	 *
	 * @param $category_ids
	 * @param $findResultArr
	 *
	 * @return mixed|void
	 * @throws Exception
	 * @since 3.9
	 */
	protected static function findCityFilters( $category_ids , $findResultArr )
	{
		$app = \Joomla\CMS\Factory::getApplication();
		if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
		{
//		    echo'<pre>';print_r( $app->input );echo'</pre>'.__FILE__.' '.__LINE__;
//		    die(__FILE__ .' '. __LINE__ );

		}

		$juri = \Joomla\CMS\Uri\Uri::getInstance();
		$path = $juri->getPath();
		preg_match('/^\/.+\/(.+)\//i' , $path , $matches);
		// Если название фильтра не нашли -
		if ( !$matches[ 1 ] ) return $findResultArr; #END IF

		$sef_alias = $matches[ 1 ];

		$db    = JFactory::getDbo();
		$Query = $db->getQuery(true);
		$Query->select('*')
			->from($db->quoteName('#__cf_customfields_setting_city_category_vm' , 'cat'));

		$Query->leftJoin(
			$db->quoteName('#__cf_customfields_setting_city' , 'city').' ON '
			.$db->quoteName('cat.id_filter_city').'='.$db->quoteName('city.id')
		);

		// применить метод $db->quote -- к каждому элементу массива
		$category_ids = array_map([ $db , 'quote' ] , $category_ids);
		$Query->where(sprintf('cat.id_vm_category IN (%s)' , join(',' , $category_ids)));
		$db->setQuery($Query);

		$res = $db->loadObject();

		// Перебираем дополнительные настройки - вкладка params_customs
		if ( !empty($res->params_customs) )
		{
			$params_customs = json_decode($res->params_customs);
			foreach ( $params_customs as $paramsCustom )
			{
				if ( $paramsCustom->sef_alias == $sef_alias )
				{
					return $paramsCustom;
					break;
				}#END IF
			}#END FOREACH
		}

		$params = new \Joomla\Registry\Registry();
		$params->loadString($res->params);
		$paramsArr = $params->toArray();
		// Поиск результатов в для списка Area-City
		self::getLineArr($paramsArr[ 'use_city_setting' ] , $sef_alias);

		if ( !empty(self::$LineArr) ) return self::$LineArr; #END IF
	}

	/**
	 * Проверяем ссылку из URL - для robots-'noindex, follow' результата фильтрации общее количество включенных Options.
	 * Если больше - Страница получит robots - 'noindex, follow'
	 *
	 * @param string $url - Тестируемый URL
	 *
	 * @return bool - TRUE - случае если URL -  noindex
	 * @since version
	 * @since 3.9
	 */
	public static function checkUrlNoIndex( string $url = 'SERVER' ):bool
	{
		$paramsComponent = \Joomla\CMS\Component\ComponentHelper::getParams('com_customfilters');
		/**
		 * @var int $max_count_filters_no_index Максимальное количество активных фильтров
		 */
		$max_count_filters_no_index = $paramsComponent->get('max_count_filters_no_index' , 3);
		$JUri                       = JUri::getInstance( $url );
		$path                       = $JUri->getPath();
		preg_match_all('/-and-/' , $path , $and_concat_matches);

		if ( count($and_concat_matches[ 0 ]) >= $max_count_filters_no_index ) {
			// Если тест для URL Страницы
			if ( $url == 'SERVER'  ) self::$UrlNoIndex = true; #END IF
			return true ;
		} #END IF
		return false ;
	}

	public static $LineArr;

	/**
	 * Поиск результатов в для списка Area-City
	 *
	 * @param $arr
	 * @param $sef_alias
	 *
	 * @return false
	 * @since 3.9
	 */
	public static function getLineArr( $arr , $sef_alias )
	{
		foreach ( $arr as $key => $item )
		{
			$cloneItem = $item;
			if ( is_array($item) && count($item) > 1 )
			{
				self::getLineArr($item , $sef_alias);
			}#END IF

			if ( $key == $sef_alias )
			{
				self::$LineArr = $key;
			}#END IF

		}#END FOREACH
		return false;
	}

	/**
	 * Получить ссылку на категорию VM в компоненте фильтра
	 *
	 * @param   int  $vmCategoryId  Id VM категории
	 *
	 * @return string Ссылка на категорию VM в компоненте фильтра etc: "/filtr/metallocherepitsa/"
	 * @throws Exception
	 * @since 3.9
	 */
	public static function getLinkFilterCategory( int $vmCategoryId ):string
	{
		$uri                 = new \Joomla\Uri\Uri('index.php');
		$q_array[ 'option' ] = 'com_customfilters';
		$q_array[ 'view' ]   = 'products';
		$q_array[ 'Itemid' ] = self::getItemIdComFilter();
		$uri->setQuery($q_array);
		$uri->setVar('virtuemart_category_id' , [ $vmCategoryId ]);

		return Route::link('site' , $uri->toString());
	}

	/**
	 * Получить ItemID пункта меню для option=com_customfilters & view=products
	 * @return int
	 * @throws Exception
	 * @since 3.9
	 */
	public static function getItemIdComFilter():int
	{
		if ( !self::$customfiltersItemId )
		{
			$app                       = Factory::getApplication('Site');
			$menus                     = $app->getMenu('Site');
			$cfmenus                   = $menus->getItems('link' , 'index.php?option=com_customfilters&view=products');
			self::$customfiltersItemId = $cfmenus[ 0 ]->id;
		}#END IF
		return self::$customfiltersItemId;
	}
}