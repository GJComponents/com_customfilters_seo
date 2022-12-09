<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

class CustomfiltersViewSetting_seo extends JViewLegacy
{


    /**
     * @throws Exception
     * @since    1.0.0
     */
    function display($tpl = null)
    {

        JLoader::register( 'HelperSetting_seo' , JPATH_ADMINISTRATOR . '/components/com_customfilters/helpers/setting_seo.php' );
        $HelperSetting_seo = new HelperSetting_seo();

        $db = Factory::getDbo();
        $field_id = JFactory::getApplication()->input->get('val');
        $query = $db->getQuery(true);

        $select = ['customfield_value'];
        $query->select($select)->from('#__virtuemart_product_customfields');
        $where = [
            'virtuemart_custom_id = ' . $field_id ,
        ];
        $query->where($where);
        $query->group('customfield_value' );

        $db->setQuery($query);
        $valList = $db->loadObjectList();



        foreach ( $valList as $valObject ){

            // указать что в строке есть лишние пробелы
            $valObject->name = preg_replace("/\s+$/", "_", $valObject->customfield_value );

//            $valObject->name = trim( $valObject->customfield_value );
            $valObject->id = $valObject->customfield_value ;
        }
        $Res = new stdClass();

        $Res->textSelect = Text::_( 'SETTING_SEO_SELECTED_FILTERS_TABLE_OPT_SELECT' ) ;
        $Res->valList = $HelperSetting_seo->processEncodeOptions( $valList  ) ;


        echo new JResponseJson( $Res );
        die();


    }
}