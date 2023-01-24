<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die('Restricted access');

class CustomfiltersViewForms_add extends HtmlView
{
	/**
	 * @var bool|\Joomla\CMS\Form\Form
	 * @since 3.9
	 */
	public $form;



	/**
     * @throws Exception
     * @since    1.0.0
     */
    function display($tpl = null)
    {
	    JLoader::registerNamespace( 'FiltersSeoNamespace' , JPATH_ADMINISTRATOR . '/components/com_customfilters/libraries' , $reset = false , $prepend = false , $type = 'psr4' );

	    /**
	     * @var CustomfiltersModelForms_add $model
	     */
	    $model = $this->getModel();
		$this->form = $model->getForm();
		$app = \Joomla\CMS\Factory::getApplication();
	    $layout = $app->input->get('layout' , 'add_city_seo' , 'STRING' );

		$task = $app->input->get('task' , false , 'STRING' );


	    switch ($task){



			case 'onAjaxSaveForm' : // Сохранение настроек фильтра по городам
				$model->onAjaxSaveForm();

			    break;
		    default :

	    }

		die(__FILE__ .' '. __LINE__ );

	    $this->ListCity = $model->getListCity();
	    $Data['form_html'] = $this->loadTemplate( $layout );

	    echo new JResponseJson( $Data );
	    die();
	    /**
	     * ======================================================================================================
	     */



    }
}