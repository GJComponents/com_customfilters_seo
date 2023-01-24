<?php

/***********************************************************************************************************************
 * ╔═══╗ ╔══╗ ╔═══╗ ╔════╗ ╔═══╗ ╔══╗  ╔╗╔╗╔╗ ╔═══╗ ╔══╗   ╔══╗  ╔═══╗ ╔╗╔╗ ╔═══╗ ╔╗   ╔══╗ ╔═══╗ ╔╗  ╔╗ ╔═══╗ ╔╗ ╔╗ ╔════╗
 * ║╔══╝ ║╔╗║ ║╔═╗║ ╚═╗╔═╝ ║╔══╝ ║╔═╝  ║║║║║║ ║╔══╝ ║╔╗║   ║╔╗╚╗ ║╔══╝ ║║║║ ║╔══╝ ║║   ║╔╗║ ║╔═╗║ ║║  ║║ ║╔══╝ ║╚═╝║ ╚═╗╔═╝
 * ║║╔═╗ ║╚╝║ ║╚═╝║   ║║   ║╚══╗ ║╚═╗  ║║║║║║ ║╚══╗ ║╚╝╚╗  ║║╚╗║ ║╚══╗ ║║║║ ║╚══╗ ║║   ║║║║ ║╚═╝║ ║╚╗╔╝║ ║╚══╗ ║╔╗ ║   ║║
 * ║║╚╗║ ║╔╗║ ║╔╗╔╝   ║║   ║╔══╝ ╚═╗║  ║║║║║║ ║╔══╝ ║╔═╗║  ║║─║║ ║╔══╝ ║╚╝║ ║╔══╝ ║║   ║║║║ ║╔══╝ ║╔╗╔╗║ ║╔══╝ ║║╚╗║   ║║
 * ║╚═╝║ ║║║║ ║║║║    ║║   ║╚══╗ ╔═╝║  ║╚╝╚╝║ ║╚══╗ ║╚═╝║  ║╚═╝║ ║╚══╗ ╚╗╔╝ ║╚══╗ ║╚═╗ ║╚╝║ ║║    ║║╚╝║║ ║╚══╗ ║║ ║║   ║║
 * ╚═══╝ ╚╝╚╝ ╚╝╚╝    ╚╝   ╚═══╝ ╚══╝  ╚═╝╚═╝ ╚═══╝ ╚═══╝  ╚═══╝ ╚═══╝  ╚╝  ╚═══╝ ╚══╝ ╚══╝ ╚╝    ╚╝  ╚╝ ╚═══╝ ╚╝ ╚╝   ╚╝
 *----------------------------------------------------------------------------------------------------------------------
 * @author     Gartes | sad.net79@gmail.com | Telegram : @gartes
 * @date       12.11.22 11:49
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\Toolbar;

class CustomfiltersViewSetting_city extends HtmlView
{
	/**
	 * @var array
	 * @since 1.0
	 */
	protected $item;
	/**
	 * Form with settings
	 *
	 * @var    Form
	 * @since  1.0.0
	 */
	protected $form;
	/**
	 * @var array Список городов
	 * @since 3.9
	 */
	protected $ListCity = [] ;

	public function display($tpl = null)
	{
		JLoader::registerNamespace( 'FiltersSeoNamespace' , JPATH_ADMINISTRATOR . '/components/com_customfilters/libraries' , $reset = false , $prepend = false , $type = 'psr4' );
		$this->document->addStyleSheet('/administrator/components/com_customfilters/assets/css/setting_city.css');

		$app = \Joomla\CMS\Factory::getApplication();
		$app->enqueueMessage('Константы: 
			<br> {{CATEGORY_NAME}}  - название категории, 
			<br> {{FILTER_VALUE_LIST}} - будет заменена названием города, 
			<br> {{TEXT_PROP}} - будет заменена названием Текст свойства в Дополнительных настройках.
			');


//		JLoader::register('CustomfiltersModelForms_add' , JPATH_ADMINISTRATOR . '/components/com_customfilters/models/forms_add.php');
//		$CustomfiltersModelForms_add = new CustomfiltersModelForms_add();
//		$this->setModel( $CustomfiltersModelForms_add ) ;
//		$formsAddModel = $this->getModel('forms_add');


		/**
		 * @var CustomfiltersModelSetting_city $model
		 */
		$model = $this->getModel();
		$this->ListCity = $model->getListCity();
		/**
		 * CustomfiltersModelSetting_city::getItem
		 */
		$this->item = $this->get('Item');

		if ( !isset( $this->item->params['use_city_setting']  ) )
		{
			$this->item->params['use_city_setting'] = $model->_setDefaultParams( $this->ListCity  );

		}#END IF

		$this->paramsCityList = $model->_getOneLevelParams( $this->item->params[ 'use_city_setting' ] );


		/**
		 * CustomfiltersModelSetting_city::getForm
		 */
		$this->form = $this->get( 'Form' );

		$this->_path[ 'template' ][] = JPATH_ADMINISTRATOR.'/components/com_customfilters/views/forms_add/tmpl';

		// for Multilang Site
		if (Multilanguage::isEnabled())
		{
			// code Multilang
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Install Toolbar
	 * @return void
	 * @since 3.9
	 */
	public function addToolbar()
	{
		$bar = Toolbar::getInstance('toolbar');
		JToolbarHelper::title(JText::_('COM_CUSTOMFILTERS_SETTING_CITY'), 'address large-icon');

		JToolbarHelper::divider();
		JToolbarHelper::apply('setting_city.apply');
		// Сохранить и закрыть
		JToolbarHelper::save('setting_city.save', 'JTOOLBAR_SAVE'  );

		// Закрыть
		JToolbarHelper::cancel('setting_city.cancel', 'JTOOLBAR_CLOSE');

		/**
		 * Кнопка - Добавить|Перестроить фильтр в карту сайта
		 */
		JToolbarHelper::custom('setting_city.add_area_base', 'plus-circle' , 'plus-2' ,
			'COM_CUSTOMFILTERS_ADD_AREA_BASE' , false   );

//		echo'<pre>';print_r( $this->item->id );echo'</pre>'.__FILE__.' '.__LINE__;
//		die(__FILE__ .' '. __LINE__ );
		/**
		 * Кнопка - Добавить|Перестроить фильтр в карту сайта
		 */
		JToolbarHelper::custom('setting_city.save_add_to_map', 'plus-2' , 'plus-2' ,
			'COM_CUSTOMFILTERS_ADD_FILTER_CITY_SEO_TO_CART' , false   );

		// Стиль для зеленого плюса
		$this->document->addStyleDeclaration('span.icon-plus-2:before{color: #378137;}');

	}
}