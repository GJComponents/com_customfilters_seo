<?php

/***********************************************************************************************************************
 * ╔═══╗ ╔══╗ ╔═══╗ ╔════╗ ╔═══╗ ╔══╗  ╔╗╔╗╔╗ ╔═══╗ ╔══╗   ╔══╗  ╔═══╗ ╔╗╔╗ ╔═══╗ ╔╗   ╔══╗ ╔═══╗ ╔╗  ╔╗ ╔═══╗ ╔╗ ╔╗ ╔════╗
 * ║╔══╝ ║╔╗║ ║╔═╗║ ╚═╗╔═╝ ║╔══╝ ║╔═╝  ║║║║║║ ║╔══╝ ║╔╗║   ║╔╗╚╗ ║╔══╝ ║║║║ ║╔══╝ ║║   ║╔╗║ ║╔═╗║ ║║  ║║ ║╔══╝ ║╚═╝║ ╚═╗╔═╝
 * ║║╔═╗ ║╚╝║ ║╚═╝║   ║║   ║╚══╗ ║╚═╗  ║║║║║║ ║╚══╗ ║╚╝╚╗  ║║╚╗║ ║╚══╗ ║║║║ ║╚══╗ ║║   ║║║║ ║╚═╝║ ║╚╗╔╝║ ║╚══╗ ║╔╗ ║   ║║
 * ║║╚╗║ ║╔╗║ ║╔╗╔╝   ║║   ║╔══╝ ╚═╗║  ║║║║║║ ║╔══╝ ║╔═╗║  ║║─║║ ║╔══╝ ║╚╝║ ║╔══╝ ║║   ║║║║ ║╔══╝ ║╔╗╔╗║ ║╔══╝ ║║╚╗║   ║║
 * ║╚═╝║ ║║║║ ║║║║    ║║   ║╚══╗ ╔═╝║  ║╚╝╚╝║ ║╚══╗ ║╚═╝║  ║╚═╝║ ║╚══╗ ╚╗╔╝ ║╚══╗ ║╚═╗ ║╚╝║ ║║    ║║╚╝║║ ║╚══╗ ║║ ║║   ║║
 * ╚═══╝ ╚╝╚╝ ╚╝╚╝    ╚╝   ╚═══╝ ╚══╝  ╚═╝╚═╝ ╚═══╝ ╚═══╝  ╚═══╝ ╚═══╝  ╚╝  ╚═══╝ ╚══╝ ╚══╝ ╚╝    ╚╝  ╚╝ ╚═══╝ ╚╝ ╚╝   ╚╝
 *----------------------------------------------------------------------------------------------------------------------
 * @author     Gartes | sad.net79@gmail.com | Telegram : @gartes
 * @date       12.11.22 11:49
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\Form;

/**
 * @var Joomla\CMS\Form\Form $form
 */
$form = $this->form ;

//echo'<pre>';print_r( $this->form );echo'</pre>'.__FILE__.' '.__LINE__;

HTMLHelper::_('behavior.formvalidator');


?>
<div class="addFilterCitySeo">
    <form class="form-validate" action="<?= Joomla\CMS\Router\Route::_('index.php') ?>" method="post" name="adminForm" id="adminForm">

        <div id="form_content">
            <?php  if ( $this->document->_type == 'json')  echo $this->loadTemplate('head_form'); #END IF  ?>

            <div class="name-element">
	            <?= $this->form->renderField('alias')  ?>
            </div>

            <ul class="nav nav-tabs" id="ID-Tabs-GroupTabs">
                <li class="active">
                    <a href="#basic" data-toggle="tab">
						<?= Text::_('COM_CUSTOMFILTERS_BASIC_SETTINGS') ?>
                    </a>
                </li>
                <li class="">
                    <a href="#cities" data-toggle="tab">
						<?= Text::_('COM_CUSTOMFILTERS_CITIES_SETTINGS') ?>
                    </a>
                </li>
                <li class="">
                    <a href="#customs_settings" data-toggle="tab">
	                    <?= Text::_('COM_CUSTOMFILTERS_CUSTOMS_SETTINGS') ?>
                    </a>
                </li>
            </ul>
            <div class="tab-content" id="ID-Tabs-GroupContent">

                <!-- Вкладка с общими настройками -->
                <div id="basic" class="tab-pane active">
			        <?php  echo $this->loadTemplate('add_city_seo_basic_settings'); ?>
                </div>

                <!-- Вкладка с городами-->
                <div id="cities" class="tab-pane">
                    <div class="table-city span8">
	                    <?= $this->loadTemplate('add_city_seo_cities_settings'); ?>
                    </div>
                    <div class="city-statistic right-bar span4">
	                    <?= $this->form->renderFieldset('city_statistic') ?>
                    </div>

                </div>
                <!-- Вкладка customs -->
                <div id="customs_settings" class="tab-pane">
                    <div class="table-city span8">
	                    <?= $this->loadTemplate('add_city_seo_customs_settings'); ?>
                    </div>
                    <div class="customs-statistic right-bar span4">
	                    <?= $this->loadTemplate('customs_right_bar_statistic'); ?>

                    </div>
                </div>

            </div>
        </div>


	    <?= $this->form->renderFieldset('hidden_fields') ?>
        <input type="hidden" name="task" value="">
        <input type="hidden" name="option" value="com_customfilters">
        <input type="hidden" name="view" value="setting_city">

        <?= JHtml::_('form.token')  ?>

	    <?php  if ( $this->document->_type == 'json')  echo $this->loadTemplate('footer_form'); #END IF  ?>
    </form>
</div>


