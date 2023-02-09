<?php
/**
 * @since       1.9.5
 * @author      Sakis Terz
 * @package     customfilters
 * @copyright   Copyright (C) 2012-2021 breakdesigns.net . All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_customfilters' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'tools.php';
require_once JPATH_SITE . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'mod_cf_filtering' . DIRECTORY_SEPARATOR . 'CfFilter.php';


use Joomla\CMS\Filter\InputFilter;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Factory;

/**
 * Class CfInput
 *
 * Handles all the inputs coming from the module
 * @since    1.9.5
 */
class CfInput
{
    // all the inputs are stored here
    protected static $cfInputs = null;

    protected static $cfInputsPerFilter = array();

	/**
	 * Когда зависимость работает сверху вниз, создайте массив с выбранными параметрами, которые нужны каждому фильтру.
	 * When the dependency works from top to bottom create an array with the selected options that each filter needs
	 *
	 * @param   stdClass  $module
	 * @param   bool      $cached
	 *
	 * @throws Exception
	 * @since 3.9
	 */
    public static function getInputsPerFilter($module = null, $cached=false)
    {
        if (empty($module)) {
            return [];
        }
        $module_id = $module->id;
        if (!isset(self::$cfInputsPerFilter[$module_id])) {
            $selected_fl = self::getInputs($cached);
            $moduleparams = \cftools::getModuleparams($module);
            $modif_selection = array();
            $filters_order = json_decode(str_replace("'", '"', $moduleparams->get('filterlist', '')));
            if (empty($filters_order) || !in_array('virtuemart_category_id', $filters_order) || !in_array('q',
                    $filters_order)) {
                $filters_order = array(
                    'q',
                    'virtuemart_category_id',
                    'virtuemart_manufacturer_id',
                    'product_price',
                    'custom_f'
                );
            }
            $filters_order = self::setCustomFiltersToOrder($filters_order, $selected_fl);

            foreach ($filters_order as $flt_key) {
                $flt_order = array_search($flt_key, $filters_order);
                $tmp_array = array();
                foreach ($selected_fl as $key => $flt) {
                    $sel_order = array_search($key, $filters_order);
                    if ($flt_order > $sel_order && !empty($flt)) {
                        $tmp_array[$key] = $flt;
                    }
                }
                // add the current filter's selections
                if (empty($tmp_array[$flt_key]) && isset($selected_fl[$flt_key])) {
                    $tmp_array[$flt_key] = $selected_fl[$flt_key];
                }
                if (!empty($tmp_array)) {
                    $modif_selection[$flt_key] = $tmp_array;
                }
            }
            $cfInputsPerFilter[$module_id] = $modif_selection;
        }
        return $cfInputsPerFilter[$module_id];
    }

	/**
	 * Получить Массив опций выбранных фильтров
	 * ---
	 * Get the inputs
	 *
	 * @param   bool  $cached
	 *
	 * @return array|null Массив отмеченных опций фильтров
	 * @throws Exception
	 * @since    1.0.0
	 */
    public static function getInputs( bool $cached = false ):?array
    {


        if (!isset(self::$cfInputs)) {
            $key = 'customfilters.input';
            $app = Factory::getApplication();

            /*
             * Session cache the last selected filters
             * So that can be requested by other modules in separate http requests.
             * The requests should be done only in the com_customfilters, otherwise it is inconsistent
             *
             * Сеансовый кеш последних выбранных фильтров
             * Так что это может быть запрошено другими модулями в отдельных HTTP-запросах.
             * Запросы должны быть сделаны только в com_customfilters, иначе это несовместимо
             *
             */
           if ($cached && $app->input->get('option', '', 'cmd') == 'com_customfilters') {
                $inputs = $app->getUserState($key);
                if ($inputs !== null) {
                    self::$cfInputs = $inputs;
                }
            }



           if(self::$cfInputs === null) {
                $cfinput = new \CfInput();
                self::$cfInputs = $cfinput->buildInputs();
            }



	        $seoTools = new seoTools();
	        $seoTools->setMetaData();

            $app->setUserState( $key, self::$cfInputs );
        }
        return self::$cfInputs;
    }

