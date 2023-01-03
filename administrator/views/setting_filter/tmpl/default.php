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
 * @date       20.12.22 11:15
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_( 'behavior.formvalidator' );
/*echo '<pre>';
print_r( $this->item );
echo '</pre>'.__FILE__.' '.__LINE__;*/
/**
 * @var Joomla\CMS\Form\Form $form
 */
$form = $this->form;
?>
<form action="<?=Joomla\CMS\Router\Route::_( 'index.php' )?>" method="post" name="adminForm" id="setting_filter">

	<?php
	$options = array(
		'active'    => 'tab1_id'    // Not in docs, but DOES work
	); ?>

    
	<?php echo JHtml::_('bootstrap.startTabSet', 'FilterSetting', $options);?>

	<?php echo JHtml::_('bootstrap.addTab', 'FilterSetting', 'tab1_id', Text::_('Значения полей')); ?>
    <?= $this->loadTemplate('hidden_value');  ?>
	<?php echo JHtml::_('bootstrap.endTab');?>

	<?php echo JHtml::_('bootstrap.addTab', 'FilterSetting', 'tab2_id', Text::_('Расширенные настройки')); ?>
	    <?= $this->loadTemplate('advanced_settings');  ?>
	<?php echo JHtml::_('bootstrap.endTab');?>

	<?php echo JHtml::_('bootstrap.endTabSet');?>

	<?= $this->loadTemplate('buttons');  ?>

    <script>
        jQuery(function($){ $("#FilterSetting a").click(function (e) {
            e.preventDefault();$(this).tab("show");});
        });
        jQuery(function($){ $("#FilterSettingTabs").append(
            $("<li class=\" active\"><a href=\"#tab1_id\" data-toggle=\"tab\">Значения полей<\/a><\/li>"));
        });
        jQuery(function($){ $("#FilterSettingTabs").append(
            $("<li class=\"\"><a href=\"#tab2_id\" data-toggle=\"tab\">Расширенные настройки<\/a><\/li>"));
        });
    </script>
    <?php
    
//    $doc = \Joomla\CMS\Factory::getDocument();
//    echo'<pre>';print_r( $doc  );echo'</pre>'.__FILE__.' '.__LINE__;
//    die(__FILE__ .' '. __LINE__ );

    
    ?>
    
    
    <div class="name-element">

    </div>

	<?=$this->form->renderFieldset( 'basic' )?>

    <input type="hidden" name="id" value="<?= $this->item->id ?>"/>
    <input type="hidden" name="vm_custom_id" value="<?= $this->item->vm_custom_id ?>"/>
    <input type="hidden" name="option" value="com_customfilters"/>
    <input type="hidden" name="view" value="setting_filter"/>
    <input type="hidden" name="task" value=""/>
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
