<?php

use Joomla\CMS\Form\FormField;
use \Joomla\CMS\HTML\HTMLHelper;
defined('_JEXEC') or die();

/**
 *
 * @package    VirtueMart
 * @subpackage Plugins  - Elements
 * @author Valérie Isaksen
 * @link https://virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id$
 */



jimport('joomla.form.formfield');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

/*
 * This element is used by the menu manager
 * Should be that way
 */
class JFormFieldVmseocategories extends FormField {

	protected static $categoryTree;
	var $type = 'vmseocategories';

	protected function getInput() {


		if (!class_exists( 'VmConfig' )) require(JPATH_ROOT .'/administrator/components/com_virtuemart/helpers/config.php');
		VmConfig::loadConfig();
		vmLanguage::loadJLang('com_virtuemart');

		if(!is_array($this->value))$this->value = array($this->value);
		$categorylist = self::categoryListTree($this->value);


		$name = $this->name;
		if($this->multiple){
			$this->multiple = ' multiple="multiple" ';
		}
		if ( $this->required )
		{
			$this->required = ' required="required" ' ;

		}#END IF




		$id = VmHtml::ensureUniqueId('jform_' . $this->fieldname  );
		$html = '<select id="' . $id . '" '
			.'class="inputbox '.$this->class.'" '
			.'name="' . $name . '" '
			. $this->multiple
			. $this->required
			. ' >';

		if(!$this->multiple)$html .= '<option value="0">' . vmText::_('COM_VIRTUEMART_CATEGORY_FORM_TOP_LEVEL') . '</option>';
		$html .= $categorylist;
		$html .= "</select>";
		return $html;
	}

	static public function categoryListTree ($selectedCategories = array(), $cid = 0, $level = 0, $disabledFields = array()) {

		if(!is_array($selectedCategories)){
			$selectedCategories = array($selectedCategories);
		}
		$hash = crc32(implode('.',$selectedCategories).':'.$cid.':'.$level.implode('.',$disabledFields));
		if (empty(self::$categoryTree[$hash])) {

			$cache = VmConfig::getCache ('com_virtuemart_cats');
			$cache->setCaching (1);

			$vendorId = vmAccess::isSuperVendor();

	        $disabledFields = self::getDisabledCategories();
			$selectedCategories = self::getDisabledCategories(true);

			self::$categoryTree[$hash] = $cache->call (array('ShopFunctions', 'categoryListTreeLoop'), $selectedCategories, $cid, $level, $disabledFields,VmConfig::isSite(),$vendorId,VmConfig::$vmlang);

			//self::$categoryTree[$hash] = ShopFunctions::categoryListTreeLoop($selectedCategories, $cid, $level, $disabledFields,$app->isSite(),$vendorId,VmConfig::$vmlang);
		}

		return self::$categoryTree[$hash];
	}
	public static function getSelectedCategories(){

	}

	/**
	 * Получить категории выбранные в других фильтрах
	 *
	 * @param   bool  $selected  IF TRUE - select category CityFilter
	 *
	 * @return array
	 * @throws Exception
	 * @since 3.9
	 */
	protected static function getDisabledCategories(bool $selected = false ): array
	{
		$path = JPATH_ADMINISTRATOR . '/components/com_customfilters/models/setting_city.php';
		JModelLegacy::addIncludePath( $path , 'CustomfiltersModel' );
		/**
		 * @var CustomfiltersModelSetting_city
		 */
		$Model = JModelLegacy::getInstance( 'Setting_city' , 'CustomfiltersModel' ,  $config = array() );
		$table = $Model->getTable('Setting_city_category_vm');
		return $table->getDisabledCategories( $selected );
	}

}


