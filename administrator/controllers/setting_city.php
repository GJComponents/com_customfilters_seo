<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;


class CustomfiltersControllerSetting_city extends \Joomla\CMS\MVC\Controller\AdminController
{
	/**
	 * Сохранить фильтр и добавить в карту сайта
	 * @return void
	 * @throws Exception
	 * @since 3.9
	 */
	public function save_add_to_map(){
		$formData = Factory::getApplication()->input->get('jform', false, 'RAW');
		JLoader::registerNamespace( 'OsmapBackgroundHelper' , JPATH_ADMINISTRATOR . '/modules/mod_osmap_background_toolbar/helpers' , $reset = false , $prepend = false , $type = 'psr4' );
		$ComFilterCity = new \OsmapBackgroundHelper\ComFilterCity();
		$dataResult = $ComFilterCity->createMapCityFilter( $formData['id'] );

		// Перегружаем страницу
		$this->setRedirect('index.php?option=com_customfilters&view=setting_city&id='.$formData['id']);


	}
	/**
	 * Сохранение формы редактирования - кнопка "Сохранить"
	 * @throws Exception
	 * @since 3.9
	 */
	public function apply(){
		$this->save();
		$id =   Factory::getApplication()->input->get('id' , false , 'INT');
		// Перегружаем страницу
		$this->setRedirect('index.php?option=com_customfilters&view=setting_city&id='.$id);
	}
	/**
	 * Сохранение формы редактирования - кнопка "Сохранить и закрыть"
	 * @throws Exception
	 * @since 3.9
	 */
	public function save(){
		// Check for request forgeries.
		$this->checkToken();
		$app      = Factory::getApplication();
		$model = $this->getModel();
		$formData = $app->input->get('jform', false, 'RAW');
		$task = $app->input->get('task', false, 'STRING');

		if (!$model->save( $formData )  ) {
			throw new \Exception($model->getError(), 500);
		} else {
			$this->setMessage(Text::_('COM_CUSTOMFILTERS_FILTERS_SETTING_CITY_SAVED_SUCCESS'));
		}
		if ($task == 'save')
		{
			// Выход в список
			$this->setRedirect('index.php?option=com_customfilters&view=setting_city_list');
		}#END IF

	}
	/**
	 * Выход из текущего вида
	 * @return bool
	 * @since 3.9
	 */
	public function cancel(): bool
	{
		Session::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$this->setRedirect(Route::_('index.php?option=com_customfilters&view=setting_city_list' , false));
		return true;
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    of the model.
	 * @param   string  $prefix  for the PHP class name.
	 * @param   bool[]  $config
	 *
	 * @return CustomfiltersModelSetting_city
	 * @since 1.0
	 */
	public function getModel($name = 'Setting_city', $prefix = 'CustomfiltersModel', $config = array('ignore_request' => true)): CustomfiltersModelSetting_city
	{
		/**
		 * @var CustomfiltersModelSetting_city Object
		 */
		return parent::getModel($name, $prefix, $config);
	}
}