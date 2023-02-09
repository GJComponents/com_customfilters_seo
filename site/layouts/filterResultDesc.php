<?php


/***********************************************************************************************************************
 *  ///////////////////////////╭━━━╮╱╱╱╱╱╱╱╱╭╮╱╱╱╱╱╱╱╱╱╱╱╱╱╭━━━╮╱╱╱╱╱╱╱╱╱╱╱╱╭╮////////////////////////////////////////
 *  ///////////////////////////┃╭━╮┃╱╱╱╱╱╱╱╭╯╰╮╱╱╱╱╱╱╱╱╱╱╱╱╰╮╭╮┃╱╱╱╱╱╱╱╱╱╱╱╱┃┃////////////////////////////////////////
 *  ///////////////////////////┃┃╱╰╯╭━━╮╭━╮╰╮╭╯╭━━╮╭━━╮╱╱╱╱╱┃┃┃┃╭━━╮╭╮╭╮╭━━╮┃┃╱╭━━╮╭━━╮╭━━╮╭━╮////////////////////////
 *  ///////////////////////////┃┃╭━╮┃╭╮┃┃╭╯╱┃┃╱┃┃━┫┃━━┫╭━━╮╱┃┃┃┃┃┃━┫┃╰╯┃┃┃━┫┃┃╱┃╭╮┃┃╭╮┃┃┃━┫┃╭╯////////////////////////
 *  ///////////////////////////┃╰┻━┃┃╭╮┃┃┃╱╱┃╰╮┃┃━┫┣━━┃╰━━╯╭╯╰╯┃┃┃━┫╰╮╭╯┃┃━┫┃╰╮┃╰╯┃┃╰╯┃┃┃━┫┃┃/////////////////////////
 *  ///////////////////////////╰━━━╯╰╯╰╯╰╯╱╱╰━╯╰━━╯╰━━╯╱╱╱╱╰━━━╯╰━━╯╱╰╯╱╰━━╯╰━╯╰━━╯┃╭━╯╰━━╯╰╯/////////////////////////
 *  ///////////////////////////╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱┃┃//  (C) 2023  ///////////////////
 *  ///////////////////////////╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╰╯/////////////////////////////////
 *----------------------------------------------------------------------------------------------------------------------
 * @author     Gartes | sad.net79@gmail.com | Telegram : @gartes
 * @date       12.01.23 00:11
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2023 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
/**
 *
 * @since 3.9
 * @copyright
 * @license
 */
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @var string $filter_list - Перечисление фильтров и выбранных опций
 * @var string $filter_value_list - Перечисление выбранных опций
 * @var string $category_name - Название категории
 * @var string $range_price - Диапазон цен "от xx до xx"
 * @var string $count_product - Количество найденных товаров
 * @var array $manufacturers - Массив с данными производителей для найденных товаров
 * @var float $min_price - Минимальная цена
 * @var float $max_price - Максимальная цена
 * @var int $count_product_int - Целое число найденных товаров
 * @var string $category_description - Описание категорий
 * @var string $sef_filter_vm_cat_description - Текст описание категорий из таблицы - "Ссылки фильтра"
 */
extract( $displayData );


//Текст описание категорий из таблицы - "Ссылки фильтра"

echo $sef_filter_vm_cat_description ;

?>
<hr />
<?php
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// Если нет описание к URL фильтра - то выводим сгенерированное описание
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

if ( empty( $sef_filter_vm_cat_description ) )
{
	//$titles = array(' %d производителя', ' %d производителей', ' %d производителей');
	$titles = array(' производителя', ' производителей', ' производителей');
	$countManufacturersText = \GNZ11\Document\Text::declOfNum ( count( $manufacturers ) , $titles );

	$listManufacturersName = [] ;
	foreach ( $manufacturers as $manufacturer )
	{
		$listManufacturersName[] = $manufacturer->mf_name ;
	}#END FOREACH


	?>
    <div class="result_filter">
        <div class="filter_list">
            В категории <?=$category_name?>
			<?php
			if ( isset( $filter_list ) ) echo ', разделе '.$filter_list;  #END IF
			if ( isset( $count_product ) ) echo ', найдено '.$count_product;  #END IF
			?>


			<?php
			// Если у товаров есть производители
			if ( count( $listManufacturersName ) )
			{
				?>
                , производителей: <?=implode( ', ' , $listManufacturersName )?>
				<?php
			}#END IF
			?>

        </div>
        <div class="range_price">
			<?php
			if ( $min_price != $max_price )
			{
				?>
                В ценовом диапазоне: <?=$range_price?>
				<?php
			}
			else
			{
				$titles   = array( ' товара' , ' товаров' , ' товаров' );
				$_prodTxt = \GNZ11\Document\Text::declOfNum( $count_product_int , $titles );
				?>
                Цена <?=$_prodTxt?> <?=$max_price?>
				<?php
			}#END IF
			?>

        </div>
    </div>
	<?php
}#END IF


