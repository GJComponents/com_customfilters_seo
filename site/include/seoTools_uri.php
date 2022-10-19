<?php

/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 * @since 3.9
 */
class seoTools_uri
{
	/**
	 * @var array SEF URL - для опций фильтра и для ссылок пагинации
	 * @since version
	 */
	public static $arrUrlSef = [] ;

	public static $UrlNoIndex = false ;

	/**
	 * @var seoTools_uri
	 * @since version
	 */
    public static $instance;

    /**
     * helper constructor.
     * @throws Exception
     * @since 3.9
     */
    private function __construct( $options = array() )
    {
		$this->checkUrlNoIndex();
        return $this;
    }#END FN

    /**
     * @param array $options
     *
     * @return seoTools_uri
     * @throws Exception
     * @since 3.9
     */
    public static function instance( $options = array() ): seoTools_uri
    {
        if( self::$instance === null )
        {
            self::$instance = new self( $options );
        }
        return self::$instance;
    }#END FN

	/**
	 * Создать объект SEF URL - для опций фильтра и для ссылок пагинации
	 * @param   string  $option_url  URL Query - /filtr/vodostochnye-sistemy/?custom_f_23[0]=d093d0bbd18fd0bdd186d0b5d0b2d0b0d18f&custom_f_10...
	 *
	 * @throws Exception
	 * @since 3.9
	 */
	public static function getSefUlrOption(string $option_url ): stdClass
	{

		// Если имеем кэшированную версию
		if (isset( self::$arrUrlSef [ $option_url ] )) return  self::$arrUrlSef [ $option_url ] ; #END IF

		$app = JFactory::getApplication() ;
		$resultData = new stdClass();
		$resultData->vmcategory_id =  $app->input->get('virtuemart_category_id' , 0  , 'INT' );

		$seoTools_filters = \seoTools_filters::instance();
		$uri = \Joomla\CMS\Uri\Uri::getInstance( $option_url );
		$path =  $uri->getPath();
		$uriQuery = $uri->getQuery(true);

		$pageStart = false ;
		if ( isset( $uriQuery['start']) )
		{
			$pageStart = $uriQuery['start'] ;
			unset( $uriQuery['start']  ) ;
		}#END IF

		// Проверить на NO-INDEX - Option
		$resultData->no_index = seoTools::checkOffFilters( $uriQuery );



		$settingSeoOrdering = [] ;
		$i_filterCount = 0;
		foreach ( $uriQuery as $fieldId => $valueCustomHashArr ){


			$filter = $seoTools_filters->_getFilterById ( $fieldId );

			if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
			{
//				echo'<pre>';print_r( $filter );echo'</pre>'.__FILE__.' '.__LINE__;
//				die(__FILE__ .' '. __LINE__ );
			}



			
//			$filter->aliasTranslite = \GNZ11\Document\Text::rus2translite( $filter->alias ) ;
//			$filter->aliasTranslite = mb_strtolower( $filter->aliasTranslite );
//			$filter->aliasTranslite = str_replace(' ' , '_' , $filter->aliasTranslite );
//			$filter->sef_url   = $filter->aliasTranslite ;

			$filter->sef_url = self::getStringSefUrl( $filter->alias  );

			if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
			{
//				echo'<pre>';print_r( $filter );echo'</pre>'.__FILE__.' '.__LINE__;
//				die(__FILE__ .' '. __LINE__ );

			}

			$i_optionCount = 0 ;

			 
			
			// Подготовить массив со значениями
			$valueCustomHashArr = seoTools::prepareHex2binArr( $valueCustomHashArr );

			foreach ( $valueCustomHashArr as $i => $valueCustom ){

				if ($i_optionCount) $filter->sef_url .= '-and';

				$valueCustomTranslite = \GNZ11\Document\Text::rus2translite($valueCustom) ;
				$valueCustomTranslite = mb_strtolower( $valueCustomTranslite );
				$valueCustomTranslite = str_replace(' ' , '_' , $valueCustomTranslite );

				$filter->sef_url  .= '-'.$valueCustomTranslite .'' ;
				$i_optionCount++;
			}

			$i_filterCount++;
			$settingSeoOrdering[$filter->ordering] = $filter ;
		}

		ksort($settingSeoOrdering);

		$resultData->url_params = $option_url ;
		$resultData->sef_url = '';
		$iArrCount = 0 ;

		foreach ( $settingSeoOrdering as $ordering => $filter  ){
			if ( $iArrCount ) $resultData->sef_url .= '-and-';
			$resultData->sef_url .= $filter->sef_url ;
			$iArrCount++;
		}
		$resultData->no_ajax = false ;
		/**
		 * Если Sef link не пустой - Добавляем путь и в конец слэш
		 */
		if ( strlen( $resultData->sef_url ) ){
			$resultData->sef_url .= '/' ;
		}
		else{
			//  $path = str_replace('/filter/' , '/catalog/' , $path );
			$resultData->no_ajax = 1 ;
			$path = seoTools::getPatchToVmCategory( $resultData->vmcategory_id );

		}

		$resultData->sef_url = $path . $resultData->sef_url   ;

		// Очистим от не нужных символов
		$resultData->sef_url = seoTools::cleanSefUrl( $resultData->sef_url );

		if (  $pageStart )
		{
			$resultData->url_params .= '&start=' . $pageStart;
			$resultData->sef_url    .= 'start=' . $pageStart;
		}
		$resultData->url_params_hash = md5( $resultData->url_params ) ;

		self::$arrUrlSef[$option_url] = $resultData ;
		return $resultData ;
		
	}

