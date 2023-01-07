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
	 * Получить инфо о всех продуктах
	 * @param JDatabaseQueryMysqli $query
	 *
	 * @return array
	 * @since 3.9
	 */
	public function getInfoProducts( JDatabaseQueryMysqli $query  ){

		$lang = JFactory::getLanguage();
		$siteLang = $lang->getTag();
		$siteLang = strtolower(strtr($siteLang, '-', '_'));
		$db = JFactory::getDbo();

		$select = [
			$db->quoteName('p_p.product_price'),
			$db->quoteName('ml.mf_name'),
//			$db->quoteName('m.virtuemart_manufacturer_id' , 'manufactur_id'),
//			$db->quoteName('MAX(p_p.product_price)' , 'largest_price' ),
		];
		$query->select($select);
		$query->leftJoin(
				$db->quoteName('#__virtuemart_product_manufacturers' , 'm')
			.' ON '
			.$db->quoteName('m.virtuemart_product_id') . ' = ' . $db->quoteName('p.virtuemart_product_id')
		);
		$query->leftJoin(
				$db->quoteName('#__virtuemart_manufacturers_' . $siteLang , 'ml')
			.' ON '
			.$db->quoteName('ml.virtuemart_manufacturer_id') . ' = ' . $db->quoteName('m.virtuemart_manufacturer_id')
		);
		$db->setQuery($query , 0 , 10 );

		return $db->loadObjectList();

	}

	/**
	 * Отправить в APP
	 * @param   array  $info_products
	 *
	 * @return void
	 * @throws Exception
	 * @since 3.9
	 */
	public function setDescriptionProductResult( array $info_products ){
		$app = \Joomla\CMS\Factory::getApplication();

		$dataArr = [
			'count_Product' => count( $info_products ) ,
			'manufacturers_list' => [] ,
			'min_Price' => [] ,
			'max_Price' => [] ,

		];
		foreach ( $info_products as $infoProduct )
		{
			$dataArr['manufacturers_list'][] = $infoProduct->mf_name ;
			$dataArr['min_Price'][] = $infoProduct->product_price ;
			$dataArr['max_Price'][] = $infoProduct->product_price ;


			/*if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
			{
				echo'<pre>';print_r( $infoProduct );echo'</pre>'.__FILE__.' '.__LINE__;
//				die(__FILE__ .' '. __LINE__ );

			}*/
		}#END FOREACH

		$dataArr['manufacturers_list'] = array_unique( $dataArr['manufacturers_list'] );
		$dataArr['min_Price'] = min( $dataArr['min_Price'] );
		$dataArr['max_Price'] = max( $dataArr['max_Price'] );

		/*if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
		{
			echo'<pre>';print_r( $dataArr );echo'</pre>'.__FILE__.' '.__LINE__;
			
		    die(__FILE__ .' '. __LINE__ );

		}*/

		$app->set('CountProductResult' , $dataArr );

//		$doc = Factory::getDocument();
//		$doc->CountProductResult  ;
	}

}