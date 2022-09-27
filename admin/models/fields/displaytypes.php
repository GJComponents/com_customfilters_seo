<?php
/**
 * @package customfilters
 * @version $Id: fields/displayTypes.php  2014-6-03 sakisTerzis $
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2012-2018 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 *
 * Class that generates a filter list
 * @author Sakis Terzis
 * @since 1.0
 */
Class JFormFieldDisplaytypes extends JFormFieldList
{
    protected $type = 'Displaytypes';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.0
	 */
	protected function getOptions()
	{
	    /** @var CustomfiltersModelCustomfilters $modelCustomfilters */
        $modelCustomfilters = JModelLegacy::getInstance('Customfilters', 'CustomfiltersModel');
        $options = $modelCustomfilters->getAllDisplayTypes();
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