	/**
	 * Функция используется для получения и фильтрации всех входных данных, поступающих от модуля.
	 * --
	 * The function is used to get and filter all the inputs coming from the module
	 *
	 * @throws Exception
	 * @todo  Проверьте, опубликован ли фильтр для пользовательских фильтров / Check if the filter is published for custom filters
	 * @since 1.9.5
	 */
    private function buildInputs(): array
    {

        $app = Factory::getApplication();
	    /**
	     * @var Joomla\CMS\Input\Input $jinput
	     */
		$jinput = $app->input;
        $filter = InputFilter::getInstance();



		// Парсим путь URL -- находим активные фильтры
	    $this->parseUrlString();

		// Параметры компонента com_customfilters
        $componentParams = \cftools::getComponentparams();
	    /**
	     * Автоматическое выделение категорий и производителей при навигации по страницам
	     * ---
	     * @var bool $use_vm_vars
	     */
		$use_vm_vars = $componentParams->get('use_virtuemart_pages_vars', true);
	    /**
	     * @var bool $reset_filters_on_new_search - Очищать фильтры после нового поиска
	     */
	    $reset_filters_on_new_search = $componentParams->get('keyword_search_clear_filters_on_new_search', true);


        $selected_flt = array();
        $rangeVars = array();
        $reset_all_filters = false;
        $component = $jinput->get('option', '', 'cmd');

		/**
	     * Если option - com_virtuemart - в фильтре будут отмечена текущая категория товара(ов)
	     */
        if ($use_vm_vars && $component == 'com_virtuemart') {
            $use_vm_vars = true;
        } else {
            $use_vm_vars = false;
        }

        // --keywords search--
        if ($component == 'com_customfilters') {
            $source = $jinput->get('q', '', 'string');
            $keyword = preg_replace('/[<>]/i', '', $source);
            $keyword = trim($keyword);

            if (!empty($keyword) && strlen($keyword) > 1) {
                $selected_flt['q'] = (string)$keyword;
            }
        }


        if ($reset_filters_on_new_search) {
            $current_keyword = !empty($selected_flt['q']) ? $selected_flt['q'] : '';
            $cache = Factory::getCache('com_customfilters.input', 'output');
            $cache->setCaching(1);
            $previous_keyword = $cache->get('keyword');
            $cache->store($current_keyword, 'keyword');
            if (!empty($current_keyword) && $current_keyword != $previous_keyword) {
                $reset_all_filters = true;
            }
        }

        // --categories--
        if (($use_vm_vars == true || $component == 'com_customfilters') && $reset_all_filters == false) {

			$vm_cat_array = $jinput->get('virtuemart_category_id', array(), 'array');
            if ($vm_cat_array) {
                $vm_cat_array = ArrayHelper::toInteger($vm_cat_array);
            }
            $vm_cat_array = array_filter($vm_cat_array);

            if (count($vm_cat_array) > 0) {
                $selected_flt['virtuemart_category_id'] = $vm_cat_array;

                // set the var as single integer var. It is used this way by other extensions
                if (count($vm_cat_array) == 1) {
                    $jinput->set('virtuemart_category_id', (int)reset($vm_cat_array));
                }
            }
        }

        // --manufs--
        if (($use_vm_vars == true || $component == 'com_customfilters') && $reset_all_filters == false) {
            $vm_mnf_array = $jinput->get('virtuemart_manufacturer_id', array(), 'array');

            if ($vm_mnf_array) {
                $vm_mnf_array = ArrayHelper::toInteger($vm_mnf_array);
            }
            $vm_mnf_array = array_filter($vm_mnf_array);

            if (count($vm_mnf_array) > 0) {
                $selected_flt['virtuemart_manufacturer_id'] = $vm_mnf_array;

                // set the var as single integer var. It is used this way by other extensions
                if (count($vm_mnf_array) == 1) {
                    $jinput->set('virtuemart_manufacturer_id', (int)reset($vm_mnf_array));
                }
            }
        }

        // --prices--
        if ($component == 'com_customfilters' && $reset_all_filters == false) {
            $var_name = 'price';
            $prices = $jinput->get('price', array(), 'array');
            if (!empty($prices[0])) {
                $price_from = (float)$prices[0];
            }
            if (!empty($prices[1])) {
                $price_to = (float)$prices[1];
            }

            // price from should be lower or equal to price to
            if ((!empty($price_from) && empty($price_to)) || (!empty($price_from) && !empty($price_to) && $price_from <= $price_to)) {
                $rangeVars[] = $var_name;
                $selected_flt['price'][0] = $price_from;
            }
            // price to should be higher or equal to price from
            if ((!empty($price_to) && empty($price_from)) || (!empty($price_to) && !empty($price_from) && $price_to >= $price_from)) {
                if (!in_array($var_name, $rangeVars)) {
                    $rangeVars[] = $var_name;
                }
                $selected_flt['price'][1] = $price_to;
            }
        }

        // --stock--
        if ($component == 'com_customfilters' && $reset_all_filters == false) {
            $stock = $jinput->get('stock', 0, 'int');
            if(!empty($stock)) {
                $selected_flt['stock'][0] = 1;
            }
        }

        // --custom filters--
        if ($reset_all_filters == false) {

	        /**
	         * Все опубликованные фильтры
	         * ---
	         * @var array $published_cf
	         */
            $published_cf = \cftools::getCustomFilters('');

            $var_name = '';
            foreach ($published_cf as $cf) {

                if ( $use_vm_vars || $component == 'com_customfilters') {
                    $var_name = 'custom_f_' . $cf->custom_id;
					 
                    if (strpos($cf->disp_type, CfFilter::DISPLAY_INPUT_TEXT) === false
                        && strpos($cf->disp_type, CfFilter::DISPLAY_RANGE_SLIDER) === false
                        && strpos($cf->disp_type, CfFilter::DISPLAY_RANGE_DATES) === false) {

	                    $custom_array = $jinput->get($var_name, array(), 'array');

                        $c_array = [];
                        $data_type = 'string';
                        if ($cf->field_type == 'B') {
                            $data_type = 'INT';
                        }

                        // если плагин, мы можем получить тип входных данных и фильтровать на основе этого
	                    // if plugin, we can get the input data type and filter based on that
                        if (isset($cf->pluginparams->filter_data_type)) {
                            if ($cf->pluginparams->filter_data_type == 'int' || $cf->pluginparams->filter_data_type == 'boolean' || $cf->pluginparams->filter_data_type == 'bool') {
                                $data_type = 'int';
                            } else {
                                if ($cf->pluginparams->filter_data_type == 'float') {
                                    $data_type = 'float';
                                }
                            } // sanitize the float numbers
                        }
						 
                        // default data type is string
                        foreach ($custom_array as $cf_el) {
                            // Разрешены только шестнадцатеричные или целочисленные входы
	                        // Only hexademical or Int inputs allowed
                            $cf_el = (string)preg_replace('/[^A-F0-9]/i', '', $cf_el);

                            if (!empty($cf_el)) {
	                            /**
	                             * Преобразуем значение выбранной опции в строку
	                             * unecnode значение, только если строка
	                             * unecnode the value only if string
	                             */
                                $unencoded_value = \cftools::cfHex2bin($cf_el);

                                // clean again the unencoded value this time
                                $result = $filter->clean($unencoded_value, $data_type);
                                if (isset($result)) {
                                    $c_array[] = $result;
                                }
                            }
                        }
                        if (count($c_array) > 0) {
                            $selected_flt[$var_name] = $c_array;
                        }
                    }
					// ranges
                    else {
                        if ($cf->disp_type == CfFilter::DISPLAY_INPUT_TEXT || $cf->disp_type == CfFilter::DISPLAY_RANGE_SLIDER || $cf->disp_type == CfFilter::DISPLAY_INPUT_TEXT . ',' . CfFilter::DISPLAY_RANGE_SLIDER) {
                            $input_filter = 'FLOAT';
                        } else {
                            $input_filter = 'STRING';
                        } // date range and default

                        $custom_from = 0;
                        $custom_to = 0;
                        $custom_range = $jinput->get($var_name, array(), 'array');

                        // sanitize them
                        if (!empty($custom_range[0])) {
                            $custom_from = $filter->clean($custom_range[0], $input_filter);
                        }
                        if (!empty($custom_range[1])) {
                            $custom_to = $filter->clean($custom_range[1], $input_filter);
                        }

                        if (!empty($custom_from) && $custom_from > 0) {
                            $rangeVars[] = $var_name;
                            $selected_flt[$var_name][0] = $custom_from;
                        }
                        if (!empty($custom_to) && $custom_to > 0) {
                            if (!in_array($var_name, $rangeVars)) {
                                $rangeVars[] = $var_name;
                            }
                            $selected_flt[$var_name][1] = $custom_to;
                        }
                    }
                }
            }
        }


        \cftools::setRangeVars($rangeVars);

        return $selected_flt;
    }

