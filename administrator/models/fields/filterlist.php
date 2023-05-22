<?php
/**
 * @package customfilters
 * @version $Id: fields/filterlist.php  2012-6-14 sakisTerzis $
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright    Copyright (C) 2012-2018 breakDesigns.net. All rights reserved
 * @license    GNU/GPL v2
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('jquery.framework');

/**
 *
 * Class that generates a filter list
 * @author Sakis Terzis
 */
class JFormFieldFilterlist extends FormField
{
    /**
     * Method to get the field input markup.
     *
     *
     * @return    string    The field input markup.
     * @since    1.6
     */
    protected function getInput()
    {
        $script = "window.addEventListener('DOMContentLoaded',function(event){
        let listContainerSelector = '#cf_filterlist';       
		var mysortable= jQuery(listContainerSelector).sortable();

		mysortable.on('sortupdate',function(el){
		    let list = document.querySelectorAll('.sortableFilter');
		    let sortList = [];
		    list.forEach((element) => {
		        sortList.push(element.id);
		    });
		    document.getElementById('cf_filterlist_hidden').value=JSON.stringify(sortList);
		});
	});";


        $oldLabelStrings = array(
            'category_flt' => 'virtuemart_category_id',
            'manuf_flt' => 'virtuemart_manufacturer_id',
            'price_flt' => 'price',
            'custom_flt' => 'custom_f'
        );

        $labelStrings = array(
            'q' => Text::_('COM_MODULES_MOD_CF_FILTERING_KEYWORD_FIELDSET_LABEL'),
            'virtuemart_category_id' => Text::_('COM_MODULES_MOD_CF_FILTERING_CATEGORIES_FIELDSET_LABEL'),
            'virtuemart_manufacturer_id' => Text::_('COM_MODULES_MOD_CF_FILTERING_MANUFACTURERS_FIELDSET_LABEL'),
            'price' => Text::_('COM_MODULES_MOD_CF_FILTERING_PRICE_FIELDSET_LABEL'),
            'stock' => Text::_('COM_MODULES_MOD_CF_FILTERING_STOCK_FIELDSET_LABEL'),
            'custom_f' => Text::_('COM_MODULES_MOD_CF_FILTERING_CUSTOM_FILTERS_FIELDSET_LABEL')
        );

        $html = '';
        if (!empty($this->value)) {
            $value_array_temp = json_decode(str_replace("'", '"', $this->value));
            $value_array_temp = (array)$value_array_temp;
            $value_array_temp = array_filter($value_array_temp);
            if (count($value_array_temp) == count($labelStrings)) {
                if (!in_array('price', $value_array_temp)) {//change the price format
                    $index = array_search('product_price', $value_array_temp);
                    if ($index !== false) {
                        unset($value_array_temp[$index]);
                        $value_array_temp[$index] = 'price';
                    }
                }

                if (!in_array('virtuemart_category_id', $value_array_temp)) {//old format
                    foreach ($value_array_temp as &$val) {
                        $val = $oldLabelStrings[$val];
                    }
                }
                ksort($value_array_temp);
            } else {
                $value_array_temp = array_keys($labelStrings);
            }
            $value_array = $value_array_temp;
            $value_json = str_replace('"', "'", json_encode($value_array));

        }
        if (empty($value_array)) {
            $value_array = array_keys($labelStrings);
            $value_json = str_replace('"', "'", json_encode($value_array));
        }

        if (is_array($value_array) && !empty($value_array)) {
            $attr = '';
            // Initialize some field attributes.
            $attr .= $this->element['class'] ? ' class="' . (string)$this->element['class'] . '"' : '';
            $attr .= $this->element['size'] ? ' size="' . (int)$this->element['size'] . '"' : '';

            $document = Factory::getDocument();
            $document->addScriptDeclaration($script);
            $document->addStyleSheet(Uri::root() . 'modules/mod_cf_filtering/assets/style_backend.css');

            // We need jquery ui for the sortable functionality
            if (version_compare(JVERSION, '4.0.0', 'lt')) {
                HTMLHelper::_('jquery.ui', array('core', 'sortable'));
                HTMLHelper::_('script', 'jui/sortablelist.js', array('version' => 'auto', 'relative' => true));
            }
            else {
                // We load the jQuery Ui externally in J4
                $document->addScript('https://cdn.jsdelivr.net/npm/jquery-ui-bundle@1.12.1-migrate/jquery-ui.min.js');
            }

            $html = '<ul id="cf_filterlist" style="font-size:12px;" class="cf_sorting_list">';
            foreach ($value_array as $key) {
                $html .= '<li id="' . $key . '" class="sortableFilter" style="padding-left:8px;">' . $labelStrings[$key] . '</li>';
            }
            $html .= '</ul>
			<input type="hidden" id="cf_filterlist_hidden" name="' . $this->name . '" value="' . $value_json . '"/>';
            $language = Factory::getLanguage();
            $language->load('mod_cf_filtering', JPATH_SITE);
        }
        return $html;
    }
}
