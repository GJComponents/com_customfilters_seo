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

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;

/**
 * Vm_seo_product_filter_grt
 *
 * @package  vm_seo_product_filter_grt
 * @since    1.0.0
 */
class CustomfiltersModelForms_add extends AdminModel
{

    /**
     * @var   string  The prefix to use with controller messages.
     *
     * @since 1.0.0
     */
    protected $text_prefix = 'COM_VM_SEO_PRODUCT_FILTER_GRT';

    /**
     * Method to get the record form.
     *
     * @param   array    $data      An optional array of data for the form to interrogate.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  Form|boolean    A Form object on success, false on failure
     * @since   1.0.0
     */
    public function getForm($data = [], $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm(
            'com_customfilters.forms_add',
            'forms_add', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    mixed    The data for the form.
     *
     * @since   1.0.0
     *
     * @throws  Exception
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState(
            'com_vm_seo_product_filter_grt.edit.vm_seo_product_filter_grt.data',
            []
        );

        if (empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Returns a Table object, always creating it.
     *
     * @param type    The table type to instantiate.
     * @param string    A prefix for the table class name. Optional.
     * @param array    Configuration array for model. Optional.
     * @return        Table    A database object.
     * @access        public
     * @since        1.0
     */
    public function getTable($type = 'Setting_seo', $prefix = 'CustomfiltersTable', $config = array())
    {
        return Table::getInstance($type, $prefix, $config);
    }

    public function publishTogle( $publish = 0 ){
        $app = \JFactory::getApplication();
        $user = \JFactory::getUser();
        $input = $app->input;
        $recordIdArr = $input->get('cid');

        /**
         * @var CustomfiltersTableSetting_seo Object
         */
        $table = $this->getTable();

        if ( empty( $recordIdArr ))
        {
            die('Не переданный Id записи ' . __FILE__ .' '. __LINE__ );
        }#END IF

        if (!$table->publish($recordIdArr, $publish , $user->get('id')))
        {

            // обрабатываем ошибки
            die(__FILE__ .' ^^^ // обрабатываем ошибки ^^^ '. __LINE__ );

        }

        // Clear the component's cache
        $this->cleanCache();
        return true ;
    }


}
