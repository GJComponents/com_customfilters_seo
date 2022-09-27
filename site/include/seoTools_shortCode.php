<?php

/**
 * Заполнениен - создание текста для замены Short Code
 * - {{FILTER_LIST}} - Название фильтра и перечесление значений :
 *      -> Вид поверхности: Глянцевые, Цвет: 5005 Синий насыщенный и 9006 Белый алюминий, Тип покрытия: Colorcoat Prisma®
 * - {{FILTER_VALUE_LIST}} Значение фильтров через запятую :
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
    public static function getFilterListText($filterOrdering){
        $FILTER_LIST_text = '';
        // Перебираем фильтры -- составляем описание для вкл. фильтров
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
        return $FILTER_LIST_text ;
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

}