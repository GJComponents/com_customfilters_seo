<?php

/**
 * @package     Joomla\Component\Example\Helpers
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Joomla\Component\Customfilters\Site\Helpers;


use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Menu\MenuItem;

class CfHelper extends ComponentHelper
{
	/**
	 * Создать SEF-URL - Для названия фильтра
	 *
	 * @param   string  $alias  Строка алиас фильтра
	 *
	 * @return string
	 * @throws Exception
	 * @since version
	 */
	public static function getStringSefUrl(  $alias ):string
	{

		$alias = \GNZ11\Document\Text::rus2translite( $alias );
		$alias = mb_strtolower( $alias );

		$alias = str_replace( [ ' ' , '-' ] , '_' , $alias );
		$alias = str_replace( '/' , '' , $alias );

		$alias = self::cleanSefUrl( $alias );

		return $alias;
	}

	/**
	 * Очистить sef ссылку от лишних символов при создании ссылки или Alias
	 * @param $sef_url
	 * @return array|string|string[]|null
	 * @throws Exception
	 * @since    1.0.0
	 */
	public static function cleanSefUrl( $sef_url ){
		$app = Factory::getApplication();
		$sef_suffix = $app->get('sef_suffix' , false ) ;
		$suffix = '';
		if ( $sef_suffix )
		{
			$suffix = '.html' ;
			$sef_url = str_replace($suffix , '' , $sef_url ) ;
		}#END IF

		// TODO Gartes -- Добавил пропускать скобки "("  ")" -- При добавлении urlencode - перестает нормально работать
		$resReplace =  preg_replace('/[^\/\-_\w\d\(\)]/i', '', $sef_url) . $suffix ;

		return $resReplace ;
	}

	/**
	 * Получить объект Joomla Menu со ссылкой не результаты фильтрации
	 * @return MenuItem
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function getCustomFilterMenuMenuItem(): MenuItem
	{
		$app     = Factory::getApplication('Site');
		$menus   = $app->getMenu('Site');
		$menuItemArray = $menus->getItems('link', 'index.php?option=com_customfilters&view=products');

		return $menuItemArray[0];
	}
	public static function getParseUrl()
	{
		die(__FILE__ .' '. __LINE__ );

	}
}