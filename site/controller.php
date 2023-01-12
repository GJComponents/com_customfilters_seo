<?php
/**
 *
 * Customfilters basic controller
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
 */

// no direct access
defined('_JEXEC') or die();

// Include dependancies
jimport('joomla.application.component.controller');

class CustomfiltersController extends \Joomla\CMS\MVC\Controller\BaseController
{

    protected $default_view = 'products';

    function __construct($config = array())
    {
        parent::__construct($config);
    }

    /**
     * (non-PHPdoc)
     *
     * @see JControllerLegacy::display()
     */
    function display( $cachable = false, $urlparams = false)
    {

	    $cachable = true ;
	    if ( !$urlparams )
	    {
		    $app = \Joomla\CMS\Factory::getApplication();
		    $juri = \Joomla\CMS\Uri\Uri::getInstance();
		    $filterUrl = $juri->getPath();
		    $app->input->set('filter-url' , md5( $filterUrl ) );

		    $urlparams = [
//			    'Itemid' => 'INT',
//			    'virtuemart_category_id' => 'ARRAY',
//			    'virtuemart_manufacturer_id' => 'ARRAY',
			    'filter-url' => 'STRING',
		    ];

	    }#END IF

		// Отключить CACH -  для DEV
		if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
		{
//			$cachable = false ;
		}
		

        $input = JFactory::getApplication()->input;
        $viewName = $input->get('view', $this->default_view);
        if ($viewName != 'module' && $viewName != 'products')
            $viewName = $this->default_view;
        $input->set('view', $viewName);

        parent::display( $cachable, $urlparams);
//        return $this;
    }

    /**
     * (non-PHPdoc)
     *
     * @see JControllerLegacy::getModel()
     * @since 3.9
     */
    public function getModel( $name = 'Products', $prefix = 'customfiltersModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }
}