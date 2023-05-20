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
 * @date       24.01.23 13:09
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2023 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/

class HelperSetting_city
{
	public static $OneLevel = [] ;
	protected static $ParamsComponent = false  ;

	public static function getParamsComponent(){
		if ( !self::$ParamsComponent )
		{
			self::$ParamsComponent = JComponentHelper::getParams('com_customfilters');
		}#END IF
		return self::$ParamsComponent ;
	}

	/**
	 * Для фильтра по городам создать из многомерного массива одномерный
	 * @param   array  $array
	 * @param   bool|string   $keyAlias
	 *
	 * @return array
	 * @since 3.9
	 */
	public static function getOneLevelParams(   $array  , $keyAlias = false    ){
		$paramsComponent = self::getParamsComponent();
		$default_h1_tag = $paramsComponent->get('default_h1_tag' , '{{CATEGORY_NAME}} - {{FILTER_VALUE_LIST}}') ;
		$default_title = $paramsComponent->get('default_title' , '{{CATEGORY_NAME}} - {{FILTER_VALUE_LIST}}') ;
		$default_description = $paramsComponent->get('default_description' , '{{CATEGORY_NAME}} - {{FILTER_VALUE_LIST}}') ;
		$default_keywords = $paramsComponent->get('default_keywords' , '{{CATEGORY_NAME}} - {{FILTER_VALUE_LIST}}') ;

		foreach ( $array as $alias => $below )
		{
			if ( empty($below[ 'use' ]) )
			{
				$below[ 'use' ] = 0 ;
			}#END IF
			self::$OneLevel[ $alias ][ 'use' ]                 = $below[ 'use' ];
			self::$OneLevel[ $alias ][ 'default_h1_tag' ]      = trim(  empty($below[ 'default_h1_tag' ])?  $default_h1_tag : $below[ 'default_h1_tag' ]   );
			self::$OneLevel[ $alias ][ 'default_title' ]       = trim( empty($below[ 'default_title' ])?  $default_title : $below[ 'default_title' ]    );
			self::$OneLevel[ $alias ][ 'default_description' ] = trim( empty($below[ 'default_description' ])?  $default_description : $below[ 'default_description' ]   );
			self::$OneLevel[ $alias ][ 'default_keywords' ]    = trim( empty($below[ 'default_keywords' ])?  $default_keywords : $below[ 'default_keywords' ]  );

			unset( $below[ 'use' ] );
			unset( $below[ 'default_h1_tag' ] );
			unset( $below[ 'default_title' ] );
			unset( $below[ 'default_description' ] );
			unset( $below[ 'default_keywords' ] );

			if ( !count( $below ) ) continue ;#END IF

			self::getOneLevelParams( $below );
        }#END FOREACH
		return self::$OneLevel ;

	}

	public static function getCityByAlias( $alias ){
		$db = JFactory::getDbo();
		$Query = $db->getQuery( true );
		$Query->select('*')->from( $db->quoteName('#__cf_customfields_city'))
			->where(
				$db->quoteName('alias') . '='. $db->quote( $alias )
			);
		$db->setQuery( $Query );
		return $db->loadAssoc();
	}
}