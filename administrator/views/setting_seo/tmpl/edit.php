<?php
/**
 * @package    vm_seo_product_filter_grt
 *
 * @author     Максим <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var Vm_seo_product_filter_grtViewVm_seo_product_filter_grt $this */

HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen');



Factory::getDocument()->addScriptDeclaration(<<<JS
        
		Joomla.submitbutton = function(task)
		{
			if (task === 'vm_seo_product_filter_grt.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
			{
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
		};
        
JS
);




?>
<form action="<?php echo Route::_('index.php?option=com_customfilters&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm"
      enctype="multipart/form-data" id="adminForm" class="form-validate">

	<?php

//    echo LayoutHelper::render('joomla.edit.title_alias', $this);


    ?>

    <?php  echo $this->form->renderField('vmcategory_id'); ?>

    <?php  echo $this->form->renderField('sef_url'); ?>

    <?php  echo $this->form->renderField('url_params'); ?>

    <?php  echo $this->form->renderField('selected_filters_table'); ?>



    <?php  echo $this->form->renderField('sef_filter_h_tag'); ?>


    <?php  echo $this->form->renderField('note_vm_cat_description'); ?>
    <?php  echo $this->form->renderField('sef_filter_vm_cat_description'); ?>


    <?php  echo $this->form->renderField('sef_filter_title'); ?>

    <?php  echo $this->form->renderField('sef_filter_description'); ?>

    <?php  echo $this->form->renderField('sef_filter_keywords'); ?>


	<hr/>

	<div class="row-fluid">

        <div class="span9">
<!--			--><?php  echo $this->form->getInput('description'); ?>
		</div>
		<div class="span3">
			<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
		</div>
	</div>

	<input type="hidden" name="task" value=""/>
	<?php echo $this->form->getInput('id'); ?>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
<script src="\administrator\components\com_customfilters\assets\js\selected_filters_table.js" async defer ></script>