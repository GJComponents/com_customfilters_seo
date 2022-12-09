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
     * @var   string Префикс для использования с сообщениями контроллера  The prefix to use with controller messages.
     *
     * @since 1.0.0
     */
    protected $text_prefix = 'COM_VM_SEO_PRODUCT_FILTER_GRT';

	/**
	 * @var array - одномерный массив с результатом
	 * @since 3.9
	 */
	protected $ArrData = [] ;
	/**
	 * @var array Список alias - для поддержки уникальности
	 * @since 3.9
	 */
	protected $ArrAlias = [] ;

	/**
	 * Способ получения формы записи.
	 * Method to get the record form.
	 *
	 * @param   array    $data      Необязательный массив данных для опроса формы.
	 *                              An optional array of data for the form to interrogate.
	 * @param   boolean  $loadData  Истина, если форма должна загружать свои собственные данные (случай по умолчанию),
	 *                              ложь, если нет.
	 *                              True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form|boolean    Объект Form в случае успеха, false в случае неудачи  / A Form object on success, false on failure
	 * @since   1.0.0
	 */
    public function getForm($data = [], $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm(
            'com_customfilters.setting_city',
            'setting_city', array('control' => 'jform', 'load_data' => $loadData));
	    return !empty($form) ? $form : false;
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
     * @param string    The table type to instantiate.
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
