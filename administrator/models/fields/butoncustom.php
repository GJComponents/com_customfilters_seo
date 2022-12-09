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
 * @date       19.11.22 12:34
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
/**
 *
 * @Copyright   Copyright © 2010-2022 Gartes.  All rights reserved.
 * @license     GNU Geneal Public License 2 or later, see COPYING.txt for license details.
 */

defined('JPATH_BASE') or die;

/**
 * Class JFormFieldButoncustom
 *
 * @since 3.9
 */
class JFormFieldButoncustom extends \Joomla\CMS\Form\FormField
{
	/**
	 * The form field type.
	 *
	 * @since  1.7.0
	 * @var    string
	 */
	protected $type = 'butoncustom';
	/**
	 * The class of the form field
	 *
	 * @var    mixed
	 * @since  3.2
	 */
	protected $class;
	/**
	 * The label for the form field.
	 *
	 * @var    string
	 * @since  1.7.0
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

		$html = '<button   ';
			$html .=  'type="'. ( $this->element['button_type'] ?  (string) $this->element['button_type']  : 'button' ) .'" ';
			$html .=  !$this->onclick ?: 'onclick="'.$this->onclick.'" ';
			$html .= 'class="btn btn-small button-apply btn-success '.$this->class.'">';
			$html .= '<span class="icon-apply icon-white" aria-hidden="true"></span>';
			$html .=  $this->label ;
			$html .= '</button>';

		return $html;
	}

	/**
	 * @return string
	 * @since 3.9
	 */
	function getLabel()
	{
		return '';
	}
}