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
 * @date       21.12.22 10:36
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/

/**
 * @Copyright   Copyright © 2010-2022 Gartes.  All rights reserved.
 * @license     GNU Geneal Public License 2 or later, see COPYING.txt for license details.
 */

use Joomla\CMS\Language\Text;

defined( 'JPATH_BASE' ) or die;

/**
 * Class JFormFieldCustomfiltersselect
 *
 * @since 3.9
 */
class JFormFieldCustomfiltersselect extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @since  1.7.0
	 * @var    string
	 */
	protected $type = 'customfiltersselect';
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
	protected $label;

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
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   3.7.0
	 */
	protected function getOptions(){
		$app = \Joomla\CMS\Factory::getApplication();
		$cid = $app->input->get( 'cid' , false , 'INT' );

		$options       = [];
		$customFilters = \cftools::getCustomFilters( '' , false );



		foreach ( $customFilters as $customFilter )
		{
			// Если передан FilterId - то пропускаем
			if ( $cid == $customFilter->id ) continue ; #END IF

			$selectedStr = '';
			$tmp = array(
				'value'    => $customFilter->custom_id ,
				'text'     => Text::_( $customFilter->custom_title ),
//				'disable'  => $disabled,
//				'class'    => (string) $option['class'],
//				'selected' => ($checked || $selected),
//				'checked'  => ($checked || $selected),
			);
			$options[] = $tmp ;
		}#END FOREACH




		return $options;
	}
	/**
	 * @return string
	 * @since    1.6
	 */
	function getLabel()
	{
		return parent::getLabel();
	}
}