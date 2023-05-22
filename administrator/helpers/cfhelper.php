<?php
/**
 *
 * @package    customfilters
 * @author        Sakis Terz
 * @link        http://breakdesigns.net
 * @copyright    Copyright (c) 2012-2021 breakdesigns.net. All rights reserved.
 * @license        http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *                customfilters is free software. This version may have been modified
 *                pursuant to the GNU General Public License, and as distributed
 *                it includes or is derivative of works licensed under the GNU
 *                General Public License or other free or open source software
 *                licenses.
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_customfilters' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'tools.php';

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;

/**
 * Class cfHelper
 *
 * A helper class offering some useful functions
 * @since 1.9.0
 */
class cfHelper
{

    /**
     *
     * @var int
     */
    public static $counter = 0;

    /**
     *
     * @var array
     */
    public static $categories = [];

    /**
     *
     * @var array
     */
    public static $categoryTree = [];

    /**
     *
     * @param string $key
     * @param string $default
     * @return string
     */
    public static function getValue($key, $default)
    {

        static $config;
        if (empty($config)) {
            $config = self::loadConfig();
        }

        if (array_key_exists($key, $config)) {
            return $config[$key];
        } else {
            return $default;
        }
    }

    /**
     * @return array|mixed
     * @since 1.0.0
     */
    private static function loadConfig()
    {
        $db = Factory::getDbo();

        $sql = $db->getQuery(true)
            ->select($db->quoteName('params'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . " = " . $db->quote('com_customfilters'));
        $db->setQuery($sql);
        $config_ini = $db->loadResult();

        // OK, Joomla! 1.6 stores values JSON-encoded so, what do I do? Right!
        $config_ini = json_decode($config_ini, true);
        if (is_null($config_ini) || empty($config_ini)) {
            $config_ini = array();
        }

        return $config_ini;
    }

    /**
     * Returns the category tree and checks if there is a cached tree too
     *
     * @param array $selectedCategories
     * @param int $cid
     * @param int $level
     * @param array $disabledFields
     */
    static public function categoryListTree(
        $selectedCategories = array(),
        $cid = 0,
        $level = 0,
        $disabledFields = array()
    ) {
        $category_key = md5(serialize($selectedCategories) . $cid . $level . serialize($disabledFields));
	    $app  =  Factory::getContainer()->get(SiteApplication::class);
        $vendorId = 1;
        if (empty(self::$categoryTree[$category_key])) {
            $cache = Factory::getCache('_virtuemart');
            $cache->setCaching(1);
            self::$categoryTree[$category_key] = $cache->call(array('ShopFunctions', 'categoryListTreeLoop'),
                $selectedCategories, $cid, $level, $disabledFields, $clean_cache = true, $app->isClient('site'), $vendorId);
        }
        return self::$categoryTree[$category_key];
    }

    /**
     * Creates structured option fields for all categories
     *
     * @param array $selectedCategories All category IDs that will be pre-selected
     * @param int $cid Internally used for recursion
     * @param int $level Internally used for recursion
     * @param boolean
     * @return string    $category_tree HTML: Category tree list
     * @see    ShopFunctions
     */
    static public function categoryListTreeLoop(
        $selectedCategories = array(),
        $cid = 0,
        $level = 0,
        $disabledFields = array(),
        $clean_cache = false
    ) {
        $category_key = md5(serialize($selectedCategories) . $cid . $level . serialize($disabledFields));

        self::$counter++;

        static $categoryTree = '';

        //clean the previous cached $categoryTree
        if ($clean_cache) {
            $categoryTree = '';
        }

        $categoryModel = VmModel::getModel('category');
        $level++;

        $categoryModel->_noLimit = true;
        if (self::$categories) {
	        $app  =  Factory::getContainer()->get( SiteApplication::class);
            self::$categories = $categoryModel->getCategories($app->isClient('site'), $cid);
        }
        $records = self::$categories;
        $selected = "";
        if (!empty($records)) {
            foreach ($records as $key => $category) {

                $childId = $category->category_child_id;

                if ($childId != $cid) {
                    $selected = '';
                    if (in_array($childId, $selectedCategories)) {
                        $selected = 'selected';
                    }
                    $disabled = '';
                    if (in_array($childId, $disabledFields)) {
                        $disabled = 'disabled';
                    }

                    if ($disabled != '' && stristr($_SERVER['HTTP_USER_AGENT'], 'msie')) {
                        //IE7 suffers from a bug, which makes disabled option fields selectable
                    } else {
                        $categoryTree .= '<option ' . $selected . ' ' . $disabled . ' value="' . $childId . '">';
                        $categoryTree .= str_repeat(' - ', ($level - 1));

                        $categoryTree .= $category->category_name . '</option>';
                    }
                }

                if ($categoryModel->hasChildren($childId)) {
                    self::categoryListTreeLoop($selectedCategories, $childId, $level, $disabledFields);
                }
            }
        }
        return $categoryTree;
    }

	/**
	 * Получите пользовательские фильтры
	 * Get the custom filters select options
	 *
	 * @param   int  $currentFilterId
	 * @param $selected
	 *
	 * @return array
	 * @throws Exception
	 * @since 1.0.0
	 */
    public static function getCustomFiltersOptions( int $currentFilterId, $selected = null ):array
    {
	    $options       = [];
	    $customFilters = \cftools::getCustomFilters( '' , false );
	    foreach ( $customFilters as $customFilter )
	    {
		    // The current filter should not be there
		    if ( $currentFilterId == $customFilter->custom_id )
		    {
			    continue;
		    }
		    $selectedStr = '';
		    if ( is_array( $selected ) && in_array( $customFilter->custom_id , $selected ) )
		    {
			    $selectedStr = 'selected';
		    }
		    $options[] = '<option value="'.$customFilter->custom_id.'" '.$selectedStr.'>'.$customFilter->custom_title.'</option>';
	    }

	    return $options;
    }
}