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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;


class CustomfiltersControllerSetting_filter extends \Joomla\CMS\MVC\Controller\AdminController
{

	/**
	 * Сохранение формы редактирования - кнопка "Сохранить"
	 * @throws Exception
	 * @since 3.9
	 */
	public function apply()
	{
		$this->save();
		$id = \Joomla\CMS\Factory::getApplication()->input->get( 'id' , false , 'INT' );
		// Перегружаем страницу
		$this->setRedirect( 'index.php?option=com_customfilters&view=setting_filter&id='.$id );
	}
	public function onAjaxSave(){
		die(__FILE__ .' '. __LINE__ );

	}

	/**
	 * Сохранение формы редактирования - кнопка "Сохранить и закрыть"
	 * @throws Exception
	 * @since 3.9
	 */
	public function save()
	{

		die(__FILE__ .' '. __LINE__ );


		// Check for request forgeries.
		$this->checkToken();
		$app      = \Joomla\CMS\Factory::getApplication();
		$model    = $this->getModel();
		$formData = $app->input->get( 'jform' , false , 'RAW' );
		$task     = $app->input->get( 'task' , false , 'STRING' );

		if ( !$model->save( $formData ) )
		{
			throw new \Exception( $model->getError() , 500 );
		}
		else
		{
			$this->setMessage( Text::_( 'COM_CUSTOMFILTERS_SETTING_FILTER_SAVED_SUCCESS' ) );
		}
		if ( $task == 'save' )
		{
			// Выход в список
			$this->setRedirect( 'index.php?option=com_customfilters&view=setting_filter_list' );
		}#END IF

	}

	/**
	 * Выход из текущего вида
	 * @return bool
	 * @since 3.9
	 */
	public function cancel():bool
	{
		Session::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
		$this->setRedirect( Route::_( 'index.php?option=com_customfilters&view=${viewName}_list' , false ) );

		return true;
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    of the model.
	 * @param   string  $prefix  for the PHP class name.
	 * @param   bool[]  $config
	 *
	 * @return CustomfiltersModelSetting_filter
	 * @since 1.0
	 */
	public function getModel( $name = 'Setting_filter' , $prefix = 'CustomfiltersModel' , $config = array( 'ignore_request' => true ) ):CustomfiltersModelSetting_filter
	{
		/**
		 * @var CustomfiltersModelSetting_filter Object
		 */
		return parent::getModel( $name , $prefix , $config );
	}
}