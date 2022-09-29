<?php
/**
 * @package     Breakdesigns.JFilters
 *
 * @Copyright   Copyright © 2010-2018 Breakdesigns.net. All rights reserved.
 * @license     GNU Geneal Public License 2 or later, see COPYING.txt for license details.
 */

defined('JPATH_BASE') or die;

/**
 * Class JFormFieldVoid
 *
 * The only usage of that field is to load the language files
 */
Class JFormFieldVoid extends \Joomla\CMS\Form\FormField
{
    /**
     * Method to get the field input markup.
     *
     *
     * @return	string	The field input markup.
     * @since	1.6
     */
    protected function getInput(){
        $language=JFactory::getLanguage();
        $language->load('mod_cf_filtering', $basePath = JPATH_SITE);
        return '';
    }

    /**
     * @return string
     */
    function getLabel()
    {
        return '';
    }
}