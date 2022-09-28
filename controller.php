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
        $input = Factory::getApplication()->input;
        $view = $input->get('view', 'customfilters', '');

        echo'<pre>';print_r( $view );echo'</pre>'.__FILE__.' '.__LINE__;



        if ($view == 'customfilters' || $view == '') {
            $this->_createFilters();
            UpdateManager::getInstance()->refreshUpdateSite();
        }



        parent::display();
        return $this;
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
         * @var CustomfiltersModelCustomfilters Object
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

}
