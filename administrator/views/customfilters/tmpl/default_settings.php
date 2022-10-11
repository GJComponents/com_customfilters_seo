<?php
/**
 *
 * The file for the advanced setting
 *
 * @package 	customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2012-2021 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *				customfilters is free software. This version may have been modified
 *				pursuant to the GNU General Public License, and as distributed
 *				it includes or is derivative of works licensed under the GNU
 *				General Public License or other free or open source software
 *				licenses.
 * @version $Id: default_advanced.php 2015-04-01 19:44 sakis $
 * @since		1.8.0
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

/*
 * The class name of each li indicates for each display type should be displayed
 * e.g. class:setting6 will be displayed for the display type 6 (range slider), setting5 for range inputs
 */
?>

<div class="bdpopup cf_advacned_settings cfhide" id="window<?php echo $item->id?>">
	<a id="hide_popup<?php echo $item->id?>" class="hide_popup" aria-label="<?php echo Text::_('JLIB_HTML_BEHAVIOR_CLOSE')?>"></a>
	<h3><?php echo Text::_('COM_CUSTOMFILTERS_ADV_SETTINGS');?></h3>
	<ul class="adminformlist">

        <?php
        if ($item->data_type == 'float' || $item->data_type == 'int' || $item->data_type == 'date') {
            ShopFunctions::$categoryTree = '';
            if (count($item->filter_category_ids) > 0) {
                $categoryTree = cfHelper::categoryListTree($item->filter_category_ids);
            } else {
                $categoryTree = cfHelper::categoryListTree();
            }
            ?>

		<li class="setting6">
			<label class="cflabel" for="slider_min_value_<?php echo $item->id?>"><?php echo Text::_('COM_CUSTOMFILTERS_SLIDER_MIN_VALUE_LABEL');?>:</label> <input type="text"
			name="slider_min_value[<?php echo $item->id?>]"
			id="slider_min_value_<?php echo $item->id?>"
			value="<?php echo $item->slider_min_value ?>" class="inputbox"
			size="4" maxlength="8" />
		</li>
		<li class="setting6">
			<label class="cflabel" for="slider_max_value_<?php echo $item->id?>"><?php echo Text::_('COM_CUSTOMFILTERS_SLIDER_MAX_VALUE_LABEL');?>
			:</label> <input type="text"
			name="slider_max_value[<?php echo $item->id?>]"
			id="slider_max_value_<?php echo $item->id?>"
			value="<?php echo $item->slider_max_value ?>" class="inputbox"
			size="4" maxlength="8" />
		</li>
		<li class="setting5 setting6 setting8">
			<label class="cflabel" for="categories_<?php echo $item->id?>"><?php echo Text::_('COM_CUSTOMFILTERS_FILTER_TO_CATEGORIES');?>
			:</label>
			<select class="cf-choosen-select"
			data-placeholder="<?php echo Text::_('JOPTION_ALL_CATEGORIES');?>"
			id="categories_<?php echo $item->id?>"
			name="filter_categories[<?php echo $item->id?>][]"
			multiple="multiple">
			<?php echo $categoryTree; ?>
			</select>
		</li>
        <?php }?>

        <?php
        $customFiltersOptions = cfHelper::getCustomFiltersOptions($item->vm_custom_id, $item->display_if_filter_exist);

        $itemParams = new \Joomla\Registry\Registry($item->params);

        $use_only_one_opt = $itemParams->get('use_only_one_opt' , 0) ;




        ?>
        <li>
            <!-- Лимит количества выбранных опций -->
            <label class="cflabel" for="customfilters_<?php echo $item->id?>">
				<?= Text::_('COM_CUSTOMFILTERS_LIMIT_OPTIONS_SELECT_FOR_NO_INDEX');?>
                :</label>
            <input class="cf-input"
                    data-placeholder="<?= Text::_('COM_CUSTOMFILTERS');?>"
                    id="customfilters_<?= $item->id?>"
                    name="limit_options_select_for_no_index[<?= $item->id?>]"
                   min="0"
                   max="10"
                   type="number"
                   value="<?= $itemParams->get('limit_options_select_for_no_index' , 0 ) ?>"
            />
     </li>

        <li>
            <div class="control-group">
                <div class="control-label">
                    <label id="jform_on_show_children_category-lbl" for="jform_on_show_children_category"
                           class="hasPopover"
                           title=""
                           data-content="<?= Text::_('COM_CUSTOMFILTERS_USE_ONLY_ONE_OPT_CONTENT');?>"
                           data-original-title="<?= Text::_('COM_CUSTOMFILTERS_USE_ONLY_ONE_OPT_TITLE');?>">
	                    <?= Text::_('COM_CUSTOMFILTERS_USE_ONLY_ONE_OPT');?></label>
                </div>
                <div class="controls">
                    <fieldset id="jform_on_show_children_category" class="btn-group btn-group-yesno radio">
                        <input type="radio"
                               id="customfilters_use_only_one_opt_<?= $item->id?>0" name="use_only_one_opt[<?= $item->id?>]"
                               value="1"
	                        <?= $use_only_one_opt?'checked="checked"':'' ?>
                        />
                        <label for="customfilters_use_only_one_opt_<?= $item->id?>0"
                               class="btn <?= $use_only_one_opt?'active':'' ?>">Да</label>


                        <input type="radio"
                               id="customfilters_use_only_one_opt_<?= $item->id?>1" name="use_only_one_opt[<?= $item->id?>]"
                               value="0"
	                        <?= !$use_only_one_opt?'checked="checked"':'' ?>
                        />
                        <label for="customfilters_use_only_one_opt_<?= $item->id?>1"
                               class="btn  <?= !$use_only_one_opt?'btn-danger active':'' ?>"> Нет </label>
                    </fieldset>
                </div>
            </div>
        </li>



        <li>

            <label class="cflabel" for="customfilters_<?php echo $item->id?>">
                <?= Text::_('COM_CUSTOMFILTERS_DISPLAY_IF_SELECTED');?>
                :</label>
            <select class="cf-choosen-select"
                    data-placeholder="<?php echo Text::_('COM_CUSTOMFILTERS');?>"
                    id="customfilters_<?php echo $item->id?>"
                    name="display_if_filter_exist[<?php echo $item->id?>][]"
                    multiple="multiple">
                <?php echo implode('',$customFiltersOptions); ?>
            </select>
        </li>

        <li>
            <label class="cflabel"
                   for="conditional_operator_<?php echo $item->id ?>"><?php echo Text::_('COM_CUSTOMFILTERS_CONDITIONAL_OPERATOR'); ?>
                :</label>
            <select id="conditional_operator_<?php echo $item->id ?>"
                    name="conditional_operator[<?php echo $item->id ?>]">
                <option value="AND" <?php echo $item->conditional_operator == 'AND' ? 'selected' : '' ?>><?php echo Text::_('COM_CUSTOMFILTERS_OPERATOR_AND') ?></option>
                <option value="OR" <?php echo $item->conditional_operator == 'OR' ? 'selected' : '' ?>><?php echo Text::_('COM_CUSTOMFILTERS_OPERATOR_OR') ?></option>
            </select>
        </li>
	</ul>
    <div class="control-group text-center">
	    <button class="btn btn-block bdokbutton" id="close_btn<?php echo $item->id?>"	onclick="return false;">OK</button>
    </div>
</div>
<script type="text/javascript">displayPopup(<?php echo $item->id?>);</script>

