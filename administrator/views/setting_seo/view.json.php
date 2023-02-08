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
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Response\JsonResponse;

class customfiltersViewSetting_seo extends HtmlView
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
	 * @throws Exception
	 * @since 3.9
	 */
	public function display( $tpl = null )
	{
		/**
		 * @var customfiltersModelSetting_seo $model
		 */
		$model  = $this->getModel();
		$app    = Factory::getApplication();
		$task   = $app->input->get( 'task' , false , 'STRING' );
		$layout = $app->input->get( 'layout' , $tpl , 'STRING' );
		switch ( $task )
		{
			case 'onAjaxGetAllValueField':
				$this->getCustomFieldAllValues();
				break ;
			case 'onAjaxSave':
			case 'save' : // Сохранение 
				$formData = $app->input->get( 'jform' , false , 'RAW' );
				$data     = array();
				parse_str( $formData , $data );

				if ( !$model->save() )
				{
					echo new JsonResponse( null , Text::_( 'COM_CUSTOMFILTERS_SETTING_SEO_SAVED_ERROR' ) , true );
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
				echo new JsonResponse( $returnData , Text::_( 'COM_CUSTOMFILTERS_SETTING_SEO_SAVED_SUCCESS' ) , false );
				die();
		}
		/**
		 * CustomfiltersModel::getItem
		 */
		$this->item = $this->get( 'Item' );
		/**
		 * CustomfiltersModel::getForm
		 */
		$this->form = $this->get( 'Form' );

		// for Multilang Site
		if ( JLanguageMultilang::isEnabled() )
		{
			// code Multilang
		}
		$Data[ 'html' ] = $this->loadTemplate( $layout );
		echo new JResponseJson( $Data );
		die();
	}

	/**
	 * Получить все значения для VM Custom Field by ID
	 * @return void
	 * @throws Exception
	 * @since 3.9
	 */
	public function getCustomFieldAllValues(){
		JLoader::register( 'HelperSetting_seo' , JPATH_ADMINISTRATOR . '/components/com_customfilters/helpers/setting_seo.php' );
		$HelperSetting_seo = new HelperSetting_seo();

		$db = Factory::getDbo();
		$field_id = JFactory::getApplication()->input->get('val');
		$query = $db->getQuery(true);

		$select = ['customfield_value'];
		$query->select($select)->from('#__virtuemart_product_customfields');
		$where = [
			'virtuemart_custom_id = ' . $field_id ,
		];
		$query->where($where);
		$query->group('customfield_value' );

		$db->setQuery($query);
		$valList = $db->loadObjectList();



		foreach ( $valList as $valObject ){

			// указать что в строке есть лишние пробелы
			$valObject->name = preg_replace("/\s+$/", "_", $valObject->customfield_value );

//            $valObject->name = trim( $valObject->customfield_value );
			$valObject->id = $valObject->customfield_value ;
		}
		$Res = new stdClass();

		$Res->textSelect = Text::_( 'SETTING_SEO_SELECTED_FILTERS_TABLE_OPT_SELECT' ) ;
		$Res->valList = $HelperSetting_seo->processEncodeOptions( $valList  ) ;


		echo new JResponseJson( $Res );
		die();



	}

}