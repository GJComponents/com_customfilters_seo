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

$doc = \Joomla\CMS\Factory::getDocument();
$parentAlias = \Joomla\CMS\Factory::getApplication()->input->get('parentAlias' , false , 'STRING');
$parentName = \Joomla\CMS\Factory::getApplication()->input->get('parentName' , false , 'STRING');


$AccordionSelector = 'city_controls' . (!$parentAlias?:'-'.$parentAlias) ;

?>

<?php echo Behavior::startAccordion( $AccordionSelector , array('active' => 'warning0')); ?>
<?php

$aliasArr = [
	'kiev',
];


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


	// Сбросить - количество активных дочерних регионов
    $ModelSetting_city->ActiveChildArea = 0;
    // Почитать - количество активных дочерних регионов
	$ModelSetting_city->getActiveChildArea($area);
	$item['ActiveChildArea'] = $ModelSetting_city->ActiveChildArea ;


	if ( isset( $this->paramsCityList[$alias] ) )
	{
		$item['params'] = $this->paramsCityList[$alias] ;
	}#END IF


//    echo'<pre>';print_r( $item );echo'</pre>'.__FILE__.' '.__LINE__;
//    echo'<pre>';print_r( $this->paramsCityList );echo'</pre>'.__FILE__.' '.__LINE__;
//    echo'<pre>';print_r( $alias );echo'</pre>'.__FILE__.' '.__LINE__;
//    die(__FILE__ .' '. __LINE__ );


    ?>


	<?= Behavior::addSlideCitySlider( $AccordionSelector , $item /*, $this->paramsCityList */  )   ?>
    
    <?php



	if ( $doc->_type == 'json' && !in_array( $item['alias'] , $aliasArr ) )
	{
//		echo'<pre>';print_r( $item );echo'</pre>'.__FILE__.' '.__LINE__;
//        die(__FILE__ .' '. __LINE__ );

	}#END IF


    ?>
    
    <?= Behavior::getMetaFormElementHtml( $item )?>

		<p>CONTENT - ONE SLIDER !!!</p>
	<?= Behavior::endSlide(); ?>

	<?php
}#END FOREACH


?>

<?= Behavior::endAccordion(); ?>
	<!-- --------------------------------------------------------------- -->


	<!-- --------------------------------------------------------------- -->



	<!-- --------------------------------------------------------------- -->
<?php

