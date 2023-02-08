<?php
/**
 *
 * The basic view file
 *
 * @package 	customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2012-2021 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *				customfilters is free software. This version may have been modified
 *				pursuant to the GNU General Public License, and as distributed
 *				it includes or is derivative of works licensed under the GNU
 *				General Public License or other free or open source software
 *				licenses.
 * @since		1.9.5
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
// Load the view framework
jimport('joomla.application.component.view');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\Registry\Registry;


/**
 * The basic view class
 *
 * @author Sakis Terz
 * @since 1.0
 */
class CustomfiltersViewSetting_seo_list extends HtmlView
{
    /**
     * Array with profiles
     *
     * @var    array
     * @since  1.0.0
     */
	public $items = [];

    /**
     * The model state
     *
     * @var    Registry
     * @since  1.0.0
     */
    public $state;

    /**
     * Pagination object
     *
     * @var    Pagination
     * @since  1.0.0
     */
    protected $pagination;

    /**
     * Companies helper
     *
     * @var    Vm_seo_product_filter_grtHelper
     * @since  1.0.0
     */
    protected $helper;

    /**
     * The sidebar to show
     *
     * @var    string
     * @since  1.0.0
     */
    public $sidebar = '';

    /**
     * Form with filters
     *
     * @var    Form
     * @since  1.0.0
     */
    public $filterForm;

    /**
     * List of active filters
     *
     * @var    array
     * @since  1.0.0
     */
    public $activeFilters = [];

    /**
     * Actions registry
     *
     * @var    Registry
     * @since  1.0.0
     */
	public $canDo;



    /**
     * Display the view
     * @since 1.0
     * @return void
     */
    public function display($tpl = null)
    {



        /**
         * @var CustomfiltersModelSetting_seo_list $model
         */
        $model               = $this->getModel();

        $this->items         = $model->getItems();
        $this->state         = $model->getState();
        $this->pagination    = $model->getPagination();
        $this->filterForm    = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();
        $this->canDo         = ContentHelper::getActions('com_customfilters');

        $this->addToolbar();


//        echo'<pre>';print_r( $model );echo'</pre>'.__FILE__.' '.__LINE__ .'<br>';
//        die( __FILE__ .' : ' . __LINE__);

        parent::display($tpl);
    }

    /**
     * Create the Toolbar
     * @since 1.0
     */
    public function addToolbar()
    {
        JToolBarHelper::title(Text::_('COM_CUSTOMFILTERS_SETTING_SEO_LIST'), 'custom_filters');
        $this->document->setTitle(Text::_('COM_CUSTOMFILTERS_SETTING_SEO_LIST'));

        JFactory::getApplication()->input->set('hidemainmenu', true);

        if ( $this->canDo->get('core.create' ))
        {
            ToolbarHelper::addNew('setting_seo.add');
        }
        if ($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own'))
        {
            ToolbarHelper::editList('setting_seo.edit');
        }

        if ($this->canDo->get('core.edit.state'))
        {
            ToolbarHelper::publish('setting_seo.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('setting_seo.unpublish', 'JTOOLBAR_UNPUBLISH', true);
//            ToolbarHelper::archiveList('setting_seo.archive');
        }

        ToolbarHelper::cancel('setting_seo_list.cancel', 'COM_CUSTOMFILTERS_SETTING_SEO_TO_COMPONENT');

        $this->document->addScript(JURI::base() . 'components/com_customfilters/assets/js/general.js');
        return $this;
    }
}
