<?php

/**
 * Заполнение - создание текста для замены Short Code
 * - {{FILTER_LIST}} - Название фильтра и перечисление значений :
 *      -> Вид поверхности: Глянцевые, Цвет: 5005 Синий насыщенный и 9006 Белый алюминий, Тип покрытия: Colorcoat Prisma®
 * - {{FILTER_VALUE_LIST}} Только значения фильтров через запятую :
 *      -> Глянцевые, 5005 Синий насыщенный, 9006 Белый алюминий, Colorcoat Prisma®
 *
 * @since    1.0.0
 * @license i
 * @copyright
 */
class seoTools_shortCode
{
    /**
     * {{FILTER_LIST}} - Название фильтра и перечисление значений
     * @param $filterOrdering
     * @return string
     * @since    1.0.0
     */
    public static function getFilterListText($filterOrdering):string
    {
        $FILTER_LIST_text = '';
        // Перебираем фильтры -- составляем описание для включенных фильтров
        $filterCount = 0 ;
        foreach ($filterOrdering as $filter ){
            if ($filterCount)  $FILTER_LIST_text .= ', ';
            $FILTER_LIST_text .= $filter->custom_title . ': ' ;

            $valueCount = 0;
            foreach ( $filter->valueArr as $item)
            {
                if ( $valueCount ) $FILTER_LIST_text .= ' и ';
                $FILTER_LIST_text .= $item ;
                $valueCount++;
            }
            $filterCount++;
        }

        return \Joomla\CMS\Language\Text::_( $FILTER_LIST_text );
    }

    /**
     * {{FILTER_VALUE_LIST}} Значение фильтров через запятую
     * @param $filterOrdering
     * @return string
     * @since    1.0.0
     */
    public static function getFilterValueListText($filterOrdering){
        $FILTER_LIST_text = '';
        $filterCount = 0 ;
        foreach ($filterOrdering as $iOr => $filter ){
            $valueCount = 0;
            foreach ( $filter->valueArr as $item)
            {
                if ( $valueCount ) $FILTER_LIST_text .= ', ';
                $FILTER_LIST_text .= $item ;
                $valueCount++;
            }
            $filterCount++;
            if ($filterCount < count( $filterOrdering ) ) $FILTER_LIST_text .= ', ';
        }
        return $FILTER_LIST_text ;
    }

	public static function getResultFilterCountProduct($dataArr):string
	{
		$titles = array(' %d товар', ' %d товара', ' %d товаров');
		return  \GNZ11\Document\Text::declOfNum ( $dataArr['count_Product'] , $titles );

	}

	/**
	 * Создает строку описания результатов поиска для диапазона цен.
	 * etc/   [{{RANGE_PRICE}}] => от 7667 грн. до 39490 грн.
	 * @param $dataArr
	 *
	 * @return string
	 * @since 3.9
	 */
	public static function getResultFilterRangePrice($dataArr){
		$min_Price = round( $dataArr['min_Price'] )  ;
		$max_Price = round( $dataArr['max_Price'] )  ;
		//create a currency object which will be used later
		if (!class_exists('CurrencyDisplay')) {
			require_once(JPATH_VM_ADMIN . '/helpers/currencydisplay.php');
		}


		$vmCurrencyHelper = CurrencyDisplay::getInstance();
		$min_Price = $vmCurrencyHelper->priceDisplay( $min_Price ) ;
		$max_Price = $vmCurrencyHelper->priceDisplay( $max_Price ) ;

		return \Joomla\CMS\Language\Text::sprintf( 'COM_FILTER_PRICE_RANGE' , $min_Price , $max_Price  ) ;

	}
	public static function getResultFilterPrice($price){
		if (!class_exists('CurrencyDisplay')) {
			require_once(JPATH_VM_ADMIN . '/helpers/currencydisplay.php');
		}
		$vmCurrencyHelper = CurrencyDisplay::getInstance();
		return $vmCurrencyHelper->priceDisplay( $price ) ;
	}



	public static function getResultDescription($dataArr){

		return  $dataArr ;
	}

}