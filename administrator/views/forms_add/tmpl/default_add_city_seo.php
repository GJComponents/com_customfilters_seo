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





?>




<form action="<?php echo Route::_('index.php?option=com_customfilters&view=customfilters'); ?>" method="post" name="adminForm" id="adminForm">
	 <div id="form_head">
         <h1 class="title_form"></h1>
     </div>
	 <div id="form_content">
         <?= $this->form->renderFieldset('basic') ?>
     </div>
    <div id="form_footer">
        form_footer form_footer form_footer form_footer
    </div>
	<input type="hidden" name="option" value="com_customfilters" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

<?php  echo $this->loadTemplate('update'); ?>


