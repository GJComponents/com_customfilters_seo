<?php
/**
 * @package 	customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2012-2021 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @since		1.0
 */

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;

die(__FILE__ .' '. __LINE__ );


?>

<form action="<?php echo Route::_('index.php?option=com_customfilters&view=customfilters'); ?>" method="post" name="adminForm" id="adminForm">

    <div id="form_content">
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
        </ul>
        <div class="tab-content" id="ID-Tabs-GroupContent">

            <!-- Вкладка с общими настройками -->
            <div id="basic" class="tab-pane active">
                <?php  echo $this->loadTemplate('add_city_seo_basic_settings'); ?>
            </div>

            <!-- Вкладка с городами-->
            <div id="cities" class="tab-pane">
	            <?php  echo $this->loadTemplate('add_city_seo_cities_settings'); ?>

            </div>
        </div>



    </div>

    <div id="form_footer">
        form_footer form_footer form_footer form_footer
    </div>


    <input type="hidden" name="option" value="com_customfilters"/>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
	<?= $this->form->renderFieldset('hidden_fields') ?>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

<?php // echo $this->loadTemplate('update'); ?>


