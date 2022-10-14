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

if (!defined('DEV_IP')) {
	define('DEV_IP',     '***.***.***.***');
}


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



$profiler = \JProfiler::getInstance('PRO_Application');
$profiler->mark('End All');


if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
{
//	$pageCreationTime = $profiler->getBuffer();
//	echo'<pre>';print_r( $pageCreationTime );echo'</pre>'.__FILE__.' '.__LINE__;
}



$controller->redirect();
