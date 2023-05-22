<?php
/**
 *
 * Customfilters router
 *
 * @package		customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2012-2022 breakdesigns.net. All rights reserved.
 * @license		See LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();

use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Factory;

require_once JPATH_SITE. DIRECTORY_SEPARATOR. 'components'.DIRECTORY_SEPARATOR. 'com_customfilters'.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'FilterRules.php';

class CustomfiltersRouter extends RouterView
{
	public function __construct($app = null, $menu = null)
	{
		$products = new RouterViewConfiguration('products');
		$this->registerView($products);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
		$this->attachRule(new \FilterRules($this));
	}
}

/**
 * Callback for old sef extensions
 *
 * @param $query
 *
 * @return array
 * @throws Exception
 * @since 1.0.0
 */
function customfiltersBuildRoute(&$query)
{
	$app = Factory::getApplication();
	$router = new CustomfiltersRouter($app, $app->getMenu());
	echo'<pre>';print_r( $query );echo'</pre>'.__FILE__.' '.__LINE__;
	die(__FILE__ .' '. __LINE__ );

	return $router->build($query);
}

/**
 * Callback for old sef extensions
 *
 * @author Sakis Terz
 * @since 1.0
 */
function customfiltersParseRoute($segments)
{
	$app = Factory::getApplication();
	$router = new CustomfiltersRouter($app, $app->getMenu());
	return $router->parse($segments);
}

/**
 * Class offering helper functions to the router's functions
 *
 * @author sakis
 *
 */
class CfRouterHelper
{

	/**
	 *
	 * @var CfRouterHelper
	 */
	protected static $_cfrouter;

	/**
	 *
	 * @var bool|string
	 */
	protected $defaultShopLang;

	/**
	 * Constructor function
	 * since 1.9.0
	 */
	public function __construct()
	{
		if (! class_exists('VmConfig')) {
			require (JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php');
		}
		VmConfig::loadConfig();
	}

	/**
	 * Instantiation function
	 *
	 * @since 1.9.0
	 *
	 */
	public static function getInstance()
	{
		if (empty(self::$_cfrouter)) {
			self::$_cfrouter = new CfRouterHelper();
		}
		return self::$_cfrouter;
	}

	/**
	 * Return the langprefix
	 *
	 * @since 1.9.0
	 */
	public function getDefaultLangPrefix()
	{
		return cftools::getDefaultLanguagePrefix();
	}

	/**
	 * Return the langprefix
	 *
	 * @since 1.9.0
	 */
	public function getLangPrefix()
	{
		return cftools::getCurrentLanguagePrefix();
	}

	/**
	 * Checks if the site's language is the same as the current
	 * If not return the default
	 *
	 * @return false|string
	 */
	public function getDefaultLang()
	{
		if ($this->defaultShopLang == null) {
			if ($this->getLangPrefix() != $this->getDefaultLangPrefix() && VmConfig::$langCount > 1) {
				$this->defaultShopLang = $this->getLangPrefix();
			} else {
				$this->defaultShopLang = false;
			}
		}
		return $this->defaultShopLang;
	}
}