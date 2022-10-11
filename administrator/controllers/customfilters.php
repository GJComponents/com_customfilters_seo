<?php
/**
 * @package    customfilters
 * @author        Sakis Terz
 * @link        http://breakdesigns.net
 * @copyright    Copyright (c) 2012-2021 breakdesigns.net. All rights reserved.
 * @license        See LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

/**
 * main controller class
 *
 * @since 1.0
 * @author Sakis Terz
 * @package customfilters
 */
class CustomfiltersControllerCustomfilters extends JControllerAdmin
{

    /**
     * save filters task
     *
     *
     * @return void
     * @throws Exception
     * @author Sakis Terz
     * @since 1.0
     */
    public function savefilters()
    {
        $app = Factory::getApplication();
        $user = Factory::getUser();
        if ($user->authorise('core.edit', 'com_customfilters')) {

            // типы дисплеев с расширенными настройками
            // display types which have advanced settings
            $adv_setting_types = [
                5,
                6,
                8
            ];
            /** @var CustomfiltersModelCustomfilter $model */
            $model = $this->getModel();


            $type_ids = $app->input->get('type_id', [], 'array');
            $alias = $app->input->get('alias', [], 'array');
            $smart_search = $app->input->get('smart_search', [], 'array');
            $expanded = $app->input->get('expanded', [], 'array');
            $scrollbar_after = $app->input->get('scrollbar_after', [], 'array');
            $slider_min_value = $app->input->get('slider_min_value', [], 'array');
            $slider_max_value = $app->input->get('slider_max_value', [], 'array');
            $filter_category_ids = $app->input->get('filter_categories', [], 'array');
            // Display if selected setting display_if_filter_exist
            $display_if_filter_exist = $app->input->get('display_if_filter_exist', [], 'array');
			$conditional_operator = $app->input->get('conditional_operator', [], 'array');

			// Лимит количества выбранных опций
			$limit_options_select_for_no_index = $app->input->get('limit_options_select_for_no_index', [], 'array');
			$use_only_one_opt_for_no_index = $app->input->get('use_only_one_opt', [], 'array');







            $params_array = [];

			array_walk($type_ids, function (&$value, $key) {
                $value = (string)$value;
            });

            $smart_search = ArrayHelper::toInteger($smart_search);
            $expanded = ArrayHelper::toInteger($expanded);
            $slider_min_value = ArrayHelper::toInteger($slider_min_value);
            $slider_max_value = ArrayHelper::toInteger($slider_max_value);

            array_walk($alias, function (&$value, $key) {
                $value = (string)$value;
            });
            array_walk($scrollbar_after, function (&$value, $key) {
                $value = (string)$value;
            });




            // store the params in an assoc array and use the item id as key
            foreach ($smart_search as $key => $val) {
                $params_array[$key] = array(
                    'smart_search' => $val,
                    'expanded' => $expanded[$key],
                    'scrollbar_after' => $scrollbar_after[$key],
                    'display_if_filter_exist' => !empty($display_if_filter_exist[$key]) ? $display_if_filter_exist[$key] : '',
                    // Sanitize the operator. Can be 'AND' or 'OR'
                    'conditional_operator' => !empty($conditional_operator[$key]) && in_array($conditional_operator[$key],
                        ['AND', 'OR']) ? strtoupper($conditional_operator[$key]) : 'AND'
                );
                if (in_array($type_ids[$key], $adv_setting_types)) {
                    if ($type_ids[$key] == '6' || $type_ids[$key] == '5,6') {
						// slider
                        $params_array[$key]['slider_min_value'] = $slider_min_value[$key];
                        $params_array[$key]['slider_max_value'] = $slider_max_value[$key];
                    }
                    $params_array[$key]['filter_category_ids'] = !empty($filter_category_ids[$key]) ? $filter_category_ids[$key] : '';
                }
	            $params_array[$key]['limit_options_select_for_no_index'] = $limit_options_select_for_no_index[$key] ;
	            $params_array[$key]['use_only_one_opt'] = $use_only_one_opt_for_no_index[$key] ;


            }


            $params_formated = $this->formatParams($params_array);
            // sanitize the input to be int

//	        echo'<pre>';print_r( $params_array );echo'</pre>'.__FILE__.' '.__LINE__;
//	        echo'<pre>';print_r( $params_formated );echo'</pre>'.__FILE__.' '.__LINE__;
//	        echo'<pre>';print_r( $limit_options_select_for_no_index );echo'</pre>'.__FILE__.' '.__LINE__;
//	        die(__FILE__ .' '. __LINE__ );

            if ($type_ids || $alias || $params_formated) {
                if (!$model->savefilters($type_ids, $alias, $params_formated)) {
                    throw new \Exception($model->getError(), 500);
                } else {
                    $this->setMessage(JText::_('COM_CUSTOMFILTERS_FILTERS_SAVED_SUCCESS'));
                }
            }
        }
        $this->setRedirect('index.php?option=com_customfilters&view=customfilters');
    }

    /**
     * Proxy for getModel.
     *
     * @param string $name
     *            of the model.
     * @param string $prefix
     *            for the PHP class name.
     *
     * @return bool|JModel|JModelLegacy
     * @since 1.0
     */
    public function getModel(
        $name = 'Customfilter',
        $prefix = 'CustomfiltersModel',
        $config = array('ignore_request' => true)
    ) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Create an array with the params as json string
     *
     * @return array
     * @since 1.5.3
     * @author Sakis Terz
     */
    public function formatParams($params_array)
    {
        $params_array_formated = [];

        foreach ($params_array as $key => $array) {
            $reg = new Registry();
            $reg->loadArray($array);
            $params_array_formated[$key] = $reg->toString();
        }
        return $params_array_formated;
    }
}
