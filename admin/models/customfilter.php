<?php
/**
 * The Customfilter model file
 *
 * @package    customfilters
 * @author        Sakis Terz
 * @copyright    Copyright (c) 2012-2021 breakdesigns.net. All rights reserved.
 * @license        http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

// Load the model framework
jimport('joomla.application.component.modeladmin');

/**
 * the model class
 * @author    Sakis Terz
 * @since    1.0
 */
class CustomfiltersModelCustomfilter extends JModelAdmin
{
    /**
     * @var string Model context string
     */
    private $context = 'com_customfilters.customfilter';

    /**
     * Returns a Table object, always creating it.
     *
     * @param type    The table type to instantiate.
     * @param string    A prefix for the table class name. Optional.
     * @param array    Configuration array for model. Optional.
     * @return        Table    A database object.
     * @access        public
     * @since        1.0
     */
    public function getTable($type = 'Customfilter', $prefix = 'CustomfiltersTable', $config = array())
    {
        return Table::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form located in models/forms
     *
     * @param array $data Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     * @return        mixed
     * @author        Sakis Terzis
     * @access        public
     * @since        1.0
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->context, null, array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     *
     * The function to save any change to the customfilters view
     *
     * @param array    the selected display types
     * @author    Sakis Terz
     * @since    1.0
     */
    function savefilters($type_ids, $alias, $params)
    {
        $db = JFactory::getDbo();
        $row = $this->getTable();

        $errors = array();

        //update alias
        foreach ($alias as $fltID => $al_str) {
            $data = array();
            $data['id'] = $fltID;
            $data['alias'] = $al_str;

            if (!$row->bind($data)) {
                $errors[] = $db->getErrorMsg();
            }

            // Make sure the row is valid
            if (!$row->check()) {
                $errors[] = $db->getErrorMsg();
            }

            // Store the web link table to the database
            if (!$row->store()) {
                $errors[] = $db->getErrorMsg();
            }
            unset($data);
        }

        $row2 = $this->getTable();
        //update types
        foreach ($type_ids as $fltID => $typeID) {
            $data = array();
            $data['id'] = $fltID;
            //sanitize the types
            preg_match('/[0-9]+([,]{1}[0-9]+)?/', $typeID, $matches);
            $data['type_id'] = $matches[0];

            if (!$row2->bind($data)) {
                $errors[] = $db->getErrorMsg();
            }

            // Make sure the row is valid
            if (!$row2->check()) {
                $errors[] = $db->getErrorMsg();
            }

            // Store the web link table to the database
            if (!$row2->store()) {
                $errors[] = $db->getErrorMsg();
            }
            unset($data);
        }

        $row3 = $this->getTable();
        //update params
        foreach ($params as $fltID => $param) {
            $data = array();
            $data['id'] = $fltID;
            $data['params'] = $param;

            if (!$row3->bind($data)) {
                $errors[] = $db->getErrorMsg();
            }

            // Make sure the row is valid
            if (!$row3->check()) {
                $errors[] = $db->getErrorMsg();
            }

            // Store the web link table to the database
            if (!$row3->store()) {
                $errors[] = $db->getErrorMsg();
            }
            unset($data);
        }


        if (count($errors) > 0) {
            $this->setError(Text::_('Error updating filters') . implode(',', $errors));
            return false;
        }
        return true;
    }
}
