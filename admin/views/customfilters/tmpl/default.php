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

HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.multiselect');

// set some attributes to our chosen select
HTMLHelper::_('formbehavior.chosen', '#filter_type_id', null, ['placeholder_text_multiple' => '-' . Text::_('COM_CUSTOMFILTERS_SELECT_DISPLAY_TYPE') . '-', 'disable_search_threshold' => 3]);
HTMLHelper::_('formbehavior.chosen', 'select:not(.cfDisplayTypes)');

$model = $this->getModel();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'cf.ordering';
$published_opt = array(array('value' => 1, 'text' => Text::_('Published')), array('value' => 0, 'text' => Text::_('Unpublished')));
$boolean_options = array(HTMLHelper::_('select.option', 1, Text::_('JYES')), HTMLHelper::_('select.option', 0, Text::_('JNO')));

if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_customfilters&task=customfilters.saveOrderAjax&tmpl=component';
    HTMLHelper::_('sortablelist.sortable', 'customfilterlist', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>

<?php if (version_compare(JVERSION, '3.8.1', 'lt')): ?>
    <div class="alert alert-info">
        <?php echo Text::_('COM_CUSTOMFILTERS_UPDATE_JOOMLA_VERSION'); ?>
    </div>
<?php endif; ?>

<?php if ($this->needsdlid): ?>
    <div class="alert">
        <?php echo Text::sprintf('COM_CUSTOMFILTERS_NEEDS_DLD', 'https://breakdesigns.net/custom-filters-manual-pro/49-using-the-live-update-49'); ?>
    </div>
<?php endif; ?>

<?php if (empty($this->items)) : ?>
    <div class="alert alert-no-items">
        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
    </div>
<?php endif;?>

<form action="<?php echo Route::_('index.php?option=com_customfilters&view=customfilters'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="totals">
	    <?php echo $this->pagination->getResultsCounter() ;?>
	</div>
	<br clear="all" />
	<div id="j-main-container">
        <?php
        // Search tools bar
        echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
        ?>

		<table class="table table-striped" id="customfilterlist">
			<thead>
				<tr>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo HTMLHelper::_('searchtools.sort', '', 'cf.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                    </th>
                    <th width="1%" class="center">
                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                    </th>
                    <th width="1%" style="min-width: 55px" class="nowrap center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'cf.published', $listDirn, $listOrder); ?>
                    </th>
					<th>
                        <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'vmc.custom_title', $listDirn, $listOrder); ?>
					</th>
					<th width="12%" class="nowrap hidden-phone hidden-tablet">
                        <?php echo Text::_('CUSTOM_FIELD_DESCRIPTION'); ?>
					</th>
					<th>
                        <?php echo HTMLHelper::_('searchtools.sort', 'CUSTOM_FIELD_TYPE', 'vmc.field_type', $listDirn, $listOrder); ?>
					</th>
					<th id="header-displaytype">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_CUSTOMFILTERS_DISPLAY_TYPE', 'cf.type_id', $listDirn, $listOrder); ?>
					</th>

                    <th id="header-smartsearch" class="nowrap hidden-phone">
                        <?php echo Text::_('COM_CUSTOMFILTERS_ON_SEO');?>
					</th>

                    <th id="header-smartsearch" class="nowrap hidden-phone">
                        <?php echo Text::_('COM_CUSTOMFILTERS_SMART_SEARCH');?>
                    </th>

					<th id="header-expanded">
                        <?php echo Text::_('COM_CUSTOMFILTERS_EXPANDED');?>
					</th>
                    <th id="header-scrollbarafter" class="nowrap hidden-phone hidden-tablet">
                        <?php echo Text::_('COM_CUSTOMFILTERS_SCROLLBARAFTER');?>
					</th>
                    <th class="nowrap hidden-phone hidden-tablet">
                        <?php echo Text::_('COM_CUSTOMFILTERS_ADV_SETTINGS');?>
                    </th>
                    <th width="1%" class="nowrap hidden-phone hidden-tablet">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_CUSTOMFILTERS_CUSTOM_ID', 'cf.vm_custom_id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="15">
                        <?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php




			foreach($this->items as $i => $item){
                $displayTypes=$model->getDisplayTypes($item->data_type);


//                echo'<pre>';print_r( $item );echo'</pre>'.__FILE__.' '.__LINE__;
//                die(__FILE__ .' '. __LINE__ );

                
				 ?>
                <!-- We use 1 as 'sortable-group-id' (the same) for all the records, as they all belong in the same group (e.g. category)-->
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
                    <td class="order nowrap center hidden-phone">
                        <?php
                        $canChange = true;
                        $iconClass = '';
                        if (!$canChange)
                        {
                            $iconClass = ' inactive';
                        }
                        elseif (!$saveOrder)
                        {
                            $iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::_('tooltipText', 'JORDERINGDISABLED');
                        }
                        ?>
                        <span class="sortable-handler<?php echo $iconClass; ?>">
                            <span class="icon-menu" aria-hidden="true"></span>
                        </span>
                        <?php if ($canChange && $saveOrder) : ?>
                            <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order" />
                        <?php endif; ?>

                        <!-- we still have the alias field in the database and the save query (although useless) -->
                        <input type="hidden" name="alias[<?php echo $item->id?>]" class="cf_alias_input" id="cf_alias_<?php echo $item->id?>" disabled="disabled" class="inputbox" size="45" value="<?php echo $item->alias ?>" />
                    </td>
					<td class="center">
                        <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="center">
                        <?php echo HTMLHelper::_('jgrid.published', $item->published, $i,'customfilters.'); ?>
					</td>
                    <td class="left">
                        <?php echo $item->custom_title ?>

                        <?php
                        // if there is translation different to the title, display that as well.
                        $titleTranslated = Text::_($item->custom_title);
                        if($titleTranslated != $item->custom_title) {?>
                            <div class="small">
                                <?php echo $titleTranslated?>
                            </div>
                        <?php
                        }?>
                    </td>
					<td class="left small hidden-phone hidden-tablet">
                        <div class="cf-vm-customfield-desciption">
                            <?php echo $item->custom_descr ?>
                        </div>
					</td>
					<td class="left">
                        <?php echo $item->field_type_string ?>
					</td>

                    <td class="left">
                        <?php echo  HTMLHelper::_('select.genericlist', $displayTypes,"type_id[$item->id]",'class="inputbox cfDisplayTypes" size="1" aria-labelledby="header-displaytype"', 'value', 'text',$item->type_id);?>
					</td>

                    <?php

//                    echo'<pre>';print_r( $item );echo'</pre>'.__FILE__.' '.__LINE__;
//                    die(__FILE__ .' '. __LINE__ );


                    ?>

                    <td class="center nowrap hidden-phone">
                        <div id="on_seo_<?php echo $item->id?>" class="cfCheckboxGroup">
                            <!-- Нам нужно скрыть ввод здесь, потому что флажок не будет отправлен, если он не установлен. Пустые значения не обрабатываются процессом сохранения -->
                            <!-- We need the input hidden here, because the checkbox is not submitted if not selected. Empty values are not handled by the save process-->
                            <input type="hidden" name="on_seo[<?php echo $item->id?>]" value="0"/>
                            <?php $checked = $item->on_seo ? 'checked' : '';?>
                            <input id="on_seo_input_<?php echo $item->id?>" type="checkbox" name="on_seo[<?php echo $item->id?>]" value="1" <?=$checked?> aria-labelledby="header-on_seo"/>
                            <label for="on_seo_input_<?php echo $item->id?>"></label>
                        </div>
                    </td>


                    <td class="center nowrap hidden-phone">
                        <div id="smart_search_<?php echo $item->id?>" class="cfCheckboxGroup">
                            <!-- We need the input hidden here, because the checkbox is not submitted if not selected. Empty values are not handled by the save process-->
                            <input type="hidden" name="smart_search[<?php echo $item->id?>]" value="0"/>
                            <?php $checked = $item->smart_search ? 'checked' : '';?>
                            <input id="smart_search_input_<?php echo $item->id?>" type="checkbox" name="smart_search[<?php echo $item->id?>]" value="1" <?php echo $checked?> aria-labelledby="header-smartsearch"/>
                            <label for="smart_search_input_<?php echo $item->id?>"></label>
                        </div>
					</td>


                    <td class="left">
                        <div id="expanded<?php echo $item->id?>" class="cfCheckboxGroup">
                            <!-- We need the input hidden here, because the checkbox is not submitted if not selected. Empty values are not handled by the save process-->
                            <input type="hidden" name="expanded[<?php echo $item->id?>]" value="0"/>
                            <?php $checked = $item->expanded ? 'checked' : '';?>
                            <input id="expanded_input_<?php echo $item->id?>" type="checkbox" name="expanded[<?php echo $item->id?>]" value="1" <?php echo $checked?> aria-labelledby="header-expanded"/>
                            <label for="expanded_input_<?php echo $item->id?>"></label>
                        </div>

					</td>
					<td class="nowrap hidden-phone hidden-tablet">
                        <input type="text" name="scrollbar_after[<?php echo $item->id?>]" value="<?php echo $item->scrollbar_after ?>"
                               class="inputbox" size="5" maxlength="10" pattern="[\d.]+(px|em|rem|vw|vh|vmin|vmax|ex|cm|mm|pt|in|%){1}"
                               aria-labelledby="header-scrollbarafter" aria-details="Set the height, after which a scrollbar will be applied (e.g. 100px)."/>
					</td>

                    <td class="nowrap hidden-phone hidden-tablet">
                        <button type="button" class="btn btn-small cf_adv_settings" id="show_popup<?php echo $item->id?>">
                            <span class="icon-eye"></span>
                            <span>
                                <?php echo Text::_('COM_CUSTOMFILTERS_ADV_SETTINGS');?>
                            </span>
                        </button>
                        <?php //load the settings popup
                        require(dirname(__FILE__).DIRECTORY_SEPARATOR.'default_settings.php');?>
                    </td>

					<td class="nowrap hidden-phone hidden-tablet center">
                        <?php echo $item->custom_id ?>
					</td>

				</tr>
				<?php }	?>
			</tbody>
		</table>
	</div>
	<input type="hidden" name="option" value="com_customfilters" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

<?php echo $this->loadTemplate('update'); ?>


