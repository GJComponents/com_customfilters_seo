<?php


/***********************************************************************************************************************
 *  ///////////////////////////╭━━━╮╱╱╱╱╱╱╱╱╭╮╱╱╱╱╱╱╱╱╱╱╱╱╱╭━━━╮╱╱╱╱╱╱╱╱╱╱╱╱╭╮////////////////////////////////////////
 *  ///////////////////////////┃╭━╮┃╱╱╱╱╱╱╱╭╯╰╮╱╱╱╱╱╱╱╱╱╱╱╱╰╮╭╮┃╱╱╱╱╱╱╱╱╱╱╱╱┃┃////////////////////////////////////////
 *  ///////////////////////////┃┃╱╰╯╭━━╮╭━╮╰╮╭╯╭━━╮╭━━╮╱╱╱╱╱┃┃┃┃╭━━╮╭╮╭╮╭━━╮┃┃╱╭━━╮╭━━╮╭━━╮╭━╮////////////////////////
 *  ///////////////////////////┃┃╭━╮┃╭╮┃┃╭╯╱┃┃╱┃┃━┫┃━━┫╭━━╮╱┃┃┃┃┃┃━┫┃╰╯┃┃┃━┫┃┃╱┃╭╮┃┃╭╮┃┃┃━┫┃╭╯////////////////////////
 *  ///////////////////////////┃╰┻━┃┃╭╮┃┃┃╱╱┃╰╮┃┃━┫┣━━┃╰━━╯╭╯╰╯┃┃┃━┫╰╮╭╯┃┃━┫┃╰╮┃╰╯┃┃╰╯┃┃┃━┫┃┃/////////////////////////
 *  ///////////////////////////╰━━━╯╰╯╰╯╰╯╱╱╰━╯╰━━╯╰━━╯╱╱╱╱╰━━━╯╰━━╯╱╰╯╱╰━━╯╰━╯╰━━╯┃╭━╯╰━━╯╰╯/////////////////////////
 *  ///////////////////////////╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱┃┃//  (C) 2023  ///////////////////
 *  ///////////////////////////╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╰╯/////////////////////////////////
 *----------------------------------------------------------------------------------------------------------------------
 * @author     Gartes | sad.net79@gmail.com | Telegram : @gartes
 * @date       07.02.23 19:27
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2023 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 *
 * @since 3.9
 * @copyright
 * @license
 */
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_( 'bootstrap.tooltip' );
HTMLHelper::_( 'formbehavior.chosen' );
HTMLHelper::_( 'behavior.formvalidator' );

//echo '<pre>'; print_r( $this->item ); echo '</pre>'.__FILE__.' '.__LINE__;
/**
 * @var Joomla\CMS\Form\Form $form
 */
$form = $this->form;
?>
<form action="<?=Joomla\CMS\Router\Route::_( 'index.php' )?>" method="post" name="adminForm" id="adminForm">
    <div class="name-element">
		<?=$form->renderField( 'alias' )?>
    </div>
	<?=$form->renderFieldset( 'basic' )?>

    <input type="hidden" name="option" value="com_customfilters"/>
    <input type="hidden" name="view" value="setting_seo"/>
    <input type="hidden" name="task" value=""/>
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
