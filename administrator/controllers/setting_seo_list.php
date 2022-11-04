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

// use Joomla\CMS\MVC\Controller\AdminController;




/**
 * Vm_seo_product_filter_grts Controller.
 *
 * @package  vm_seo_product_filter_grt
 * @since    1.0.0
 */
class CustomfiltersControllerSetting_seo_list extends JControllerAdmin
{


	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $text_prefix = 'com_vm_seo_product_filter_grt_vm_seo_product_filter_grt';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  \JModelLegacy  The model.
	 *
	 * @since   1.0.0
	 */
	public function getModel(
		$name = 'Vm_seo_product_filter_grt',
		$prefix = 'Vm_seo_product_filter_grtsModel',
		$config = ['ignore_request' => true]
	) {
		return parent::getModel($name, $prefix, $config);
	}

    public function cancel($key = null) {

        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $this->setRedirect(JRoute::_('index.php?option=com_customfilters' , false));
        return true;
    }

}
