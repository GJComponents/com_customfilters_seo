<?php
/**
 *
 * Customfilters module view
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
 * @version $Id: view.html.php 15 2012-10-8 20:00:00Z sakis $
 */

// No direct access
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

//import the view class
jimport('joomla.application.component.view');

class CustomfiltersViewModule extends HtmlView{


	public function display($tpl = null){
		parent::display($tpl);
	}
}