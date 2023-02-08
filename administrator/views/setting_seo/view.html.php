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
 * @date       07.02.23 19:27
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

class CustomfiltersViewSetting_seo extends HtmlView
{

	/**
	 * @since 1.0
	 * @var array
	 */
	public $item;
	/**
	 * Form with settings
	 *
	 * @since  1.0.0
	 * @var    Form
	 */
	public $form;
	/**
	 * The model state
	 *
	 * @since  1.0.0
	 * @var    Joomla\CMS\Object\CMSObject
	 */
	public $state;
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
	 * @since  1.0.0
	 * @var    string
	 */
	public $sidebar = '';


	public function display( $tpl = null )
	{
		$this->document->addStyleSheet( '/administrator/components/com_customfilters/assets/css/setting_seo.css' );
		/**
		 * @var CustomfiltersModelSetting_seo $model
		 */
		$model       = $this->getModel();
		$this->state = $model->getState();



		/**
		 * CustomfiltersModelSetting_seo::getItem
		 */
		 $this->item = $this->get('Item');


		/**
		 * customfiltersModelSetting_seo::getForm
		 */
		$this->form = $this->get( 'Form' );

		$this->form->bind( $this->item ) ;

//		echo'<pre>';print_r( $this->form );echo'</pre>'.__FILE__.' '.__LINE__;
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
		JToolbarHelper::title( JText::_( 'COM_CUSTOMFILTERS_SETTING_SEO' ) , 'joomla large-icon' );

		JToolbarHelper::divider();
		// Сохранить
		JToolbarHelper::apply( 'setting_seo.apply' );
		// Сохранить и закрыть
		JToolbarHelper::save( 'setting_seo.save' , 'JTOOLBAR_SAVE' );
		JToolbarHelper::cancel( 'setting_seo.cancel' , 'JTOOLBAR_CLOSE' );
	}
}