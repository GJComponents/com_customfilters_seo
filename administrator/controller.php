<?php
/**
 * @package 	customfilters
 * @author		Sakis Terz
 * @copyright	Copyright (c) 2012-2021 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Breakdesigns\Customfilters\Admin\Model\UpdateManager;
use Joomla\Utilities\ArrayHelper;


/**
 * main controller class
 * @package        customfilters
 * @since        1.0
 */
class CustomfiltersController extends JControllerLegacy
{

    /**
     * Method to display a view.
     *
     * @param bool $cachable
     * @param bool $urlparams
     * @return $this|JControllerLegacy
     * @throws Exception
     * @since 1.0
     */
    public function display($cachable = false, $urlparams = false)
    {
	    // TODO - для разработки формы городов
	    $doc = \Joomla\CMS\Factory::getDocument();
	    $doc->addStyleSheet('/administrator/components/com_customfilters/assets/css/formCitySeo.css');

        $input = Factory::getApplication()->input;
        $view = $input->get('view', 'customfilters', '');

	    if ( $doc->getType() != 'json')
	    {
//		    echo'<pre>';print_r( $view );echo'</pre>'.__FILE__.' '.__LINE__;
	    }#END IF


        if ($view == 'customfilters' || $view == '') {
            $this->_createFilters();
            UpdateManager::getInstance()->refreshUpdateSite();
        }

        parent::display();
        return $this;
    }

	public function map_links_lean(){
		JLoader::register('seoTools_filters' , JPATH_ROOT .'/components/com_customfilters/include/seoTools_filters.php');
		$seoTools_filters = seoTools_filters::instance();

		$app = \Joomla\CMS\Factory::getApplication();
		$db = JFactory::getDbo();
		$Query = $db->getQuery(true );
		$Query->select([
			$db->quoteName( 'id' ),
			$db->quoteName( 'vmcategory_id' ),
			$db->quoteName( 'url_params' ),
			$db->quoteName( 'url_params_hash' ),
			$db->quoteName( 'sef_url' ),
			$db->quoteName( 'no_index' ),
		])
			->from( $db->quoteName('#__cf_customfields_setting_seo'));

		$db->setQuery( $Query , $offset =  0, $limit = 30000 );


		$items = $db->loadObjectList('url_params_hash');
		$dellArr = [];
		echo'<pre>';print_r( count( $items ) );echo'</pre>'.__FILE__.' '.__LINE__;


		foreach ( $items as $item)
		{
			if ($item->no_index ) {
				$dellArr[] = $item->id ;
				continue ;
			}   #END IF

			$JUri = JUri::getInstance( $item->url_params );
			$queryUrl       = $JUri->getQuery(true);
			$queryUrl['virtuemart_category_id'][] = $item->vmcategory_id ;
			if ( seoTools::checkOffFilters( $queryUrl ) )
			{
				$dellArr[] = $item->id ;
				continue ; 
			}#END IF


		}#END FOREACH

		if (!count( $dellArr )) $app->redirect('index.php?option=com_customfilters'); #END IF

		$Query = $db->getQuery( true ) ;
		$conditions = [
			$db->quoteName('id') . 'IN ( "'.implode('","' , $dellArr  ).'")' ,
		];
		$Query->where($conditions);
		$Query->delete($db->quoteName('#__cf_customfields_setting_seo'));
		$db->setQuery($Query)->execute();


		echo'<pre>';print_r( count( $dellArr ) );echo'</pre>'.__FILE__.' '.__LINE__;
		echo'<pre>';print_r( (string)$Query );echo'</pre>'.__FILE__.' '.__LINE__;


		die(__FILE__ .' '. __LINE__ );
		$app->redirect('index.php?option=com_customfilters');


	}

