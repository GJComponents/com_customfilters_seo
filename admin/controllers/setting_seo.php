<?php
/**
 * @package    vm_seo_product_filter_grt
 *
 * @author     Максим <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;

/**
 * Vm_seo_product_filter_grt Controller.
 *
 * @package  vm_seo_product_filter_grt
 * @since    1.0.0
 */
class CustomfiltersControllerSetting_seo extends FormController
{
    /**
     * Proxy for getModel.
     *
     * @param string $name of the model.
     * @param string $prefix for the PHP class name.
     *
     * @return CustomfiltersModelSetting_seo
     * @since 1.0
     */
    public function getModel(
        $name = 'Setting_seo',
        $prefix = 'CustomfiltersModel',
        $config = array('ignore_request' => true)
    )
    {
        /**
         * @var CustomfiltersModelSetting_seo Object
         */
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Сняить с публикации URL для фильтра
     * @param $cidname
     * @param $table
     * @param $redirect
     * @return void
     * @since 3.9
     */
    public function unpublish( $cidname=0,$table=0,$redirect = 0 ){

        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel();
        $model->publishTogle(0 );

        $input = \JFactory::getApplication()->input;
        $view = $input->get('view' , 'setting_seo_list' ) ;

        $this->setMessage('Снято с публикации');
        $this->setRedirect(
            \JRoute::_( 'index.php?option=' . $this->option . '&view=' . $view , false )
        );
    }
    /**
     * Опубликовать URL для фильтра
     * URL для фильтра
     * @param $cidname
     * @param $table
     * @param $redirect
     * @return void
     * @since 3.9
     */
    public function publish( $cidname=0,$table=0,$redirect = 0 ){
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel();
        $model->publishTogle( 1 );

        $input = \JFactory::getApplication()->input;
        $view = $input->get('view' , 'setting_seo_list' ) ;

        $this->setMessage('Опубликовано');
        $this->setRedirect(
            \JRoute::_( 'index.php?option=' . $this->option . '&view=' . $view , false )
        );

    }

    public function save( $key = null, $urlVar = null )
    {

        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

//        echo'<pre>';print_r( $key );echo'</pre>'.__FILE__.' '.__LINE__ .'<br>';
//        echo'<pre>';print_r( $this->input );echo'</pre>'.__FILE__.' '.__LINE__ .'<br>';
//        die( __FILE__ .' ' . __LINE__);

        parent::save( $key , $urlVar  );
        return true;
    }

    public function cancel($key = null)
    {

        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $this->setRedirect(JRoute::_('index.php?option=com_customfilters&view=setting_seo_list', false));
        return true;
    }

}
