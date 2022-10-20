<?php
/**
 * @package 	customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2012-2021 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @since		1.0
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Breakdesigns\Customfilters\Admin\Model\UpdateManager;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;

/**
 * The basic view class
 *
 * @author Sakis Terz
 * @since 1.0
 */
class CustomfiltersViewCustomfilters extends JViewLegacy
{
    /**
     * @var array
     * @since 1.0
     */
    protected $items;

    /**
     * @var \Joomla\CMS\Pagination\Pagination
     * @since 1.0
     */
    protected $pagination;

    /**
     * @var \Joomla\CMS\Object\CMSObject
     * @since 1.0
     */
    protected $state;

    /**
     * Form object for search filters
     *
     * @var  \Joomla\CMS\Form\Form
     * @since 2.8.5
     */
    public $filterForm;

    /**
     * @param null $tpl
     * @return mixed|void
     * @throws Exception
     * @since 1.0
     */
    public function display($tpl = null)
    {





        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->displayTypes = $this->get('AllDisplayTypes');
        $this->filterForm    = $this->get('FilterForm');




        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new Exception(implode("\n", $errors), 500);
        }

        $this->params = ComponentHelper::getParams('com_customfilters');
        $this->update_id = UpdateManager::getInstance()->getUpdateId();
        $this->needsdlid = UpdateManager::getInstance()->needsDownloadID();
        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * add toolbar
     *
     * @since 1.0
     */
    public function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_CUSTOMFILTERS'), 'custom_filters');

        if (Factory::getUser()->authorise('core.edit', 'com_customfilters')) {
            JToolbarHelper::custom('customfilters.savefilters', 'save', 'save_f2.png', 'COM_CUSTOMFILTERS_SAVE', false);
        }
        if (Factory::getUser()->authorise('core.edit.state', 'com_customfilters')) {
            JToolbarHelper::publish('customfilters.publish', 'JTOOLBAR_PUBLISH', true);
            JToolbarHelper::unpublish('customfilters.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }

        // Добавить кнопки !
        if (Factory::getUser()->authorise('core.edit', 'com_customfilters')) {
            // Add the optimizer button.
            $icon = 'health';
            $height = '550';
            $width = '875';
            $top = 0;
            $left = 0;
            $onClose = '';
            $alt = 'COM_CUSTOMFILTERS_OPTIMIZER';
            $bar = Toolbar::getInstance('toolbar');
            $bar->appendButton('Popup', $icon, $alt, 'index.php?option=com_customfilters&view=optimizer&tmpl=component',
                $width, $height, $top, $left, $onClose);

            $icon = 'new';
            $height = '550';
            $width = '875';
            $top = 0;
            $left = 0;
            $onClose = '';
            $alt = 'COM_CUSTOMFILTERS_SETTING_SEO';
            $bar = Toolbar::getInstance('toolbar');
            $bar->appendButton('Link', $icon, $alt, 'index.php?option=com_customfilters&view=setting_seo_list',
                $width, $height, $top, $left, $onClose);


	        /**
	         * Кнопка - проверить таблицу
	         */
	        JToolBarHelper::custom(
				$task = 'map_links_lean',
		        $icon = 'shuffle',
				$iconOver = 'shuffle',
				$alt = 'COM_CUSTOMFILTERS_MAP_LINKS_CLEAN_CHECK',
				$listSelect = false
	        );

        }

        if (Factory::getUser()->authorise('core.administrator', 'com_customfilters')) {
            JToolbarHelper::preferences('com_customfilters', '400');
        }

        $this->document->addScript(Uri::base() . 'components/com_customfilters/assets/js/chosen.jquery.min.js');
        $this->document->addStylesheet(Uri::base() . 'components/com_customfilters/assets/css/chosen.min.css');

        // add component scripts
        $this->document->addScript(Uri::base() . 'components/com_customfilters/assets/js/loadVersion.js');
        $this->document->addScript(Uri::base() . 'components/com_customfilters/assets/js/bdpopup.js');
        $this->document->addScript(Uri::base() . 'components/com_customfilters/assets/js/general.js');
        $this->document->addStylesheet(Uri::base() . 'components/com_customfilters/assets/css/bdpopup.css');

        // add choosen
        $script = 'jQuery( function($) {$(".cf-choosen-select").chosen({width:"200px",display_selected_options:false});});';
        $this->document->addScriptDeclaration($script);
    }

    protected function addLibrary(){

    }
}
