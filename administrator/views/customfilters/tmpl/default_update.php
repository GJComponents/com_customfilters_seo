<?php
/**
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
 * @since		1.0
 */

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$input=JFactory::getApplication()->input;
if($input->get('tmpl','','cmd')!='component' && $input->get('format','','cmd')!='json') :?>
<div id="cf_info">
	<a target="_blank" href="https://breakdesigns.net/extensions/joomla/custom-filters">Custom Filters</a>
	<div id="cf_versioninfo"></div>
	<div id="cfupdate_toolbar">
		<?php
		if(!empty($this->update_id))$task='update.update';
			else $task='';
			?>
			<form action="index.php?option=com_installer&amp;view=update"
				method="post">
				<input type="hidden" name="task" value="<?php echo $task?>" /> <input
					type="hidden" name="cid[]" value="<?php echo $this->update_id ?>" />
					<?php echo JHtml::_( 'form.token' ); ?>
				<input type="submit"
					value="<?php echo Text::_('JLIB_INSTALLER_UPDATE'); ?>"
					class="btn pb_update_btn" />
			</form>

		<div style="clear: both"></div>
	</div>
	<div id="cf_copyright_footer"> Copyright &copy; <a target="_blank" href="https://breakdesigns.net">breakdesigns.net</a></div>
</div>
<div id="cf_jed"><?php echo Text::_('COM_CUSTOMFILTERS_JED');?></div>
<?php endif;
