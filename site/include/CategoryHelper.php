<?php
/***
 * @package customfilters
 * @author Sakis Terz
 * @copyright Copyright (C) 2012-2018 breakdesigns.net . All rights reserved.
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;

class CategoryHelper
{
    /**
     * @param array $ids
     * @return bool|string
     * @since 1.0.0
     */
    public static function getNames($ids)
    {
        $names = [];
        $ids = ArrayHelper::toInteger($ids);

        if(empty($ids)) {
            return $names;
        }
        $language = Factory::getLanguage();
        $langTag = $language->getTag();
        $langTag = strtolower(strtr($langTag, '-', '_'));
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('category_name'))->from($db->quoteName('#__virtuemart_categories_' . $langTag))->where('virtuemart_category_id IN (' . implode(',', $ids) . ')');
        $db->setQuery($query);
        $names = $db->loadColumn();
        return array_map('htmlspecialchars_decode', $names);
    }
}
