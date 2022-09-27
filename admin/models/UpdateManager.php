<?php
/**
 *
 * @package    customfilters
 * @author        Sakis Terz
 * @copyright    Copyright (c) 2012-2021 breakdesigns.net. All rights reserved.
 * @license        http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

namespace Breakdesigns\Customfilters\Admin\Model;

use JLoader;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Installer\Installer;

class UpdateManager
{
    /**
     * @var UpdateManager
     */
    protected static $instance;
    /**
     * @var array
     */
    protected $config = [
        'update_type' => 'package',
        'update_component' => 'pkg_customfilters',
        'update_sitename' => 'Custom Filters PRO',
        'update_site' => 'http://cdn.breakdesigns.net/release/customfilters/update.xml',
        'update_extraquery' => ''
    ];

    /**
     * @return UpdateManager
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UpdateManager();
        }
        return self::$instance;
    }

    /**
     * Refreshes the update sites, removing obsolete update sites in the process
     */
    public function refreshUpdateSite()
    {
        // Remove any update sites for the old com_customfilters package
        $this->removeObsoleteComponentUpdateSites();
        // Refresh our update sites
        $this->updateUpdateSite();

        return true;
    }

    /**
     * Removes the obsolete update sites for the component, since now we're dealing with a package.
     *
     * @return  void
     */
    private function removeObsoleteComponentUpdateSites()
    {
        // Initialize
        $deleteIDs = array();

        // Get component ID
        $componentID = $this->getExtensionId('com_customfilters', 'component');

        // Get package ID
        $packageID = $this->getExtensionId('pkg_customfilters', 'package');

        // Update sites for old extension ID (all)
        if ($componentID) {
            // Old component packages
            $deleteIDs = $this->getUpdateSitesFor($componentID, null);
        }

        // Update sites for any but current extension ID, location matching any of the obsolete update sites
        if ($packageID) {
            // Update sites for all of the current extension ID update sites
            $moreIDs = $this->getUpdateSitesFor($packageID, null);

            if (is_array($moreIDs) && count($moreIDs)) {
                $deleteIDs = array_merge($deleteIDs, $moreIDs);
            }
            $deleteIDs = array_unique($deleteIDs);

            // keep the last update site
            if (count($deleteIDs)) {
                $lastID = array_pop($moreIDs);
                $pos = array_search($lastID, $deleteIDs);
                unset($deleteIDs[$pos]);
            }
        }
        $deleteIDs = array_unique($deleteIDs);
        if (empty($deleteIDs) || !count($deleteIDs)) {
            return;
        }

        $db = Factory::getDbo();
        $deleteIDs = array_map(array($db, 'quote'), $deleteIDs);
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__update_sites'))
            ->where($db->quoteName('update_site_id') . ' IN(' . implode(',', $deleteIDs) . ')');

        try {
            $db->setQuery($query)->execute();
        } catch (\Exception $e) {
            // Do nothing.
        }

        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__update_sites_extensions'))
            ->where($db->quoteName('update_site_id') . ' IN(' . implode(',', $deleteIDs) . ')');

        try {
            $db->setQuery($query)->execute();
        } catch (\Exception $e) {
            // Do nothing.
        }
    }

    /**
     * Get the extension id from the updates table
     *
     * @param string $extension
     * @param string $type
     * @return bool
     */
    public function getExtensionId($extension = 'pkg_customfilters', $type = 'package')
    {
        // Get the extension ID to ourselves
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('extension_id'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' = ' . $db->quote($type))
            ->where($db->quoteName('element') . ' = ' . $db->quote($extension));
        $db->setQuery($query);
        $extension_id = $db->loadResult();

        if (empty($extension_id)) {
            return false;
        }
        return $extension_id;
    }

    /**
     * Returns the update site IDs matching the criteria below. All criteria are optional but at least one must be
     * defined for the method call to make any sense.
     *
     * @param int|null $includeEID The update site must belong to this extension ID
     * @param int|null $excludeEID The update site must NOT belong to this extension ID
     *
     * @return  array  The IDs of the update sites
     */
    private function getUpdateSitesFor($includeEID = null, $excludeEID = null)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('s.update_site_id'))
            ->from($db->quoteName('#__update_sites', 's'));

        if (!empty($includeEID) || !empty($excludeEID)) {
            $query->innerJoin($db->quoteName('#__update_sites_extensions', 'e') . 'ON(' . $db->quoteName('e.update_site_id') .
                ' = ' . $db->quoteName('s.update_site_id') . ')'
            );
        }

        if (!empty($includeEID)) {
            $query->where($db->quoteName('e.extension_id') . ' = ' . $db->quote($includeEID));
        } elseif (!empty($excludeEID)) {
            $query->where($db->quoteName('e.extension_id') . ' != ' . $db->quote($excludeEID));
        }
        try {
            $ret = $db->setQuery($query)->loadColumn();
        } catch (\Exception $e) {
            $ret = null;
        }
        return empty($ret) ? array() : $ret;
    }

    /**
     * Refreshes the Joomla! update sites for this extension as needed
     *
     * @return  void
     */
    protected function updateUpdateSite()
    {
        JLoader::import('joomla.application.component.helper');
        $dlid = \cfHelper::getValue('update_dlid', '');
        $extra_query = null;


        // If I have a valid Download ID I will need to use a non-blank extra_query in Joomla! 3.2+
        if (preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid)) {
            $extra_query = 'dlid=' . $dlid;
        }

        // Create the update site definition we want to store to the database
        $update_site = array(
            'name' => $this->config['update_sitename'],
            'type' => 'extension',
            'location' => $this->config['update_site'],
            'enabled' => 1,
            'last_check_timestamp' => 0,
            'extra_query' => $extra_query
        );

        $extension_id = $this->getExtensionId();
        if (empty($extension_id)) {
            return;
        }

        $db = Factory::getDbo();
        $updateSiteIDs = $this->getUpdateSitesFor($extension_id);

        if (!count($updateSiteIDs)) {
            // No update sites defined. Create a new one.
            $newSite = (object)$update_site;
            $db->insertObject('#__update_sites', $newSite);
            $id = $db->insertid();

            $updateSiteExtension = (object)array(
                'update_site_id' => $id,
                'extension_id' => $extension_id,
            );
            $db->insertObject('#__update_sites_extensions', $updateSiteExtension);
        } else {
            // Loop through all update sites
            foreach ($updateSiteIDs as $id) {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from($db->quoteName('#__update_sites'))
                    ->where($db->quoteName('update_site_id') . ' = ' . $db->quote($id));
                $db->setQuery($query);
                $aSite = $db->loadObject();

                // Does the location match?
                if (isset($aSite) && $aSite->location == $update_site['location']) {

                    // Do we have the extra_query property (J 3.2+) and does it match?
                    if (property_exists($aSite, 'extra_query')) {
                        if ($aSite->extra_query == $update_site['extra_query']) {
                            continue;
                        }
                    } else {
                        // Joomla! 3.1 or earlier. Updates may or may not work.
                        continue;
                    }
                }

                $update_site['update_site_id'] = $id;
                $newSite = (object)$update_site;
                $db->updateObject('#__update_sites', $newSite, 'update_site_id', true);
            }
        }
    }

    /**
     * Get the update id from the updates table
     *
     * @param string $extension
     * @param string $type
     * @since 2.1.0
     */
    public function getUpdateId($extension = 'com_customfilters', $type = 'component')
    {
        // Get the update ID to ourselves
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->select($db->quoteName('update_id'))
            ->from($db->quoteName('#__updates'))
            ->where($db->quoteName('type') . ' = ' . $db->quote($type))
            ->where($db->quoteName('element') . ' = ' . $db->quote($extension));
        $db->setQuery($query);
        $update_id = $db->loadResult();

        if (empty($update_id)) {
            return false;
        }
        return $update_id;
    }

    /**
     * Does the user need to enter a Download ID in the component's Options page?
     *
     * @return bool
     */
    public function needsDownloadID()
    {
        // Do I need a Download ID?
        $ret = true;

        JLoader::import('joomla.application.component.helper');
        $dlid = \cfHelper::getValue('update_dlid', '');

        if (preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid)) {
            $ret = false;
        }
        return $ret;
    }

    /**
     * Function that returns version info in JSON format
     *
     * @return string
     * @since 1.3.1
     */
    function getVersionInfo($updateFrequency = 2)
    {
        require_once(JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'update.php');
        $version_info = array();
        $html = '';
        $html_current = '';
        $html_outdated = '';
        $pathToXML = JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'customfilters.xml';
        $installData = Installer::parseXMLInstallFile($pathToXML);

        $updateHelper = \extensionUpdateHelper::getInstance($extension = 'com_customfilters_pro', $targetFile = 'assets/lastversion.ini', $updateFrequency = 2);
        $updateRegistry = $updateHelper->getData();

        if ($installData['version']) {
            if (is_object($updateRegistry) && $updateRegistry !== false) {
                $isoutdated_code = version_compare($installData['version'], $updateRegistry->version);
                if ($isoutdated_code < 0) {
                    $html_current = '<div class="cfversion">
					<span class="pbversion_label">' . Text::_('COM_CUSTOMFILTERS_LATEST_VERSION') . ' : v. </span>
					<span class="cfversion_no">' . $updateRegistry->version . '</span><span> (' . $updateRegistry->date . ')</span>
					</div>';
                }

                if ($isoutdated_code < 0) $html_outdated = ' <span id="cfoutdated">!Outdated</span>';
                else $html_outdated = ' <span id="cfupdated">Updated</span>';
            }

            $html .= '<div class="cfversion">
			<span class="pbversion_label">' . Text::_('COM_CUSTOMFILTERS_CURRENT_VERSION') . ' : v. </span>
			<span class="cfversion_no">' . $installData['version'] . '</span><span> (' . $installData['creationDate'] . ')</span>' . $html_outdated .
                '</div>';

        }
        $html .= $html_current;
        $version_info['html'] = $html;
        $version_info['status_code'] = $isoutdated_code;
        return $version_info;
    }
}
