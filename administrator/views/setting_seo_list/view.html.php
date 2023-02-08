<?php


/***********************************************************************************************************************
 *  ///////////////////////////╭━━━╮╱╱╱╱╱╱╱╱╭╮╱╱╱╱╱╱╱╱╱╱╱╱╱╭━━━╮╱╱╱╱╱╱╱╱╱╱╱╱╭╮////////////////////////////////////////
 *  ///////////////////////////┃╭━╮┃╱╱╱╱╱╱╱╭╯╰╮╱╱╱╱╱╱╱╱╱╱╱╱╰╮╭╮┃╱╱╱╱╱╱╱╱╱╱╱╱┃┃////////////////////////////////////////
 *  ///////////////////////////┃┃╱╰╯╭━━╮╭━╮╰╮╭╯╭━━╮╭━━╮╱╱╱╱╱┃┃┃┃╭━━╮╭╮╭╮╭━━╮┃┃╱╭━━╮╭━━╮╭━━╮╭━╮////////////////////////
 *  ///////////////////////////┃┃╭━╮┃╭╮┃┃╭╯╱┃┃╱┃┃━┫┃━━┫╭━━╮╱┃┃┃┃┃┃━┫┃╰╯┃┃┃━┫┃┃╱┃╭╮┃┃╭╮┃┃┃━┫┃╭╯////////////////////////
 *  ///////////////////////////┃╰┻━┃┃╭╮┃┃┃╱╱┃╰╮┃┃━┫┣━━┃╰━━╯╭╯╰╯┃┃┃━┫╰╮╭╯┃┃━┫┃╰╮┃╰╯┃┃╰╯┃┃┃━┫┃┃/////////////////////////
 *  ///////////////////////////╰━━━╯╰╯╰╯╰╯╱╱╰━╯╰━━╯╰━━╯╱╱╱╱╰━━━╯╰━━╯╱╰╯╱╰━━╯╰━╯╰━━╯┃╭━╯╰━━╯╰╯/////////////////////////
 *  ///////////////////////////╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱┃┃//  (C) 2023  ///////////////////
 *  ///////////////////////////╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╰╯/////////////////////////////////
 *----------------------------------------------------------------------------------------------------------------------
 * @author     Gartes | sad.net79@gmail.com | Telegram : @gartes
 * @date       07.02.23 14:39
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2023 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\Registry\Registry;

class CustomfiltersViewSetting_seo_list extends HtmlView
{

	/**
	 * @since 1.0
	 * @var array
	 */
	public $items = [];
	/**
	 * @since 1.0
	 * @var Joomla\CMS\Pagination\Pagination
	 */
	public $pagination;
	/**
	 * The model state
	 *
	 * @since  1.0.0
	 * @var    Joomla\CMS\Object\CMSObject
	 */
	public $state;
	/**
	 * @since 3.9
	 * @var Joomla\CMS\Form\Form
	 */
	public $filterForm ;
	/**
	 * @since 3.9
	 * @var array
	 */
	public $activeFilters ;
	/**
	 * Actions registry
	 *
	 * @since  1.0.0
	 * @var    Registry
	 */
	public $canDo;
	/**
	 * The sidebar to show
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $sidebar = '';
	public function display( $tpl = null )
	{
		$this->document->addStyleSheet( '/administrator/components/com_/assets/css/setting_seo_list.css' );
		/**
		 * @var CustomfiltersModelSetting_seo_list $model
		 */
		$model = $this->getModel();

		/**
		 * CustomfiltersModelSetting_seo_list::getItems
		 */
		$this->items         = $this->get( 'Items' );
		$this->pagination    = $this->get( 'Pagination' );
		$this->state         = $this->get( 'State' );
		$this->filterForm    = $this->get( 'FilterForm' );
		$this->activeFilters = $this->get( 'ActiveFilters' );

//		echo'<pre>';print_r( $this->state );echo'</pre>'.__FILE__.' '.__LINE__;
//		die(__FILE__ .' '. __LINE__ );


		// Какие права доступа есть у этого пользователя? Что она может делать?
		// What Access Permissions does this user have? What can (s)he do?
		$this->canDo = ContentHelper::getActions( 'com_customfilters' );


		// for Multilang Site
		if ( Multilanguage::isEnabled() )
		{
			// code Multilang
		}

		$this->addToolbar();
		parent::display( $tpl );
	}

	/**
	 * Install Toolbar
	 * @return void
	 * @since 3.9
	 */
	public function addToolbar()
	{
		$bar = Toolbar::getInstance( 'toolbar' );
		JToolbarHelper::title( JText::_( 'COM_CUSTOMFILTERS_SETTING_SEO_LIST_LINKS_FILTER' ) , 'joomla large-icon' );

		JToolbarHelper::divider();
		// Сохранить
		JToolbarHelper::apply( 'setting_seo_list.apply' );
		// Сохранить и закрыть
		JToolbarHelper::save( 'setting_seo_list.save' , 'JTOOLBAR_SAVE' );
		JToolbarHelper::cancel( 'setting_seo_list.cancel' , 'JTOOLBAR_CLOSE' );
	}
}