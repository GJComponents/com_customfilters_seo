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
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;

/**
 * The basic view class
 *
 * @author Gartes
 * @since 1.0
 */
class CustomfiltersViewSetting_city_list extends HtmlView
{
    /**
     * @var array
     * @since 1.0
     */
    protected $items;

    /**
     * @var Pagination
     * @since 1.0
     */
    protected $pagination;

    /**
     * @var CMSObject
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
	 * @var array Список поддерживаемых языков
	 * @since version
	 */
	protected $knownLanguages = [];

	/**
     * @param null $tpl
     * @return mixed|void
     * @throws Exception
     * @since 1.0
     */
    public function display($tpl = null)
    {
	    // Загрузка jui/jquery.min.js +  libraries/cms/html/jquery.php
//	    JHtmlJquery::framework(false);
	    // Загрузка  jui/jquery.ui.core.min.js + jquery.ui.sortable.min.js +
//		JHtmlJquery::ui(array('core', 'sortable'));
		// Управление subform-repeatable
//	    \Joomla\CMS\HTML\HTMLHelper::script( 'system/subform-repeatable.js', array('version' => 'auto', 'relative' => true) );



	    $this->document->addStyleSheet('/administrator/components/com_customfilters/assets/css/setting_city.css');
	    /**
	     * CustomfiltersModelSetting_city_list::getItems
	     */
        $this->items = $this->get('Items');

        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->displayTypes = $this->get('AllDisplayTypes');
//        $this->filterForm    = $this->get('FilterForm');



		// for Multilang Site
	    if ( JLanguageMultilang::isEnabled() )
	    {
		    // Получить все установленные языки
		    // $this->knownLanguages = \Joomla\CMS\Language\LanguageHelper::getKnownLanguages( JPATH_SITE ) ;
		    // Получить все опубликованные языки
		    $this->knownLanguages   = \Joomla\CMS\Language\LanguageHelper::getLanguages();
			$AllLanguage  = [
				'title' => 'All Language',
				'sef' => '*',
		    ];
		    array_unshift($this->knownLanguages , $AllLanguage );
	    }

//		echo'<pre>';print_r( $this->knownLanguages );echo'</pre>'.__FILE__.' '.__LINE__;
//		die(__FILE__ .' '. __LINE__ );


        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
	        $errors = array_unique($errors);
	        echo'<pre>';print_r( $errors );echo'</pre>'.__FILE__.' '.__LINE__;

	        echo'<pre>';print_r( $this );echo'</pre>'.__FILE__.' '.__LINE__;
	        echo'<pre>';print_r( $this->knownLanguages );echo'</pre>'.__FILE__.' '.__LINE__;
	        die(__FILE__ .' '. __LINE__ );
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
	    JToolbarHelper::deleteList('COM_CUSTOMFILTERS_SETTING_CITY_LIST_DELETE_MES', 'setting_city_list.delete', 'COM_CUSTOMFILTERS_SETTING_CITY_LIST_DELETE');

        // Добавить кнопки !
        if (Factory::getUser()->authorise('core.edit', 'com_customfilters')) {

	        $bar = Toolbar::getInstance('toolbar');

	        /**
	         * К списку фильтров
	         */
			$bar->appendButton(
				'Link',
		        'exit' , // $icon
				'COM_CUSTOMFILTERS_LIST_RETURN', // $alt
				'index.php?option=com_customfilters&view=customfilters'
			);

	        /**
	         * Кнопка - добавить фильтр CITY SEO
	         */
	        /*JToolBarHelper::custom(
				$task = 'add_filter_city_seo',
		        $icon = 'plus-2',
				$iconOver = 'plus-2',
				$alt = 'COM_CUSTOMFILTERS_ADD_FILTER_CITY_SEO',
				$listSelect = false
	        );
			*/
	        // Стиль для зеленого плюса
	        $this->document->addStyleDeclaration('span.icon-plus-2:before{color: #378137;}');
	        /**
	         * Кнопка - добавить фильтр CITY SEO - No Modal
	         */
	        $bar->appendButton(
		        'Link',
		        'plus-2' , // $icon
		        'COM_CUSTOMFILTERS_ADD_FILTER_CITY_SEO', // $alt
		        'index.php?option=com_customfilters&view=setting_city'
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

}