	/**
	 * Создать SEF-URL - Для названия фильтра
	 *
	 * @param   string  $alias  Строка алиас фильтра
	 *
	 * @return string
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function getStringSefUrl ( string $alias  ): string
	{
		$alias = \GNZ11\Document\Text::rus2translite( $alias ) ;
		$alias = mb_strtolower( $alias  );
		$alias = str_replace(' ' , '_' , $alias );
		$alias = seoTools::cleanSefUrl( $alias );
		return $alias ;
	}

	/**
	 * Получить ссылку на текущую категорию Vm
	 *
	 * @param bool|int  $category_id
	 *
	 * @return string
	 * @throws Exception
	 * @since    1.0.0
	 */
	public static function getPatchToVmCategory( $category_id = false ): string
	{

		if ( !$category_id )
		{
			$app = JFactory::getApplication() ;
			$category_id =  $app->input->get('virtuemart_category_id' , 0  , 'INT' );
		}#END IF


		return JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='. $category_id .'&virtuemart_manufacturer_id=0');
	}

	/**
	 * Проверить - нужен ли Редирект на страницу категории - Если не выбрана ни одна опция фильтра - но ссылка ведет
	 * на компонент фильтрации .
	 *
	 * @param array $category_ids - массив категорий VM
	 * @param array $findResultArr - массив выбранных опций фильтра
	 *
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function checkRedirectToCategory( $category_ids , $findResultArr ){
		$app = JFactory::getApplication();
		$option = $app->input->get('option' , false , 'STRING'   );

		// Если массив категорий - пустой -- ищем по alias категории в таблице #__menu
		if (empty($category_ids))
		{

			$juri    = \Joomla\CMS\Uri\Uri::getInstance();
			$path    = $juri->getPath();
			$catName = null;
			preg_match('/^\/.+\/(.+)\//i', $path, $matches);
			if (isset($matches[1]))
			{
				$catName = $matches[1];
			}
			else
			{
				if (CF_FLT_DEBUG)
				{
					seoTools_logger::add('-- Не удалось найти название категории из пути (' . $path . ')');
				}
			}#END IF


			$db    = JFactory::getDbo();
			$Query = $db->getQuery(true);
			$Query->select([$db->quoteName('link')])
				->from($db->quoteName('#__menu'))
				->where($db->quoteName('alias') . '=' . $db->quote($catName));
			$db->setQuery($Query);

			$category_link  = $db->loadResult();
			$juri           = \Joomla\CMS\Uri\Uri::getInstance($category_link);
			$queryUrl       = $juri->getQuery(true);
			$category_ids[] = $queryUrl['virtuemart_category_id'];
			echo '<pre>';
			print_r($queryUrl['virtuemart_category_id']);
			echo '</pre>' . __FILE__ . ' ' . __LINE__;

		}#END IF

		// Если ссылка не имеет выбранных опций фильтра - а только категория - перенаправляем в категорию
		if ( empty($findResultArr) && $option == 'com_customfilters' && count($category_ids) == 1 )
		{
			$juri = JUri::getInstance();
			$catUrl = seoTools_uri::getPatchToVmCategory($category_ids[0]);
			$catUrl = preg_replace('/^\//' , '' , $catUrl ) ;
			$redirectUrl = $juri::root().$catUrl ;

			$app->redirect( $redirectUrl , 301 );
		}#END IF
	}

	/**
	 * Проверяем ссылку из URL - результата фильтрации общее количество включенных Options.
	 * Если больше - Страница получит robots - 'noindex, follow'
	 *
	 *
	 * @since version
	 */
	protected function checkUrlNoIndex(){
		$paramsComponent = \Joomla\CMS\Component\ComponentHelper::getParams('com_customfilters');
		// Максимальное количество активных фильтров
		$max_count_filters_no_index = $paramsComponent->get('max_count_filters_no_index' , 3 ) ;
		$JUri = JUri::getInstance();
		$path = $JUri->getPath();

		preg_match_all('/-and-/' , $path , $and_concat_matches ) ;

		if ( count( $and_concat_matches[0] ) >= $max_count_filters_no_index ) self::$UrlNoIndex = true ; #END IF

	}


}