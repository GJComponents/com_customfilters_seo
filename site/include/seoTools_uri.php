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
	public static $arrUrlSef = [] ;

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
			
//			echo'<pre>';print_r( $filter );echo'</pre>'.__FILE__.' '.__LINE__;
			
			
			$filter->aliasTranslite = \GNZ11\Document\Text::rus2translite( $filter->alias ) ;
			$filter->aliasTranslite = mb_strtolower( $filter->aliasTranslite );
			$filter->aliasTranslite = str_replace(' ' , '_' , $filter->aliasTranslite );
			$filter->sef_url   = $filter->aliasTranslite ;

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
}