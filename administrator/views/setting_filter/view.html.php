<?php


/***********************************************************************************************************************
 *  ///////////////////////////╭━━━╮╱╱╱╱╱╱╱╱╭╮╱╱╱╱╱╱╱╱╱╱╱╱╱╭━━━╮╱╱╱╱╱╱╱╱╱╱╱╱╭╮////////////////////////////////////////
 *  ///////////////////////////┃╭━╮┃╱╱╱╱╱╱╱╭╯╰╮╱╱╱╱╱╱╱╱╱╱╱╱╰╮╭╮┃╱╱╱╱╱╱╱╱╱╱╱╱┃┃////////////////////////////////////////
 *  ///////////////////////////┃┃╱╰╯╭━━╮╭━╮╰╮╭╯╭━━╮╭━━╮╱╱╱╱╱┃┃┃┃╭━━╮╭╮╭╮╭━━╮┃┃╱╭━━╮╭━━╮╭━━╮╭━╮////////////////////////
 *  ///////////////////////////┃┃╭━╮┃╭╮┃┃╭╯╱┃┃╱┃┃━┫┃━━┫╭━━╮╱┃┃┃┃┃┃━┫┃╰╯┃┃┃━┫┃┃╱┃╭╮┃┃╭╮┃┃┃━┫┃╭╯////////////////////////
 *  ///////////////////////////┃╰┻━┃┃╭╮┃┃┃╱╱┃╰╮┃┃━┫┣━━┃╰━━╯╭╯╰╯┃┃┃━┫╰╮╭╯┃┃━┫┃╰╮┃╰╯┃┃╰╯┃┃┃━┫┃┃/////////////////////////
 *  ///////////////////////////╰━━━╯╰╯╰╯╰╯╱╱╰━╯╰━━╯╰━━╯╱╱╱╱╰━━━╯╰━━╯╱╰╯╱╰━━╯╰━╯╰━━╯┃╭━╯╰━━╯╰╯/////////////////////////
 *  ///////////////////////////╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱┃┃//  (C) 2022  ///////////////////
 *  ///////////////////////////╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╰╯/////////////////////////////////
 *----------------------------------------------------------------------------------------------------------------------
 * @author     Gartes | sad.net79@gmail.com | Telegram : @gartes
 * @date       20.12.22 11:15
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\Toolbar;

class CustomfiltersViewSetting_filter extends HtmlView
{

	/**
	 * @since 1.0
	 * @var array
	 */
	protected $item;
	/**
	 * Form with settings
	 *
	 * @since  1.0.0
	 * @var    Form
	 */
	protected $form;


	public function display( $tpl = null )
	{
		$this->document->addStyleSheet( '/administrator/components/com_customfilters/assets/css/setting_filter.css' );
		/**
		 * CustomfiltersModelSetting_filter::getItem
		 */
		// $this->item = $this->get('Item');
		/**
		 * customfiltersModelSetting_filter::getForm
		 */
		$this->form = $this->get( 'Form' );

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
		JToolbarHelper::title( JText::_( 'COM_CUSTOMFILTERS_SETTING_FILTER' ) , 'joomla large-icon' );

		JToolbarHelper::divider();
		// Сохранить
		JToolbarHelper::apply( 'setting_filter.apply' );
		// Сохранить и закрыть
		JToolbarHelper::save( 'setting_filter.save' , 'JTOOLBAR_SAVE' );
		JToolbarHelper::cancel( 'setting_filter.cancel' , 'JTOOLBAR_CLOSE' );
	}
}