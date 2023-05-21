<?php
/**
 * @package     Joomla\Component\Customfilters\Site\Helpers
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Joomla\Component\Customfilters\Site\Helpers;

use Exception;
use JLanguageMultilang;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\Utilities\ArrayHelper;

class CfHelperFilters
{
	/**
	 * @var mixed
	 * @since version
	 */
	protected static mixed $_customFilters;

	/**
	 * Получения фильтров Компонента из таблицы #__cf_customfields
	 * ---
	 * Function to get the existing custom filters
	 *
	 * @param   Joomla\Registry\Registry|string  $module_params  Параметры модуля
	 * @param   bool                             $published      Опубликованные
	 *
	 * @return array|mixed
	 * @throws Exception
	 * @throws Exception
	 * @since 1.0.0
	 */
	public static function getCustomFilters( $module_params = '' , bool $published = true )
	{

		if (!empty($module_params)) {
			$store = md5(json_encode($module_params->get('selected_customfilters',
					array())) . '::' . $module_params->get('cf_ordering',
					'cf.ordering') . '::' . $module_params->get('cf_ordering_dir', 'ASC').'::'. $published);
		}
		// default
		else {
			$store = md5('Array::cf.ordering::ASC::'.$published);
		}

		// Кешируется для каждого модуля
		if (!isset( self::$_customFilters[$store] )) {

			$db = Factory::getContainer()->get(DatabaseInterface::class);

			$query = $db->getQuery(true);

			if (!empty($module_params)) {
				$selected_customfilters = $module_params->get('selected_customfilters', '');
				if (!empty($selected_customfilters)) {
					$selected_customfilters = ArrayHelper::toInteger($selected_customfilters);
					$selected_customfilters = array_filter($selected_customfilters);
				}
			}

			// ordering
			$order = !empty($module_params) ? $module_params->get('cf_ordering', 'cf.ordering') : 'cf.ordering';
			$order_dir = !empty($module_params) ? $module_params->get('cf_ordering_dir', 'ASC') : 'ASC';

			// table cf_customfields
			$query->select('cf.id AS id');
			$query->select('cf.type_id  AS disp_type');
			$query->select('cf.params AS params');
			$query->select('cf.data_type AS data_type');
			$query->select('cf.ordering AS ordering');

			// Поле ON_SEO
			$query->select('cf.on_seo AS on_seo');
			// Алиас - названия фильтра
			$query->select('cf.alias AS alias');

			$query->from('#__cf_customfields AS cf');

			// table vituemart_customfields
			$query->select('vmc.virtuemart_custom_id AS custom_id');
			$query->select('vmc.custom_title AS custom_title');
			$query->select('vmc.custom_element AS custom_element');
			$query->select('vmc.field_type AS field_type');
			$query->select('vmc.is_list, vmc.custom_value');
			$query->select('vmc.custom_tip AS tooltip, vmc.custom_desc AS description');

			// joins
			$query->join('INNER', '#__virtuemart_customs AS vmc ON cf.vm_custom_id = vmc.virtuemart_custom_id');
			$query->where('cf.published = 1');
			if (!empty($selected_customfilters)) {
				$query->where('vmc.virtuemart_custom_id IN(' . implode(',', $selected_customfilters) . ')');
			}

			/**
			 * Если Multilang isEnabled отбираем поля по языкам
			 */
			if ( Multilanguage::isEnabled() )
			{
				$lang = Factory::getLanguage();
				$where = [
					$db->quoteName('known_languages') . ' = ' . $db->quote( $lang->getTag() ) ,
					$db->quoteName('known_languages') . ' = ' . $db->quote( '*' ) ,

				];
				$query->where('('. implode(' OR ' , $where ) .')');
			}

			$query->order($order . ' ' . $order_dir);
			$db->setQuery($query);
			try
			{
				// Code that may throw an Exception or Error.
				$customFilters = $db->loadObjectList();
				// throw new \Exception('Code Exception '.__FILE__.':'.__LINE__) ;
			}
			catch ( Exception $e)
			{
				$Code = $e->getCode();
				switch ( $Code ){
					// Если нет поля "ИСКЛЮЧЕНИЕ ИЗ SEO" - добавляем
					case '1054':
						$db = Factory::getContainer()->get(DatabaseInterface::class);
						$query='ALTER TABLE `#__cf_customfields` ADD `on_seo` int(11) NOT NULL DEFAULT "1" COMMENT "исключение из seo"';
						$db->setQuery($query);
						$result = $db->execute();
						echo'<pre>';print_r( $result );echo'</pre>'.__FILE__.' '.__LINE__;
						die(__FILE__ .' '. __LINE__ );

						break;
				}

				// Executed only in PHP 5, will not be reached in PHP 7
				echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
				echo 'Выброшено исключение: Code ',  $e->getCode(), "\n";
				echo'<pre>';print_r( $e );echo'</pre>'.__FILE__.' '.__LINE__;
				die(__FILE__ .' '. __LINE__ );
			}

			foreach ( $customFilters as &$filter)
			{
				$filter->sef_url = CfHelper::getStringSefUrl( $filter->alias );

			}#END FOREACH



			$customFilters = self::setPluginparamsAsAttributes( $customFilters ) ;

			self::$_customFilters = [];
			self::$_customFilters[$store] = [];

			foreach ($customFilters as $cf) {
				self::$_customFilters[$store][$cf->custom_id] = $cf;
			}
		}

		return self::$_customFilters[$store];
	}
	/**
	 * Загрузить все значения для фильтров
	 * ---
	 * @param $filtersIds
	 *
	 * @return array
	 * @throws Exception
	 * @since 3.9
	 */
	public static function getCustomSelectValue( $filtersIds = [] , $virtuemart_category_idArr = []  ): array
	{

		$app = Factory::getApplication();
		if ( !count( $virtuemart_category_idArr ) )
		{
			$virtuemart_category_idArr = $app->input->get('virtuemart_category_id' , false , 'ARRAY');
		}#END IF


		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$Query = $db->getQuery( true ) ;
		$select = [
			$db->quoteName('virtuemart_customfield_id'),
			$db->quoteName('virtuemart_custom_id'),
			$db->quoteName('customfield_value'),
		];
		$Query->select( $select );
		$Query->from( $db->quoteName('#__virtuemart_product_customfields' , 'cfp') );
		$Query->innerJoin(
			$db->quoteName('#__virtuemart_products' , 'p')
			. 'ON'
			. $db->quoteName('cfp.virtuemart_product_id') . '=' . $db->quoteName( 'p.virtuemart_product_id' )
		);
		$Query->innerJoin(
			$db->quoteName('#__virtuemart_product_categories' , 'pc')
			. 'ON'
			. $db->quoteName('p.virtuemart_product_id') . '=' . $db->quoteName( 'pc.virtuemart_product_id' )
		);
		$where = [
			$db->quoteName('cfp.virtuemart_custom_id') .'IN ( "'.implode('","' , $filtersIds  ).'")' ,
			$db->quoteName( 'pc.virtuemart_category_id' ) .'IN ( "'.implode('","' , $virtuemart_category_idArr  ).'")',
			// TODO-Info : Не работатает в некоторых категориях - из за значения 0
			//			$db->quoteName( 'cfp.published' ) .'= 1 ',
			$db->quoteName( 'p.published' ) .'= 1 ',
		];
		$Query->where( $where );
		// TODO - Не работало на tekAktiv - с условием
		$Query->group( $db->quoteName('cfp.customfield_value') );
		$db->setQuery( $Query );
		$res = $db->loadObjectList();


		$itemArr = [];
		foreach ( $res as &$item )
		{
			$item->customfield_value_alias = CfHelper::getStringSefUrl( $item->customfield_value );


			// Alias Поля - z_lock
			$itemArr[$item->customfield_value_alias] = $item ;

			// Прямое значение поля etc/ Z-Lock
			$itemArr[$item->customfield_value] = $item ;

			// Значение поле в нижнем регистре etc/ z-lock
			$customfield_value_to_lower_case =  mb_strtolower( $item->customfield_value );
			$itemArr[$customfield_value_to_lower_case] = $item ;

		}#END FOREACH

		return $itemArr ;
	}
	/**
	 * Если настраиваемое поле является плагином, получите параметры плагина и назначьте
	 * их пользовательскому фильтру в качестве атрибута объекта.
	 *
	 * If the customfield is plugin then get the plugin params and assign them to the custom filter as object attr.
	 *
	 * @param   array  $cost_filters
	 *
	 * @return array $cost_filters
	 * @throws Exception
	 * @since 1.9.0
	 */
	public static function setPluginparamsAsAttributes(array $cost_filters): array
	{
		if (!is_array($cost_filters)) {
			return [];
		}
		PluginHelper::importPlugin('vmcustom');
		foreach ($cost_filters as &$customfilter) {
			if ($customfilter->field_type == 'E') {
				$name = $customfilter->custom_element;
				$virtuemart_custom_id = $customfilter->custom_id;
				$product_customvalues_table = '';
				$customvalues_table = '';
				$filter_by_field = '';
				$customvalue_value_field = '';
				$filter_data_type = 'string';
				$sort_by = '';
				$custom_parent_id = 0;
				$value_parent_id_field = 'parent_id';
				$customvalue_value_description_field = '';

				$ret = Factory::getApplication()->triggerEvent('onFilteringCustomfilters', array(
					$name,
					$virtuemart_custom_id,
					&$product_customvalues_table,
					&$customvalues_table,
					&$filter_by_field,
					&$customvalue_value_field,
					&$filter_data_type,
					&$sort_by,
					&$custom_parent_id,
					&$value_parent_id_field,
					&$customvalue_value_description_field
				));

				// all the necessary variables should be there
				if ($ret &&
					!empty($product_customvalues_table) &&
					!empty($customvalues_table) &&
					!empty($filter_by_field) &&
					!empty($customvalue_value_field) &&
					!empty($filter_data_type) &&
					!empty($sort_by)) {
					$pluginparams = new stdClass();
					$pluginparams->product_customvalues_table = $product_customvalues_table;
					$pluginparams->customvalues_table = $customvalues_table;
					$pluginparams->filter_by_field = $filter_by_field;
					$pluginparams->filter_data_type = strtolower($filter_data_type);
					$pluginparams->customvalue_value_field = $customvalue_value_field;
					$pluginparams->sort_by = $sort_by;
					$pluginparams->custom_parent_id = $custom_parent_id;
					$pluginparams->value_parent_id_field = $value_parent_id_field;
					$pluginparams->customvalue_value_description_field = $customvalue_value_description_field;
					$customfilter->pluginparams = $pluginparams;
				}
			}
		}
		return $cost_filters;
	}

}