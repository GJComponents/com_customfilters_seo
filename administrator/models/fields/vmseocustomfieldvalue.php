<?php


/***********************************************************************************************************************
 *  ///////////////////////////╭━━━╮╱╱╱╱╱╱╱╱╭╮╱╱╱╱╱╱╱╱╱╱╱╱╱╭━━━╮╱╱╱╱╱╱╱╱╱╱╱╱╭╮////////////////////////////////////////
 *  ///////////////////////////┃╭━╮┃╱╱╱╱╱╱╱╭╯╰╮╱╱╱╱╱╱╱╱╱╱╱╱╰╮╭╮┃╱╱╱╱╱╱╱╱╱╱╱╱┃┃////////////////////////////////////////
 *  ///////////////////////////┃┃╱╰╯╭━━╮╭━╮╰╮╭╯╭━━╮╭━━╮╱╱╱╱╱┃┃┃┃╭━━╮╭╮╭╮╭━━╮┃┃╱╭━━╮╭━━╮╭━━╮╭━╮////////////////////////
 *  ///////////////////////////┃┃╭━╮┃╭╮┃┃╭╯╱┃┃╱┃┃━┫┃━━┫╭━━╮╱┃┃┃┃┃┃━┫┃╰╯┃┃┃━┫┃┃╱┃╭╮┃┃╭╮┃┃┃━┫┃╭╯////////////////////////
 *  ///////////////////////////┃╰┻━┃┃╭╮┃┃┃╱╱┃╰╮┃┃━┫┣━━┃╰━━╯╭╯╰╯┃┃┃━┫╰╮╭╯┃┃━┫┃╰╮┃╰╯┃┃╰╯┃┃┃━┫┃┃/////////////////////////
 *  ///////////////////////////╰━━━╯╰╯╰╯╰╯╱╱╰━╯╰━━╯╰━━╯╱╱╱╱╰━━━╯╰━━╯╱╰╯╱╰━━╯╰━╯╰━━╯┃╭━╯╰━━╯╰╯/////////////////////////
 *  ///////////////////////////╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱┃┃//  (C) 2022  ///////////////////
 *  ///////////////////////////╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╰╯/////////////////////////////////
 *----------------------------------------------------------------------------------------------------------------------
 * @author     Gartes | sad.net79@gmail.com | Telegram : @gartes
 * @date       20.12.22 15:41
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/

/**
 * @Copyright   Copyright © 2010-2022 Gartes.  All rights reserved.
 * @license     GNU Geneal Public License 2 or later, see COPYING.txt for license details.
 */

defined( 'JPATH_BASE' ) or die;

JLoader::register('JFormFieldList' , JPATH_LIBRARIES . '/joomla/form/fields/list.php');

/**
 * Class JFormFieldVmseocustomfieldvalue
 *
 * @since 3.9
 */
class JFormFieldVmseocustomfieldvalue extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @since  1.7.0
	 * @var    string
	 */
	protected $type = 'vmseocustomfieldvalue';
	/**
	 * The class of the form field
	 *
	 * @since  3.2
	 * @var    mixed
	 */
	protected $class;
	/**
	 * The label for the form field.
	 *
	 * @since  1.7.0
	 * @var    string
	 */
	protected $label;/**
 * @since 3.9
 * @var array|mixed
 */
	protected $_db;

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 * @since    1.6
	 */
	protected function getInput()
	{

		return parent::getInput();
	}

	/**
	 * Получить список значений на настраиваемого поля
	 * @param int|bool $vm_custom_id
	 *
	 * @return array
	 * @throws Exception
	 * @since 3.9
	 */
	public function getOptions( $vm_custom_id = false ):array
	{
		$app = \Joomla\CMS\Factory::getApplication();
		$this->_db = JFactory::getDbo();

		if ( !$vm_custom_id )
		{

			$vm_custom_id = $app->input->get( 'custom_id' , false , 'RAW' );
		}#END IF

		if ( !$vm_custom_id )
		{
			$app->enqueueMessage('Не передано ID Custom Field' , 'error');
			return [] ;
		}#END IF

		$Query = $this->_db->getQuery(true);
		$select = [
			$this->_db->quoteName('customfield_value' , 'value'),
			$this->_db->quoteName('customfield_value' , 'text'),
		];
		$Query->select( $select );
		$Query->from( $this->_db->quoteName('#__virtuemart_product_customfields') );
		$where = [
			$this->_db->quoteName('virtuemart_custom_id') . '=' . $this->_db->quote( $vm_custom_id ),
			$this->_db->quoteName('published') . '=' . $this->_db->quote( 1 ),
		];
		$Query->where( $where );
		$Query->group($this->_db->quoteName('customfield_value') );
		$this->_db->setQuery($Query);
		$result = $this->_db->loadAssocList();
//		echo'<pre>';print_r( $result );echo'</pre>'.__FILE__.' '.__LINE__;
//		die(__FILE__ .' '. __LINE__ );

		return  $result ;

	}

}