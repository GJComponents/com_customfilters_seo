<?php
/**
 * Вкладка с городами
 * @license
 * @copyright
 * @since 3.9
 */
// no direct access
defined('_JEXEC') or die;

use FiltersSeoNamespace\Cms\Html\Behavior;


$parentAlias = \Joomla\CMS\Factory::getApplication()->input->get('parentAlias' , false , 'STRING');
$parentName = \Joomla\CMS\Factory::getApplication()->input->get('parentName' , false , 'STRING');


$AccordionSelector = 'city_controls' . (!$parentAlias?:'-'.$parentAlias) ;

?>

<?php echo Behavior::startAccordion( $AccordionSelector , array('active' => 'warning0')); ?>
<?php

foreach ($this->ListCity as $item)
{
	if ( $parentAlias ) $item['parentAlias'] = $parentAlias ; #END IF
	if ( $parentName ) $item['parentName'] = $parentName ; #END IF
	$alias = $item['alias'];






	/**
	 * @var CustomfiltersModelSetting_city $ModelSetting_city
	 */
	$ModelSetting_city = $this->_models['setting_city'];

	$ModelSetting_city->getChildrenArea( $this->item->params['use_city_setting'] , $alias );
	$area = $ModelSetting_city->ChildrenAreaData;

//	echo'<pre>';print_r( $ModelSetting_city->ChildrenAreaData );echo'</pre>'.__FILE__.' '.__LINE__;
//	echo'<pre>';print_r( $alias );echo'</pre>'.__FILE__.' '.__LINE__;
//    echo'<pre>';print_r(  $area );echo'</pre>'.__FILE__.' '.__LINE__;
//    echo'<pre>';print_r(  $this->item->params['use_city_setting'] );echo'</pre>'.__FILE__.' '.__LINE__;
//    die(__FILE__ .' '. __LINE__ );



	// Сбросить - количество активных дочерних регионов
    $ModelSetting_city->ActiveChildArea = 0;
    // Почитать - количество активных дочерних регионов
	$ModelSetting_city->getActiveChildArea($area);
	$item['ActiveChildArea'] = $ModelSetting_city->ActiveChildArea ;



    ?>

	<?= Behavior::addSlideCitySlider( $AccordionSelector , $item , $this->paramsCityList   )   ?>

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

