<?php

use Joomla\CMS\Factory;


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
 * @date       03.01.23 13:19
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2023 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/

class seoTools_info_product
{
	/**
	 * Создать CacheId
	 * @return string
	 * @since 3.9
	 */
	public static function getCacheId(){
		$juri = \Joomla\CMS\Uri\Uri::getInstance();
		$filterUrl = $juri->getPath();
		$filterUrl = preg_replace('/start=\d+\//' , '' , $filterUrl ) ;
		return md5( $filterUrl );
	}
	/**
	 * Получить инфо о всех продуктах
	 *
	 * @param   JDatabaseQueryMysqli  $query
	 *
	 * @return array
	 * @throws Exception
	 * @since 3.9
	 */
	public static function getInfoProducts( JDatabaseQueryMysqli $query  ):array
	{


		$db = JFactory::getDbo();

		$select = [
			$db->quoteName('p_p.product_price'),
			$db->quoteName('m.virtuemart_manufacturer_id'),
//			$db->quoteName('ml.mf_name'),
//			$db->quoteName('m.virtuemart_manufacturer_id' , 'manufactur_id'),
//			$db->quoteName('MAX(p_p.product_price)' , 'largest_price' ),
		];
		$query->select($select);

		// TODO - Разобраться - с добавлениемм таблицы '#__virtuemart_product_prices' , 'p_p'
		/*$query->leftJoin(
			$db->quoteName('#__virtuemart_product_prices' , 'p_p')
			.' ON '
			.$db->quoteName('p_p.virtuemart_product_id') . ' = ' . $db->quoteName('p.virtuemart_product_id')
		);*/

		$query->leftJoin(
				$db->quoteName('#__virtuemart_product_manufacturers' , 'm')
			.' ON '
			.$db->quoteName('m.virtuemart_product_id') . ' = ' . $db->quoteName('p.virtuemart_product_id')
		);

		/*$query->leftJoin(
				$db->quoteName('#__virtuemart_manufacturers_' . $siteLang , 'ml')
			.' ON '
			.$db->quoteName('ml.virtuemart_manufacturer_id') . ' = ' . $db->quoteName('m.virtuemart_manufacturer_id')
		);*/

		
//		$db->setQuery($query , 0 , 10 );
		$db->setQuery($query );


		
		
		try
		{
		    // Code that may throw an Exception or Error.
			$resultObjectList = $db->loadObjectList() ;
		    // throw new \Exception('Code Exception '.__FILE__.':'.__LINE__) ;
		}
		catch (\Exception $e)
		{
		    // Executed only in PHP 5, will not be reached in PHP 7
		    echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
		    echo'<pre>';print_r( $e );echo'</pre>'.__FILE__.' '.__LINE__;
		    die(__FILE__ .' '. __LINE__ );
		}




		return self::prepareListDescriptionProduct( $resultObjectList );

	}

	/**
	 * Получить список производителей
	 *
	 * @param   array  $resultObjectList
	 *
	 * @return void
	 * @since 3.9
	 */
	protected static function getManufacturersProduct( array $resultObjectList )
	{
		$lang     = JFactory::getLanguage();
		$siteLang = $lang->getTag();
		$siteLang = strtolower( strtr( $siteLang , '-' , '_' ) );

		$db = JFactory::getDbo();


		$Query  = $db->getQuery( true );
		$select = [
			$db->quoteName( 'm.virtuemart_manufacturer_id' ) ,
			$db->quoteName( 'ml.mf_name' ) ,
			$db->quoteName( 'ml.mf_desc' ) ,
		];
		$Query->select( $select );
		$Query->from(
			$db->quoteName( '#__virtuemart_product_manufacturers' , 'm' )
		);
		$Query->leftJoin(
			$db->quoteName( '#__virtuemart_manufacturers_'.$siteLang , 'ml' )
			.' ON '
			.$db->quoteName( 'ml.virtuemart_manufacturer_id' ).' = '.$db->quoteName( 'm.virtuemart_manufacturer_id' )
		);
		$manufacturers = array_map([$db, 'quote'], $resultObjectList );
		$Query->where( sprintf('m.virtuemart_manufacturer_id IN (%s)', join(',', $manufacturers)));

		$Query->group($db->quoteName( 'm.virtuemart_manufacturer_id' ) ) ;

		$db->setQuery( $Query );
		$result = $db->loadObjectList();
		return $result ;


	}


	/**
	 * Подготовить массив с описанием товар для результата фильтрации
	 * ---
	 * @param   array  $info_products
	 *
	 * @return array
	 * @since 3.9
	 */
	protected static function prepareListDescriptionProduct( array $info_products ):array
	{
		$dataArr = [
			'count_Product' => count( $info_products ) ,
			'manufacturers' => [] ,
			'min_Price' => [] ,
			'max_Price' => [] ,

		];

		$virtuemart_manufacturer_idArr = [];

		foreach ( $info_products as $infoProduct )
		{
//			$dataArr['manufacturers_list'][] = $infoProduct->mf_name ;
			$dataArr['min_Price'][] = $infoProduct->product_price ;
			$dataArr['max_Price'][] = $infoProduct->product_price ;
			$virtuemart_manufacturer_idArr[] = $infoProduct->virtuemart_manufacturer_id ;

		}#END FOREACH

		$virtuemart_manufacturer_idArr = array_unique( $virtuemart_manufacturer_idArr );




		$dataArr['min_Price'] = min( $dataArr['min_Price'] );
		$dataArr['max_Price'] = max( $dataArr['max_Price'] );
		$dataArr['manufacturers'] = self::getManufacturersProduct( $virtuemart_manufacturer_idArr ) ;



//		echo'<pre>';print_r( $dataArr );echo'</pre>'.__FILE__.' '.__LINE__;
//		die(__FILE__ .' '. __LINE__ );


		return $dataArr ;
	}

	/**
	 * Отправить в APP
	 * @param   array  $info_products
	 *
	 * @return void
	 * @throws Exception
	 * @since 3.9
	 */
	public function setDescriptionProductResult( array $dataArr ){
		$app = \Joomla\CMS\Factory::getApplication();
		$paramsComponent = JComponentHelper::getParams('com_customfilters');


		$seoTools = new seoTools();
		$findReplaceArr = $seoTools->getReplaceFilterDescriptionArr();

		$dataArr['min_Price'] = round($dataArr['min_Price'] ) ;
		$dataArr['max_Price'] = round($dataArr['max_Price'] ) ;

		$findReplaceArr['{{RANGE_PRICE}}'] = seoTools_shortCode::getResultFilterRangePrice( $dataArr );
		$findReplaceArr['{{COUNT_PRODUCT}}'] = seoTools_shortCode::getResultFilterCountProduct( $dataArr );
		$findReplaceArr['{{MANUFACTURERS}}'] =  $dataArr['manufacturers'];

		$findReplaceArr['{{MIN_PRICE}}'] =  seoTools_shortCode::getResultFilterPrice( $dataArr['min_Price'] )  ;
		$findReplaceArr['{{MAX_PRICE}}'] =  seoTools_shortCode::getResultFilterPrice( $dataArr['max_Price'] ) ;
		$findReplaceArr['{{COUNT_PRODUCT_INT}}'] =  $dataArr['count_Product'];



		$app->set('ResultFilterDescription' , $findReplaceArr );

//		$doc = Factory::getDocument();
//		$doc->CountProductResult  ;
	}

}