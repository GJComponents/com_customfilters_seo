<?php
/**
 *
 * Customfilters module default tmpl
 *
 * @package        customfilters
 * @author         Sakis Terz
 * @link           http://breakdesigns.net
 * @copyright      Copyright (c) 2012-2021 breakdesigns.net. All rights reserved.
 * @license        http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *                customfilters is free software. This version may have been modified
 *                pursuant to the GNU General Public License, and as distributed
 *                it includes or is derivative of works licensed under the GNU
 *                General Public License or other free or open source software
 *                licenses.
 */

// no direct access
defined( '_JEXEC' ) or die;
require_once JPATH_COMPONENT.DS.'include'.DS.'cfmoduleHelper.php';
$document = JFactory::getDocument();
$document->setMimeEncoding( 'text/html' );
$jinput    = JFactory::getApplication()->input;
$module_id = $jinput->get( 'module_id' , '' , 'int' );

//echo'<pre>';print_r( $module_id );echo'</pre>'.__FILE__.' '.__LINE__;
//die(__FILE__ .' '. __LINE__ );
 
//get and print the module
$module                    = cfModuleHelper::getModule( $module_id );
$attributes[ 'showtitle' ] = 0;
$module->title             = '';
echo JModuleHelper::renderModule( $module , $attributes );

