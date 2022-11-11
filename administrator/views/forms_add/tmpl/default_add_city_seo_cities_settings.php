<?php
// no direct access
defined('_JEXEC') or die;

use FiltersSeoNamespace\Cms\Html\Behavior;


$parentAlias = \Joomla\CMS\Factory::getApplication()->input->get('parentAlias' , false , 'STRING');
$parentName = \Joomla\CMS\Factory::getApplication()->input->get('parentName' , false , 'STRING');


$AccordionSelector = 'city_controls' . (!$parentAlias?:'-'.$parentAlias)


?>

<?php echo Behavior::startAccordion( $AccordionSelector , array('active' => 'warning0')); ?>
<?php
foreach ($this->ListCity as $item)
{
	if ( $parentAlias ) $item['parentAlias'] = $parentAlias ; #END IF
	if ( $parentName ) $item['parentName'] = $parentName ; #END IF


	?>

	<?= Behavior::addSlideCitySlider( $AccordionSelector , $item    )   ?>

		<p>CONTENT - ONE SLIDER </p>
	<?= Behavior::endSlide(); ?>

	<?php
}#END FOREACH
?>

<?= Behavior::endAccordion(); ?>
	<!-- --------------------------------------------------------------- -->


	<!-- --------------------------------------------------------------- -->



	<!-- --------------------------------------------------------------- -->
<?php

//echo '<pre>'; print_r($this->ListCity[0]); echo '</pre>' . __FILE__ . ' ' . __LINE__;
	
