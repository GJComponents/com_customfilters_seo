<?php
/**
 * @package     Joomla\Component\Customfilters\Site\Helpers
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Joomla\Component\Customfilters\Site\Helpers;

use Exception;
use stdClass;

class CfHelperUri
{
	/**
	 * @throws Exception
	 * @since __BUMP_VERSION__
	 */
	public static function parseCheckFiltersToPatch($path , &$vars){
		/**
		 * @var array $published_cf - Все опубликованные фильтры
		 */
		$published_cf = CfHelperFilters::getCustomFilters('');

		/**
		 * @var array $findResultArr - массив выбранных
		 */
		$findResultArr = [];
		/**
		 * @var array $filtersArr - массив фильтров у которых есть выбранные опции
		 */
		$filtersArr    = [];

		// Перебираем опубликованные фильтры - Алиасы находим фильтры названий фильтра
		foreach ($published_cf as $item)
		{
			// Поиск вхождения первого фильтра
			$needle =   $item->sef_url . '-'   ;
			$needleRegExp = '/' . preg_quote($needle ) . '/u';
			preg_match( $needleRegExp , $path , $matches , PREG_OFFSET_CAPTURE ) ;

			if ( isset( $matches[0] ) )
			{
				$pos    = strripos($path, $item->sef_url );
				$findResultArr[$pos] = $item->sef_url ;
				$filtersArr[]        = $item;
			}#END IF

		}#END FOREACH
		// Если не нашли название фильтров в URL
		if (empty($findResultArr)) return false ; #END IF

		// Сортируем массив Alias названий фильтров по ключу в порядке убывания
		// - для того что бы разбирать URL с конца строки
		// Ключ в массиве - это номер символа после которого начинается Alias фильтра
		krsort($findResultArr);

		$length     = 0;
		$i          = 0;

		$dataFiltersArr = [];

		if ( !isset( $findResultArr['type'] ) || $findResultArr['type'] != 'city' )
		{
			// Перебираем массив с Названиями (Alias) фильтров [ 77 => 'cvet' , 25 => 'vid_poverhnosti' ]
			foreach ($findResultArr as $start => $item)
			{
				$dataFilters        = new stdClass();
				$dataFilters->name  = str_replace(['/', '-and-'], '', $item);
				$dataFilters->value = [];
				// Если это первый фильтр с конца
				if (!$i) $length = null; #END IF

				$i++;

				// Получаем строку от символа в позиции $start  до символа $length
				$subStr = mb_substr( $path, $start , $length );
				// Находим двойные или более опции фильтра
				$arrValFilter = explode('-and-', $subStr);
				// Удаляем пустые ключи в массиве -- Если выбранная только одна опция фильтра
				$arrValFilter = array_diff($arrValFilter, array(''));




				foreach ( $arrValFilter as $itemValF )
				{
					// Удалить слэши
					$itemValF = str_replace('/', '', $itemValF);

					// Удаляем сам Alias фильтра
					$itemValF = str_replace($dataFilters->name, '', $itemValF);
					//После удаления Alias фильтра остается "-" в начале строки - и ее тоже удаляем
					$itemValF = preg_replace('/^-/' , '' , $itemValF ) ;

					$dataFilters->value[] = $itemValF;

				}#END FOREACH

				$path         = str_replace($subStr, '', $path);
				$length = $start;
				$dataFiltersArr[] = $dataFilters;
			}#END FOREACH
		}#END IF


		$selectFilterIds = [];

		// Добавить выбранные опции к объекту фильтра
		foreach ($filtersArr as &$filter)
		{
			$filter->optionSelected = [];
			$filter->dataOptions = [];
			foreach ($dataFiltersArr as $item)
			{
				if ($item->name == $filter->sef_url)
				{
					$filter->optionSelected = $item->value;
					$selectFilterIds[]      = $filter->custom_id;

				}#END IF
			}#END FOREACH
		}#END FOREACH

		/**
		 * @var array $customSelectValueArr - Массив всех значений для фильтров
		 */
		$customSelectValueArr = CfHelperFilters::getCustomSelectValue($selectFilterIds , $vars['virtuemart_category_id'] );

		// Определение Value для выбранных опций
		foreach ($filtersArr as &$item)
		{
			$key         = 'custom_f_' . $item->custom_id;
			$optArr      = [];
			$arrSetInput = [];

			if ( isset( $item->optionSelected ) && is_array($item->optionSelected) || is_object($item->optionSelected))
			{
				foreach ($item->optionSelected as $option)
				{
					if (array_key_exists( $option , $customSelectValueArr ))
					{
						$item->dataOptions[] =  $customSelectValueArr[$option];
						$customfield_value = $customSelectValueArr[$option]->customfield_value;

						/**
						 * Pattern : пропускаем =>
						 * буквы, пробелы, числа, скобки()[], точки и запятые, тире, ®, *
						 *  - Пока нельзя использовать в customfield_value - кавычки «"»
						 *
						 */
						// Регулярное выражение для очистки customfield_value '/[^\w\s\d\(\)\[\]\.,-®*]/iu'
						$customfield_value = preg_replace('/[^\w\s\d\(\)\[\]\.,-®*]/iu' , '' , $customfield_value ) ;


						// Преобразование двоичных данных в шестнадцатеричное представление
						$optArr[]          = bin2hex($customfield_value);

					}#END IF
				}#END FOREACH
			}


			$vars[$key] = $optArr ;
//			$app->input->set($key, $optArr);

		}#END FOREACH


//		echo'<pre>';print_r( $customSelectValueArr );echo'</pre>'.__FILE__.' '.__LINE__;
//		echo'<pre>';print_r( $vars );echo'</pre>'.__FILE__.' '.__LINE__;
//		echo'<pre>';print_r( $selectFilterIds );echo'</pre>'.__FILE__.' '.__LINE__;
//		echo'<pre>';print_r( $findResultArr );echo'</pre>'.__FILE__.' '.__LINE__;
//		echo'<pre>';print_r( $filtersArr );echo'</pre>'.__FILE__.' '.__LINE__;


	}
}