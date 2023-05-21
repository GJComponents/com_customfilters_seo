<?php
/**
 * @package     customfilters
 *
 * @copyright   Copyright © 2022 breakdesigns.net. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

\defined('_JEXEC') or die();

use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\Rules\RulesInterface;
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;

require_once JPATH_SITE. DIRECTORY_SEPARATOR. 'components'.DIRECTORY_SEPARATOR. 'com_customfilters'.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'tools.php';

class FilterRules implements RulesInterface
{
	/**
	 * Router this rule belongs to
	 *
	 * @var RouterView
	 * @since 2.12.0
	 */
	protected $router;

	/**
	 * Class constructor.
	 *
	 * @param   RouterView  $router  Router this rule belongs to
	 *
	 * @since 2.12.0
	 */
	public function __construct(RouterView $router)
	{
		$this->router = $router;
	}

	public function preprocess(&$query)
	{
		// TODO: Implement preprocess() method.
	}

	/**
	 * Parse a sef url
	 *
	 * @param   array  $segments
	 * @param   array  $vars
	 *
	 * @since 2.12.0
	 */
	public function parse(&$segments, &$vars)
	{
		$CfRouterHelper = CfRouterHelper::getInstance();
		$siteLang = $CfRouterHelper->getLangPrefix();
		$defaultSiteLang = $CfRouterHelper->getDefaultLangPrefix();

		// Fix the segments
		$total = count($segments);
		for ($i = 0; $i < $total; $i ++) {
			$segments[$i] = preg_replace('/:/', '-', $segments[$i], 1);
		}

		// empty filters strings
		$no_category = urlencode(Text::_('CF_NO_VMCAT'));
		$no_manufacturer = urlencode(Text::_('CF_NO_VMMANUF'));
		$db = Factory::getDbo();

		$categories_ar = explode('__or__', $segments[0]);
		if (count($categories_ar) == 1 && $categories_ar[0] == $no_category) {} else {
			unset($segments[0]);

			// It's multi-lingual
			if ($CfRouterHelper->getDefaultLang()) {
				// prepare the slugs for the query
				array_walk($categories_ar, function (&$value, $key) {
					$db = Factory::getDbo();
					$value = '(lang_def.slug=' . $db->quote($db->escape($value)) . ' OR lang.slug=' . $db->quote($db->escape($value)) . ')';
				});

				$vmcat_where_str = implode(' OR ', $categories_ar);

				if ($vmcat_where_str) {
					// Add the manuf alias
					$q = $db->getQuery(true);
					$q->select("IFNULL(lang.virtuemart_category_id, lang_def.virtuemart_category_id) AS virtuemart_category_id");
					$q->from('#__virtuemart_categories_' . $defaultSiteLang . ' AS lang_def');
					$q->leftJoin("#__virtuemart_categories_" . $siteLang . " AS lang ON lang_def.virtuemart_category_id=lang.virtuemart_category_id");
					$q->where($vmcat_where_str);
					$db->setQuery($q);
					$vm_cat_ids = $db->loadColumn();
					$vars['virtuemart_category_id'] = $vm_cat_ids;
				}
			} else {
				// prepare the slugs for the query
				array_walk($categories_ar, function (&$value, $key) {
					$db = Factory::getDbo();
					$value = 'slug=' . $db->quote($db->escape($value));
				});

				$vmcat_where_str = implode(' OR ', $categories_ar);

				if ($vmcat_where_str) {
					$q = $db->getQuery(true);
					$q->select('virtuemart_category_id');
					$q->from('#__virtuemart_categories_' . $siteLang);
					$q->where($vmcat_where_str);
					$db->setQuery($q);
					$vm_cat_ids = $db->loadColumn();
					$vars['virtuemart_category_id'] = $vm_cat_ids;
				}
			}
		}
		if (isset($segments[1])) {

			$manuf_ar = explode('__or__', $segments[1]);
			if (count($manuf_ar) == 1 && $manuf_ar[0] == $no_manufacturer) {} else {
				unset($segments[1]);

				// It's multi-lingual
				if ($CfRouterHelper->getDefaultLang()) {
					// prepare the slugs for the query
					array_walk($manuf_ar, function (&$value, $key) {
						$db = Factory::getDbo();
						$value = '(lang_def.slug=' . $db->quote($db->escape($value)).' OR lang.slug=' . $db->quote($db->escape($value)).')';
					});

					$vmmnf_where_str = implode(' OR ', $manuf_ar);

					if ($vmmnf_where_str) {
						// Add the manuf alias
						$q = $db->getQuery(true);
						$q->select('IFNULL(lang_def.virtuemart_manufacturer_id, lang.virtuemart_manufacturer_id) AS virtuemart_manufacturer_id');
						$q->from('#__virtuemart_manufacturers_' . $defaultSiteLang.' AS lang_def');
						$q->leftJoin('#__virtuemart_manufacturers_' . $siteLang.' AS lang ON lang_def.virtuemart_manufacturer_id=lang.virtuemart_manufacturer_id');
						$q->where($vmmnf_where_str);
						$db->setQuery($q);
						$vm_mnf_ids = $db->loadColumn();
						$vars['virtuemart_manufacturer_id'] = $vm_mnf_ids;
					}

				}
				else {

					// prepare the slugs for the query
					array_walk($manuf_ar, function (&$value, $key) {
						$db = Factory::getDbo();
						$value = 'slug=' . $db->quote($db->escape($value));
					});

					$vmmnf_where_str = implode(' OR ', $manuf_ar);

					if ($vmmnf_where_str) {
						// Add the manuf alias
						$q = $db->getQuery(true);
						$q->select('virtuemart_manufacturer_id');
						$q->from('#__virtuemart_manufacturers_' . $siteLang);
						$q->where($vmmnf_where_str);
						$db->setQuery($q);
						$vm_mnf_ids = $db->loadColumn();
						$vars['virtuemart_manufacturer_id'] = $vm_mnf_ids;
					}
				}
			}
		}
	}

	/**
	 * Build a sef url
	 *
	 * @param   array  $query
	 * @param   array  $segments
	 *
	 * @throws Exception
	 * @since  2.12.0
	 */
	public function build(&$query, &$segments)
	{
		// Get the menu item belonging to the Itemid that has been found
		$menuItem = $this->router->menu->getItem($query['Itemid']);

		/*
		 * Component do not match the menu item
		 * or view is not set
		 */
		if ($menuItem && ($menuItem->component !== 'com_' . $this->router->getName() || !isset($menuItem->query['view']))) {
			return;
		}

		$db = Factory::getDbo();

		// first get the filters
		if (!empty($query['virtuemart_category_id']) && is_array($query['virtuemart_category_id'])) {
			$vm_categories = $query['virtuemart_category_id'];
			$vm_categories = ArrayHelper::toInteger($vm_categories);
			$vm_categories = array_filter($vm_categories);
		}
		if (!empty($query['virtuemart_manufacturer_id']) && is_array($query['virtuemart_manufacturer_id'])) {
			$vm_manufacturers = $query['virtuemart_manufacturer_id'];
			$vm_manufacturers = ArrayHelper::toInteger($vm_manufacturers);
			$vm_manufacturers = array_filter($vm_manufacturers);
		}
		// empty filters strings
		$no_category = urlencode(Text::_('CF_NO_VMCAT'));
		$manuf_string = '';
		$categ_string = '';

		// get the variables related with the languages
		if (!empty($vm_categories) || !empty($vm_manufacturers)) {
			$CfRouterHelper = CfRouterHelper::getInstance();
			$siteLang = $CfRouterHelper->getLangPrefix();
			$defaultSiteLang = $CfRouterHelper->getDefaultLangPrefix();
		}

		// categories
		if (!empty($vm_categories)) {
			// Add the category alias
			$q = $db->getQuery(true);

			// It's multi-lingual
			if ($CfRouterHelper->getDefaultLang()) {
				$q->select("IFNULL(lang.slug, lang_def.slug) AS name");
				$q->from("#__virtuemart_categories_" . $defaultSiteLang . " AS lang_def");
				$q->leftJoin("#__virtuemart_categories_" . $siteLang . " AS lang ON lang_def.virtuemart_category_id=lang.virtuemart_category_id");
				$q->where('lang_def.virtuemart_category_id IN (' . implode(',', $vm_categories) . ')');
			} else {
				$q->select('slug');
				$q->from('#__virtuemart_categories_' . $siteLang);
				$q->where('virtuemart_category_id IN (' . implode(',', $vm_categories) . ')');
			}

			$db->setQuery($q);
			$vm_cat_aliases = $db->loadColumn();
			if ($vm_cat_aliases) {
				$categ_string = implode('__or__', $vm_cat_aliases);
			} else {
				$categ_string = $no_category;
			}
			unset($query['virtuemart_category_id']);
		}
		else {
			if (!empty($vm_manufacturers)) {
				$categ_string = $no_category;
			}
		}

		// manufacturers
		if (!empty($vm_manufacturers)) {
			$vm_manufacturers = (array)$vm_manufacturers;

			// Add the manuf alias
			$q = $db->getQuery(true);

			// It's multi-lingual
			if ($CfRouterHelper->getDefaultLang()) {
				$q->select("IFNULL(lang.slug, lang_def.slug) AS name");
				$q->from("#__virtuemart_manufacturers_" . $defaultSiteLang . " AS lang_def");
				$q->leftJoin("#__virtuemart_manufacturers_" . $siteLang . " AS lang ON lang_def.virtuemart_manufacturer_id=lang.virtuemart_manufacturer_id");
				$q->where('lang_def.virtuemart_manufacturer_id IN (' . implode(',', $vm_manufacturers) . ')');
			} else {
				$q->select('slug');
				$q->from('#__virtuemart_manufacturers_' . $siteLang);
				$q->where('virtuemart_manufacturer_id IN (' . implode(',', $vm_manufacturers) . ')');
			}

			$db->setQuery($q);
			$vm_mnf_aliases = $db->loadColumn();
			if ($vm_mnf_aliases) {
				$manuf_string = implode('__or__', $vm_mnf_aliases);
			}
			unset($query['virtuemart_manufacturer_id']);
		}

		$segments[] = $categ_string;
		$segments[] = $manuf_string;
	}

}