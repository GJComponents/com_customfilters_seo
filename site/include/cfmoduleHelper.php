<?php
/**
 *
 * Customfilters Module helper
 *
 * @package        customfilters
 * @author        Sakis Terz
 * @link        http://breakdesigns.net
 * @copyright    Copyright (c) 2008-2012 breakdesigns.net. All rights reserved.
 * @license        http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

//defined
defined('_JEXEC') or die;

//import the view class
jimport('joomla.application.module.helper');

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Factory;

/**
 * Extends the abstract JModuleHelper
 * @author    Sakis Terz
 * @since    1.6.0
 */
class cfModuleHelper extends ModuleHelper
{
    /**
     * Get module by id
     *
     * @param   integer $id The id of the module
     * @param   string $name
     *
     * @return  object  The Module object
     * @author    Sakis Terz
     * @since    1.6.0
     */
    public static function &getModule($id, $type = 'mod_cf_filtering', $name = null)
    {
        $result =& self::loadById($id);

        // If we didn't find it, and the name is mod_something, create a dummy object
        if (is_null($result)) {
            $result = parent::getModule($type);
            if (is_null($result)) {
                $result = new stdClass;
                $result->id = 0;
                $result->title = '';
                $result->module = $type;
                $result->position = '';
                $result->content = '';
                $result->showtitle = 0;
                $result->control = '';
                $result->params = '';
                $result->user = 0;
            }
        }
        return $result;
    }

    /**
     * Load published modules.
     *
     * @return  array
     *
     * @since   1.6.1
     */
    protected static function &loadById($module_id)
    {
        static $clean;

        if (isset($clean)) {
            return $clean;
        }
        $app = Factory::getApplication();
        $jinput = $app->input;
        $Itemid = $jinput->get('Itemid', '', 'int');
        $user = Factory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $lang = Factory::getLanguage()->getTag();
        $clientId = (int)$app->getClientId();

        $cache = Factory::getCache('com_modules', '');
        $cacheid = md5(serialize(array($Itemid, $groups, $clientId, $lang, $module_id)));

        if (!($clean = $cache->get($cacheid))) {
            $db = Factory::getDbo(true);

            $query = $db->getQuery(true);
            $query->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params, mm.menuid');
            $query->from('#__modules AS m');
            $query->join('LEFT', '#__modules_menu AS mm ON mm.moduleid = m.id');
            $query->where('m.published = 1');

            $query->join('LEFT', '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id');
            $query->where('e.enabled = 1');

            $query->where('m.access IN (' . $groups . ')');
            $query->where('m.client_id = ' . $clientId);
            $query->where('m.id =' . (int)$module_id);
            //for security the module can be only mod_cf_filtering or mod_cf_breadcrumbs type
            $query->where('(m.module ="mod_cf_filtering" OR m.module="mod_cf_breadcrumbs")');

            // Filter by language
            if ($app->isSite() && $app->getLanguageFilter()) {
                $query->where('m.language IN (' . $db->quote($lang) . ',' . $db->quote('*') . ')');
            }

            // Set the query
            $db->setQuery($query);
            $module = $db->loadObject();
            $clean = null;

            If (empty($module)) {
                return $clean;
            }

            // Determine if this is a 1.0 style custom module (no mod_ prefix)
            // This should be eliminated when the class is refactored.
            // $module->user is deprecated.
            $file = $module->module;
            $custom = substr($file, 0, 4) == 'mod_' ? 0 : 1;
            $module->user = $custom;
            // 1.0 style custom module name is given by the title field, otherwise strip off "mod_"
            $module->name = $custom ? $module->module : substr($file, 4);
            $module->style = null;
            $module->position = strtolower($module->position);
            $clean = $module;
            $cache->store($clean, $cacheid);
        }
        return $clean;
    }
}