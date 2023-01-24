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
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Response\JsonResponse;

class customfiltersViewSetting_filter extends HtmlView
{
	/**
	 * @since 1.0
	 * @var array
	 */
	protected $items;
	/**
	 * Form with settings
	 *
	 * @since  1.0.0
	 * @var    Form
	 */
	protected $form;

	/**
	 * Создание формы - настроек для фильтра
	 * @throws Exception
	 * @since 3.9
	 */
	public function display( $tpl = null )
	{

		/**
		 * @var customfiltersModelSetting_filter $model
		 */
		$model  = $this->getModel();
		$app    = Factory::getApplication();
		$task   = $app->input->get( 'task' , false , 'STRING' );
		$layout = $app->input->get( 'layout' , $tpl , 'STRING' );

		switch ( $task )
		{

			case 'onAjaxSave':
			case 'save' : // Сохранение 
				$app      = \Joomla\CMS\Factory::getApplication();
				$formData = $app->input->get( 'jform' , false , 'RAW' );
				$data     = array();
				parse_str( $formData , $data );

				if ( !$model->save() )
				{
					echo new JsonResponse( null , Text::_( 'COM_CUSTOMFILTERS_SETTING_FILTER_SAVED_ERROR' ) , true );
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
				echo new JsonResponse( $returnData , Text::_( 'COM_CUSTOMFILTERS_SETTING_FILTER_SAVED_SUCCESS' ) , false );
				die();
		}
		/**
		 * customfiltersModelSetting_filter::getItem
		 */
		$this->item = $this->get( 'Item' );
//		$this->CustomFieldValue = $model->getCustomFieldValue( );




		/**
		 * customfiltersModelSetting_filter::getForm
		 */
		$this->form = $this->get( 'Form' );
		$this->form->bind( $this->item->params );

//		echo '<pre>'; print_r( $this->item->params ); echo '</pre>'.__FILE__.' '.__LINE__;
//		echo'<pre>';print_r( $this->CustomFieldValue );echo'</pre>'.__FILE__.' '.__LINE__;
//		die( __FILE__.' '.__LINE__ );

		// for Multilang Site
		if ( JLanguageMultilang::isEnabled() )
		{
			// code Multilang
		}
		$Data[ 'html' ] = $this->loadTemplate( $layout );
		echo new JResponseJson( $Data );
		die();
	}


}