    /**
     *  Function to load the existing custom fields to the filters table
     *
     * @throws Exception
     * @since 1.0
     */
    protected function _createFilters()
    {
        /**
         * @var CustomfiltersModelCustomfilters $model Object
         */
        $model = $this->getModel();

        try {
            $model->createFilters();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Function to get version info
     *
     * @since 1.0
     */
    public function getVersionInfo()
    {
        $html_result = UpdateManager::getInstance()->getVersionInfo();
        if ($html_result) echo json_encode($html_result);
        else echo '';
        jexit();
    }

    /**
     * Получить список всех значений для поля
     * @return void
     * @since    1.0.0
     */
    public function onAjaxGetAllValueField (){

        if (!JSession::checkToken('get'))
        {
            echo new JResponseJson(null, JText::_('JINVALID_TOKEN'), true);
        }
        else
        {
            parent::display();
        }
    }

    /**
     * @return void
     * @since    1.0.0
     */
    public function onAjaxChangeCategory(){

        if (!JSession::checkToken('get'))
        {
            echo new JResponseJson(null, JText::_('JINVALID_TOKEN'), true);
            die();
        }

        $app = JFactory::getApplication();
        $catId = $app->input->get('catId'  , null );


        if (!class_exists( 'VmConfig' ))
            require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
        try{
            /**
             * @var VirtueMartModelCategory
             */
            $categoryModel = VmModel::getModel('Category');
        }catch (Exception $e) {
            echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
        }
        $category = $categoryModel->getCategory( $catId );

        $dataResponse = [
            'category' => $category
        ];
        echo new JResponseJson( $dataResponse, null, false );
        die();

    }

	/**
	 * Изменить Index - ON OFF
	 *
	 * @throws Exception
	 * @since version
	 */
    public function updateOnSeoElement()
    {
        if (!JSession::checkToken('get'))
        {
            echo new JResponseJson(null, JText::_('JINVALID_TOKEN'), true);
            die();
        }
        $app = \Joomla\CMS\Factory::getApplication();
        $object = new stdClass();
        // Должно быть допустимое значение первичного ключа.
        $object->id = $app->input->get('idField' , 0 , 'INT' );
		$object->on_seo =  $app->input->get('status' , 0  , 'INT' ) ;

//      Update their details in the users table using id as the primary key.
        $result = \Joomla\CMS\Factory::getDbo()->updateObject('#__cf_customfields', $object, 'id');
//
        echo new JResponseJson( $result , JText::_('COM_COMPONENT_MY_TASK_ERROR'), false );
        die( );

    }

	/**
	 * Изменение использования фильтра для языка (для всех -* | ru-RU | ua-UA) - Ajax Input
	 * @return void
	 * @throws Exception
	 * @since 3.9
	 */
	public function updateKnownLanguagesElement(){
		if (!JSession::checkToken('get'))
		{
			echo new JResponseJson(null, JText::_('JINVALID_TOKEN'), true);
			die();
		}
		$app = \Joomla\CMS\Factory::getApplication();
		$object = new stdClass();
		// Должно быть допустимое значение первичного ключа.
		$object->id = $app->input->get('idField' , 0 , 'INT' );
		$object->known_languages =  $app->input->get('status' , 0  , 'STRING' ) ;

		// Update their details in the users table using id as the primary key.
		$result = \Joomla\CMS\Factory::getDbo()->updateObject('#__cf_customfields', $object, 'id');
		echo new JResponseJson( $result , JText::_('COM_COMPONENT_MY_TASK_ERROR'), false );
        die( );
	}

	/**
	 * Загрузить форму настроек для гениратора города
	 * @return void
	 * @throws Exception
	 * @since 3.9
	 */
	public function onAjaxGetFormAddFilterCitySeo(){
		$input = Factory::getApplication()->input;
		$view = $input->get('view', 'customfilters', '');
		if (!JSession::checkToken('get'))
		{
			echo new JResponseJson(null, JText::_('JINVALID_TOKEN'), true);
		}
		else
		{
			parent::display();
		}
	}

	/**
	 * Загрузить слайдер дочерних городов
	 * @return void
	 * @since 3.9
	 */
	public function onAjaxGetChildrenArea(){
		if (!JSession::checkToken('get'))
		{
			echo new JResponseJson(null, JText::_('JINVALID_TOKEN'), true);
		}
		else
		{


			parent::display();
		}
	}

	public function onAjaxSaveForm(){
		if (!JSession::checkToken('get'))
		{
			echo new JResponseJson(null, JText::_('JINVALID_TOKEN'), true);
		}
		else
		{
			parent::display();
		}
	}

}
