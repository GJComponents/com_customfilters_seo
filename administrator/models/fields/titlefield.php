<?php

use Joomla\CMS\Language\Text;

defined('JPATH_BASE') or die;

if (!class_exists('VmConfig')) require(JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/config.php');

/**
 * Создание Select title custom field
 * @since    1.0.0
 */
class JFormFieldTitlefield extends JFormField
{
    var $type = 'titlefield';

    /**
     * Method to get the field input markup.
     *
     * @return    string    The field input markup.
     * @since    1.6
     */

    protected function getInput()
    {
        //load model
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_customfilters/models/setting_seo.php');

        /**
         * @var CustomfiltersModelCustomfilters
         */
        $model = JModelLegacy::getInstance('Customfilters', 'CustomfiltersModel');
        // Получить список всех фильтров
        $dataArr = $model->getCustomFilters();


//        $data = array(['value' => null, 'text' => Text::_( 'SETTING_SEO_SELECTED_FILTERS_TABLE_OPT_SELECT' ) ,]);
        $data = array(['value' => null, 'text' => null ,]);



        foreach ($dataArr as $fieldObject)
        {
            $params = null;
            if (!$fieldObject->published) continue;

            if (!empty($fieldObject->params))
            {
                $params = json_decode($fieldObject->params);
            }




            $opt = [
                'value' => $fieldObject->custom_id ,
                'text' => $fieldObject->custom_title . ' (' . $fieldObject->custom_id . ')',
                'attr' => [
                    'data-conditional_operator' => $params->conditional_operator ,
                    'data-ordering' => $fieldObject->ordering ,
                ],
            ];
            $data[] = $opt;
        }

        $options = array(
            // 'id' => 'applesfield', // HTML id for select field
            'list.attr' => array( // additional HTML attributes for select field
                'class' => 'title_field_list',
                'data-placeholder'  =>  Text::_( 'SETTING_SEO_SELECTED_FILTERS_TABLE_OPT_SELECT' ) ,
            ),
            'list.translate' => false, // true to translate
            'option.key' => 'value', // key name for value in data array
            'option.text' => 'text', // key name for text in data array
            'option.attr' => 'attr', // key name for attr in data array
            'list.select' => $this->value, // value of the SELECTED field
        );

        return JHtmlSelect::genericlist($data, $this->name, $options);
    }
}