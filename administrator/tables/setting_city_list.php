<?php
/**
 * Customfilter table
 *
 * @package		Customfilters
 * @since		1.5
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * 
 * Table class
 * @author Sakis Terz
 *
 */
class CustomfiltersTableSetting_city_list extends \Joomla\CMS\Table\Table{
/**
	 * Constructor
	 *
	 * @since	1.5
	 */
	function __construct(&$_db)
	{

		parent::__construct('#__cf_customfields_setting_city', 'id', $_db);
	}
}