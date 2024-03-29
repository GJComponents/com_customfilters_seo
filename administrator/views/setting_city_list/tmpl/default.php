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
HTMLHelper::_('formbehavior.chosen',
    '#filter_type_id',
    null,
    [
            'placeholder_text_multiple' => '-' . Text::_('COM_CUSTOMFILTERS_SELECT_DISPLAY_TYPE') . '-',
            'disable_search_threshold' => 3
    ]);

HTMLHelper::_('formbehavior.chosen', 'select:not(.cfDisplayTypes)');

$model = $this->getModel();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'cf.ordering';
$published_opt = array(
        array('value' => 1, 'text' => Text::_('Published') ),
        array('value' => 0, 'text' => Text::_('Unpublished'))
);
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

	<div id="j-main-container">
        <?php
        // Search tools bar
//        echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
        ?>

		<table class="table table-striped" id="customfilterlist">
			<thead>
				<tr>
                    <!-- ordering -->
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo HTMLHelper::_('searchtools.sort', '', 'cf.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                    </th>
                    <!-- check all -->
                    <th width="1%" class="center">
                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                    </th>
                    <!-- published -->
                    <th width="1%" style="min-width: 55px" class="nowrap center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'cf.published', $listDirn, $listOrder); ?>
                    </th>
					<!-- title -->
                    <th>
                        <?= HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'vmc.custom_title', $listDirn, $listOrder); ?>
					</th>
					<!--<th width="12%" class="nowrap hidden-phone hidden-tablet">
                        <?php /*echo Text::_('CUSTOM_FIELD_DESCRIPTION'); */?>
					</th>-->
					<!--<th>
                        <?php /*echo HTMLHelper::_('searchtools.sort', 'CUSTOM_FIELD_TYPE', 'vmc.field_type', $listDirn, $listOrder); */?>
					</th>-->
					<!--<th id="header-displaytype">
                        <?php /*echo HTMLHelper::_('searchtools.sort', 'COM_CUSTOMFILTERS_DISPLAY_TYPE', 'cf.type_id', $listDirn, $listOrder); */?>
					</th>-->

                    <!-- для языков если включено Multilang -->
                    <?php
                    if ( JLanguageMultilang::isEnabled() )
                    {
	                    ?>
                        <th id="header-language_set" class="nowrap hidden-phone">
		                    <?php echo Text::_('COM_CUSTOMFILTERS_LANGUAGE_SET');?>
                        </th>
	                    <?php
                    }#END IF
                    ?>

                    <!-- Index - NoIndex-->
                    <!--<th id="header-smartsearch" class="nowrap hidden-phone">
                        <?php /*echo Text::_('COM_CUSTOMFILTERS_ON_SEO');*/?>
					</th>-->

                    <!--<th id="header-smartsearch" class="nowrap hidden-phone">
                        <?php /*echo Text::_('COM_CUSTOMFILTERS_SMART_SEARCH');*/?>
                    </th>-->

					<!--<th id="header-expanded">
                        <?php /*echo Text::_('COM_CUSTOMFILTERS_EXPANDED');*/?>
					</th>-->

                    <!--<th class="nowrap hidden-phone hidden-tablet">
                        <?php /*echo Text::_('COM_CUSTOMFILTERS_ADV_SETTINGS');*/?>
                    </th>-->
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

				 ?>
                <!-- We use 1 as 'sortable-group-id' (the same) for all the records, as they all belong in the same group (e.g. category)-->
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
                    <!-- sortable -->
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
                    <!-- grid.id -->
					<td class="center">
                        <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
					</td>
                    <!-- published -->
					<td class="center">
                        <?php echo HTMLHelper::_('jgrid.published', $item->published, $i,'customfilters.'); ?>
					</td>
                    <!--  title -->
                    <td class="left title_item">
                        <a href="<?= JRoute::_('index.php?option=com_customfilters&view=setting_city&id='.$item->id ) ?>" >
	                        <?= $item->alias ?>
                        </a>

                        <?php
//                        echo'<pre>';print_r( $item->id );echo'</pre>'.__FILE__.' '.__LINE__;
//                        die(__FILE__ .' '. __LINE__ );

                        ?>
                    </td>




                    <!-- Выбор языков для custom field - если включено Multilang -->
                    <?php
                    if ( JLanguageMultilang::isEnabled() )
                    {
                        ?>
                        <td class="left">
		                    <?php echo  HTMLHelper::_(
                                    'select.genericlist',
                                    $this->knownLanguages ,
                                    "known_languages[$item->id]",
                                    'class="inputbox knownLanguages" '
                                    .'onchange="window.CustomfiltersAdminCore.updateKnownLanguagesElement(this)"  '
                                    .'data-tbl="#__cf_customfields_setting_city" '
                                    .'size="1" '
                                    .'aria-labelledby="header-displaytype"'  ,
                                    'sef',
                                    'title',
                                    $item->known_languages
                            );?>
                        </td>
                        <?php
                    }#END IF


//                    echo'<pre>';print_r( $item );echo'</pre>'.__FILE__.' '.__LINE__;
//                    die(__FILE__ .' '. __LINE__ );


                    ?>
                    <!-- ID поля -->
					<td class="nowrap hidden-phone hidden-tablet center"><?= $item->id ?></td>

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


