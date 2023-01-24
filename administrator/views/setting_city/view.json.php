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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Response\JsonResponse;

class CustomfiltersViewSetting_city extends HtmlView
{
	/**
	 * @var array
	 * @since 1.0
	 */
	protected $items;
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
	public $ListCity;

	/**
	 * @throws Exception
	 * @since 3.9
	 */
	public function display($tpl = null)
	{

		$this->_path[ 'template' ][] = JPATH_ADMINISTRATOR.'/components/com_customfilters/views/forms_add/tmpl';

		JLoader::registerNamespace( 'FiltersSeoNamespace' , JPATH_ADMINISTRATOR.'/components/com_customfilters/libraries' , $reset = false , $prepend = false , $type = 'psr4' );


		/**
		 * @var CustomfiltersModelSetting_city $model
		 */
		$model = $this->getModel();

		$app         = Factory::getApplication();
		$task        = $app->input->get( 'task' , false , 'STRING' );
		$parentAlias = $app->input->get( 'parentAlias' , false , 'STRING' );

		switch ( $task )
		{
			/**
			 * Получить подсказки для AutoComplete - Родительского региона для формы "Создание нового региона"
			 */
			case 'onAjaxGetParentsAreaAutoComplete' :
				$string     = $app->input->get( 'query' , false , 'STRING' );
				$objectList = $model->getParentsAreaAutoComplete( $string );
				echo new JsonResponse( $objectList , null , false );
				die();
				break;
			/**
			 * Сохранить новый регион форма "Создание нового региона"
			 */
			case 'onAjaxSaveNewArea' :
				$formData = $app->input->get( 'formData' , false , 'RAW' );
				$data     = array();
				parse_str( $formData , $data );

				$result = $model->saveNewArea( $data[ 'jform' ] );
				$error  = !$result;
				if ( $error )
				{
					$app->enqueueMessage( 'Не удалось сохранить регион "'.$data[ 'jform' ][ 'name' ].'"' , 'error' );
				}
				else
				{
					$app->enqueueMessage( 'Новый регион добавлен' );
				}#END IF
				echo new JsonResponse( null , null , $error );
				die();
				break;
			/**
			 * Получить форму для добавления региона
			 */
			case 'onAjaxAddAreaBase' :
				$Data[ 'form_html' ] = $this->loadTemplate( 'form_add_area_base' );
				echo new JsonResponse( $Data , null , false );
				die();

				break;
			/**
			 * Создание Alias - При вводе - названия фильтра
			 */
			case 'onKeyupSetTranslite':
				$string = $app->input->get( 'val' , false , 'STRING' );
				$string = $model->getTranslite( $string );
				$string = strtolower( $string );

				$string = str_replace( ' ' , '-' , $string );
				$string = preg_replace( '/[^-a-zа-яё\d_]/ui' , '' , $string );
				echo new JsonResponse( $string , null , false );
				die();
				break;
			case 'onAjaxSave':
			case 'save' : // Сохранение настроек фильтра по городам

				$formData = $app->input->get( 'jform' , false , 'RAW' );
				$data     = array();
				parse_str( $formData , $data );

				if ( !$model->save() )
				{
					echo new JsonResponse( null , Text::_( 'COM_CUSTOMFILTERS_FILTERS_SETTING_CITY_SAVED_ERROR' ) , true );
					die();
				}#END IF

				$itemID = $data[ 'jform' ][ 'id' ];
				if ( !$itemID )
				{
					// Получаем данные после сохранения
					$item   = $model->getItem();
					$itemID = $item->id;
				}#END IF


				$returnData = [
					'id' => $itemID
				];
				echo new JsonResponse( $returnData , Text::_( 'COM_CUSTOMFILTERS_FILTERS_SETTING_CITY_SAVED_SUCCESS' ) , false );
				die();

		}


		/**
		 * CustomfiltersModelSetting_city::getItem
		 */
		$this->item     = $this->get( 'Item' );
		$this->ListCity = $model->getListCity();

		if ( $task == 'onAjaxGetChildrenArea' && $parentAlias == 'doneckaya-oblast' )
		{


		}#END IF



		if ( !isset( $this->item->params[ 'use_city_setting' ] ) )
		{
//			$this->item->params[ 'use_city_setting' ] = $model->_setDefaultParams( $this->ListCity );

		}#END IF



		/**
		 * CustomfiltersModelSetting_city::getForm
		 */
		$this->form = $this->get( 'Form' );

		// for Multilang Site
		if ( JLanguageMultilang::isEnabled() )
		{
			// code Multilang
		}


		$this->paramsCityList = $model->_getOneLevelParams( $this->item->params[ 'use_city_setting' ] );


		foreach ( $this->ListCity as    $city )
		{
			$alias = $city['alias'] ;
			if ( !key_exists( $alias , $this->paramsCityList ) )
			{
				$this->paramsCityList[$alias] = $model->addDefaultParamsCity( $city );
//				$this->paramsCityList[$alias] = array_merge( $city , $defaultParams ) ;
			}#END IF
		}#END FOREACH

		$layout = $app->input->get( 'layout' , null , 'STRING' );
		$Data[ 'form_html' ] = $this->loadTemplate( $layout );

		echo new JResponseJson( $Data );
		die();
		parent::display( $tpl );
	}



}