	/**
	 * Парсим путь URL -- находим активные фильтры и добавляем их в $app->input -- как если не использовалось SEO
	 * ---
	 *
	 * @throws Exception
	 * @since version
	 * @auhtor Gartes
	 */
	public function parseUrlString()
	{
		$app  = \Joomla\CMS\Factory::getApplication();
		$juri = JUri::getInstance();
		$path = $juri->getPath();

		/**
		 * @var array $category_ids - массив категорий
		 */
		$category_ids = $app->input->get('virtuemart_category_id' , [] , 'ARRAY');

		/**
		 * @var array $published_cf - Все опубликованные фильтры
		 */
		$published_cf = \cftools::getCustomFilters('');

		// Удалить параметры пагинации
		$path = preg_replace('/\/start=\d+/', '', $path);

		/**
		 * Парсинг параметров сортировки
		 */
		// Параметры сортировки
		preg_match( '/\/orderby=([\w_]+)/' , $path , $matchesOrderBy );
		if ( isset($matchesOrderBy[1]) ) $app->input->set('orderby' , $matchesOrderBy[1] ); #END IF
		// Удалить параметр сортировки
		$path = preg_replace('/\/orderby=[\w_]+/', '', $path);

		preg_match( '/\/order=([\w_]+)/' , $path , $matchesOrder  );
		if ( isset($matchesOrder[1]) ) $app->input->set('order' , $matchesOrder[1] ); #END IF
		$path = preg_replace('/\/order=[\w_]+/', '', $path);


		/**
		 * @var array $findResultArr - массив выбранных
		 */
		$findResultArr = [];
		/**
		 * @var array $filtersArr - массив фильтров у которых есть выбранные опции
		 */
		$filtersArr    = [];

		// Перебираем опубликованные фильтры - Алиасы находим фильтры названий фильтра
		foreach ($published_cf as $item)
		{
			// Поиск вхождения первого фильтра
			$needle =   $item->sef_url . '-'   ;
			$needleRegExp = '/' . preg_quote($needle ) . '/u';


			preg_match( $needleRegExp , $path , $matches , PREG_OFFSET_CAPTURE ) ;

			if ( isset( $matches[0] ) )
			{ 
				$pos    = strripos($path, $item->sef_url );
				$findResultArr[$pos] = $item->sef_url ;
				$filtersArr[]        = $item;
			}#END IF

		}#END FOREACH


		// Если данных о включенных фильтрах нет - Ищем данный в фильтрах городах
		if ( empty($findResultArr) )
		{
			$findResultArr = seoTools_uri::findCityFilters( $category_ids , $findResultArr );
		}#END IF



		seoTools_uri::checkRedirectToCategory( $category_ids , $findResultArr  );

		// Если не нашли название фильтров в URL
		if (empty($findResultArr)) return; #END IF



		// Сортируем массив Alias названий фильтров по ключу в порядке убывания
		// - для того что бы разбирать URL с конца строки
		// Ключ в массиве - это номер символа после которого начинается Alias фильтра
		krsort($findResultArr);

		$length     = 0;
		$i          = 0;

		$dataFiltersArr = [];

		
		// Перебираем массив с Названиями (Alias) фильтров [ 77 => 'cvet' , 25 => 'vid_poverhnosti' ]
		foreach ($findResultArr as $start => $item)
		{
			$dataFilters        = new stdClass();
			$dataFilters->name  = str_replace(['/', '-and-'], '', $item);
			$dataFilters->value = [];
			// Если это первый фильтр с конца 
			if (!$i) $length = null; #END IF

			$i++;

			// Получаем строку от символа в позиции $start  до символа $length
			$subStr = mb_substr( $path, $start , $length );
			// Находим двойные или более опции фильтра
			$arrValFilter = explode('-and-', $subStr);

			if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
			{
				echo'<pre>';print_r( $path );echo'</pre>'.__FILE__.' '.__LINE__;
				echo'<pre>';print_r( $start );echo'</pre>'.__FILE__.' '.__LINE__;
				echo'<pre>';print_r( $length );echo'</pre>'.__FILE__.' '.__LINE__;
				echo'<pre>';print_r( $item );echo'</pre>'.__FILE__.' '.__LINE__;
			    echo'<pre>';print_r( $subStr );echo'</pre>'.__FILE__.' '.__LINE__;
			    echo'<pre>';print_r( $arrValFilter );echo'</pre>'.__FILE__.' '.__LINE__;


			}
			
			

			// Удаляем пустые ключи в массиве -- Если выбранная только одна опция фильтра
			$arrValFilter = array_diff($arrValFilter, array(''));

			foreach ( $arrValFilter as $itemValF )
			{
				// Удалить слэши
				$itemValF = str_replace('/', '', $itemValF);

				// Удаляем сам Alias фильтра 
				$itemValF = str_replace($dataFilters->name, '', $itemValF);
				//После удаления Alias фильтра остается "-" в начале строки - и ее тоже удаляем	
                $itemValF = preg_replace('/^-/' , '' , $itemValF ) ;
				
                $dataFilters->value[] = $itemValF;


			}#END FOREACH
			if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
			{
				echo'<pre>';print_r( $dataFilters );echo'</pre>'.__FILE__.' '.__LINE__;
			}
			
			$path         = str_replace($subStr, '', $path);
			$length = $start;
			$dataFiltersArr[] = $dataFilters;
		}#END FOREACH




		$selectFilterIds = [];

		// Добавить выбранные опции к объекту фильтра
		foreach ($filtersArr as &$filter)
		{
			$filter->optionSelected = [];
			$filter->dataOptions = [];
			foreach ($dataFiltersArr as $item)
			{
				if ($item->name == $filter->sef_url)
				{
					$filter->optionSelected = $item->value;
					$selectFilterIds[]      = $filter->custom_id;

				}#END IF
			}#END FOREACH
		}#END FOREACH




		/**
		 * @var array $customSelectValueArr - Массив всех значений для фильтров
		 */
		$customSelectValueArr = \cftools::getCustomSelectValue($selectFilterIds);

		// Определение Value для выбранных опций
		foreach ($filtersArr as &$item)
		{
			$key         = 'custom_f_' . $item->custom_id;
			$optArr      = [];
			$arrSetInput = [];

			if ( isset( $item->optionSelected ) && is_array($item->optionSelected) || is_object($item->optionSelected))
			{
				foreach ($item->optionSelected as $option)
				{
					if (array_key_exists( $option , $customSelectValueArr ))
					{
						$item->dataOptions[] =  $customSelectValueArr[$option];
						$customfield_value = $customSelectValueArr[$option]->customfield_value;

						$customfield_value = preg_replace('/[^\w\s\d\(\)\[\]\.,-®]/iu' , '' , $customfield_value ) ;
//						echo'<pre>';print_r( $customfield_value );echo'</pre>'.__FILE__.' '.__LINE__;

						// Преобразование двоичных данных в шестнадцатеричное представление
						$optArr[]          = bin2hex($customfield_value);

					}#END IF
				}#END FOREACH
			}
			$app->input->set($key, $optArr);

		}#END FOREACH

		/*if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
		{
			echo'<pre>';print_r( $path );echo'</pre>'.__FILE__.' '.__LINE__;
			echo'<pre>';print_r( $category_ids );echo'</pre>'.__FILE__.' '.__LINE__;
			echo'<pre>';print_r( $findResultArr );echo'</pre>'.__FILE__.' '.__LINE__;
			echo'<pre>';print_r( $dataFiltersArr );echo'</pre>'.__FILE__.' '.__LINE__;
			echo'<pre>';print_r( $filtersArr );echo'</pre>'.__FILE__.' '.__LINE__;
			die(__FILE__ .' '. __LINE__ );

		}*/

		$app->set('seoToolsActiveFilter' , $filtersArr );

		/**
		 * Создаем данные активных фильтров
		 */
		$dataTable = [] ;
		foreach ( $filtersArr as $items)
		{
			if (is_array($items->dataOptions) || is_object($items->dataOptions))
			{
				foreach ( $items->dataOptions as $optionSelected)
				{
//				$key = 'custom_f_' . $optionSelected->virtuemart_custom_id  ;
					$key = 'custom_f_' . $items->custom_id  ;

					$dataTable[$key][] = bin2hex( $optionSelected->customfield_value );
				}#END FOREACH
			}

		}#END FOREACH
		$app->set('seoToolsActiveFilter.table' , $dataTable );

	}

	/**
	 * Переупорядочивает массив упорядочивания фильтров, устанавливая также существующие настраиваемые поля в порядке
	 * Reorders the filters ordering array, setting also the existing custom fields in the order
	 *
	 * @param   Array  $filters_order  Array The ordering of the filters
	 * @param   Array  $selected_fl    Array The selected filters
	 *
	 * @return Array
	 * @throws Exception
	 * @since  1.6.0
	 * @author Sakis Terz
	 */
    public static function setCustomFiltersToOrder( $filters_order, $selected_fl)
    {
        $custom_f_pos = array_search('custom_f', $filters_order);
        if ($custom_f_pos === false) {
            return $filters_order;
        }
        $first_portion = array_slice($filters_order, 0, $custom_f_pos);
        $second_portion = array_slice($filters_order, $custom_f_pos + 1);
        $custom_filters = \cftools::getCustomFilters();

        foreach ($custom_filters as $key => $flt) {
            $first_portion[] = 'custom_f_' . $flt->custom_id;
        }
        if (is_array($first_portion) && is_array($second_portion)) {
            $filters_order = array_merge($first_portion, $second_portion);
        }
        return $filters_order;
    }
}
