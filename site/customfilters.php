<?php
/**
 *
 * Customfilters entry point
 *
 * @package        customfilters
 * @author         Sakis Terz
 * @link           http://breakdesigns.net
 * @copyright      Copyright (c) 2012-2021 breakdesigns.net. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterFactoryInterface;

function CustomfiltersBuildRoute(RouterFactoryInterface $router, array &$query)
{
	$segments = array();

	// Определение сегментов маршрута
die(__FILE__ .' '. __LINE__ );

	return $segments;
}

function CustomfiltersParseRoute(RouterFactoryInterface $router, array $segments)
{
	$vars = array();

	// Определение переменных из сегментов маршрута
	die(__FILE__ .' '. __LINE__ );

	return $vars;
}

if (!defined('DEV_IP')) {
	define('DEV_IP',     '***.***.***.***');
}
try
{
    // Code that may throw an Exception or Error.

//     throw new \Exception('Code Exception '.__FILE__.':'.__LINE__) ;
}
catch (\Exception $e)
{
    // Executed only in PHP 5, will not be reached in PHP 7
    echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
    echo'<pre>';print_r( $e );echo'</pre>'.__FILE__.' '.__LINE__;
    die(__FILE__ .' '. __LINE__ );
}


JLoader::registerNamespace( 'GNZ11' , JPATH_LIBRARIES . '/GNZ11' , $reset = false , $prepend = false , $type = 'psr4' );
JLoader::register( 'seoTools' , JPATH_ROOT . '/components/com_customfilters/include/seoTools.php');

// TODO*** - development - Включение отладки и вывод ошибок
if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
{
    $config = \Joomla\CMS\Factory::getConfig();
    $config->set( 'debug', 1 );
    $config->set( 'error_reporting', 'development' );
}#END IF


// Include dependencies
jimport('joomla.application.component.controller');
if (!defined('JPATH_VM_ADMIN'))
{
	define('JPATH_VM_ADMIN', JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart');
}
require_once(JPATH_VM_ADMIN . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php');
VmConfig::loadConfig();

if (!defined('JPATH_VM_SITE'))
{
	define('JPATH_VM_SITE', JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart');
}
if (!defined('JPATH_CF_MODULE'))
{
	define('JPATH_CF_MODULE', JPATH_ROOT . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'mod_cf_filtering');
}
require_once JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'seoTools.php';
require_once JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'tools.php';
require_once JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'Config.php';
require_once JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'ManufacturerHelper.php';
require_once JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'CategoryHelper.php';
require_once JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'input.php';
require_once JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'output.php';
require_once JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'search.php';
require_once JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Products' . DIRECTORY_SEPARATOR . 'ProductsQueryBuilder.php';




$input = JFactory::getApplication()->input;

// $task == display
$task = $input->get('task', 'display', 'cmd') ;
$controller = JControllerLegacy::getInstance('Customfilters');
$controller->execute( $task );

//$profiler = \JProfiler::getInstance('PRO_Application');
//$profiler->mark('End All');

$controller->redirect();
