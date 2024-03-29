<?php
/**
 *
 * Customfilters products controller
 *
 * @package		customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2012-2021 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *				customfilters is free software. This version may have been modified
 *				pursuant to the GNU General Public License, and as distributed
 *				it includes or is derivative of works licensed under the GNU
 *				General Public License or other free or open source software
 *				licenses.
 * @version $Id: products.php 1 2011-11-14 12:29:00Z sakis $
 */

// no direct access
use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller'); 

class CustomfiltersControllerProducts extends BaseController{
	
	
	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return bool|JModel|\Joomla\CMS\MVC\Model\BaseDatabaseModel
	 * @since	1.0
	 */
	public function getModel($name = 'Products', $prefix = 'customfiltersModel', $config = array('ignore_request' => true))
	{


        return parent::getModel($name, $prefix, $config);
	}

}