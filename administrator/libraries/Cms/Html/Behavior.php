<?php
namespace FiltersSeoNamespace\Cms\Html ;

/**
 * Переопределение класса JHtmlBootstrap - для создания аккордеона для городов
 * USE :
 *      JLoader::registerNamespace( 'FiltersSeoNamespace' , JPATH_ADMINISTRATOR . '/components/com_customfilters/libraries' , $reset = false , $prepend = false , $type = 'psr4' );
 *      use FiltersSeoNamespace\Cms\Html\Behavior;
 *
 * @since 3.9
 */
class Behavior extends \JHtmlBootstrap
{
	/**
	 * Begins the display of a new accordion slide.
	 *
	 * @param   string  $selector  Identifier of the accordion group.
	 * @param   array   $item      Text to display.
	 * @param           $use_city_setting
	 *
	 * @return  string  HTML to add the slide
	 *
	 * @since   3.0
	 */
	public static function addSlideCitySlider(string $selector, array $item , $use_city_setting ): string
	{
		$text = $item['name'] ;
		$id = $item['alias'] ;
		$class = $item['alias'] ;
		$alias = $item['alias'] ;
		// Количество Активных регионов
		$ActiveChildArea = $item['ActiveChildArea'];


		
		$item['use'] = $use_city_setting[$alias] == 'NOT'? 0 : $use_city_setting[$alias] ;

		$in = (static::$loaded[__CLASS__ . '::startAccordion'][$selector]['active'] == $id) ? ' in' : '';
		$collapsed = (static::$loaded[__CLASS__ . '::startAccordion'][$selector]['active'] == $id) ? '' : ' collapsed';
		$parent = static::$loaded[__CLASS__ . '::startAccordion'][$selector]['parent'] ?
			' data-parent="' . static::$loaded[__CLASS__ . '::startAccordion'][$selector]['parent'] . '"' : '';
		$class = (!empty($class)) ? ' ' . $class : '';

		$html = '<div class="accordion-group' . $class . '">' ;
			$html .= '<div class="accordion-heading">' ;
		$html .= '<strong  >' ;
			$html .= '<a href="#' . $id . '" 
						data-evt="loadChildrenArea" data-toggle="collapse"' . $parent . ' class="accordion-toggle' . $collapsed . '">' ;
				$html .= '<strong style="pointer-events: none;">' ;
					$html.= $text ;
				$html.= '</strong>' ;

				$html .='<span class="statistic-area" style="pointer-events: none;">'
							.'Активных регионов: ' . $ActiveChildArea
						.'</span>' ;

			$html .= '</a>' ;

		$html.= '</strong>' ;

			$html.= '<input type="hidden" class="city_setting_city_id" name="jform[city_setting_city_id][]" value="'.$item['id'].'" disabled="disabled" />' ;
			$html.= '<input type="hidden" class="city_setting_city_alias" name="jform[alias-city][]" value="'.$item['alias'].'" disabled="disabled" />' ;


			$html.= self::getRadioOnOffCity($item)
			. '</div>'
			. '<div class="accordion-body collapse' . $in . '" id="' . $id . '">'
			. '<div class="accordion-inner">';

		return $html;
	}

	/**
	 * Создание для слайда аккордеона городов кнопок использовать или нет
	 * @param   array  $item
	 * @return string
	 * @since 3.9
	 */
	protected static function getRadioOnOffCity(array $item){

		$name = 'jform[params][use_city_setting]['.$item['alias'].'][use]';
		$parentAlias = '';
		if ( isset ($item['parentName']) )
		{
			$item['parentName'] = str_replace('[use]' , '' , $item['parentName'] );
			$name =  $item['parentName'].'['.$item['alias'].'][use]' ;

			$parentAlias = $item['parentAlias'];

		}#END IF

		


		$html = '<div class="control-group">'
			.'<div class="control-label">'
				.'<label id="jform_use_virtuemart_pages_vars-lbl" for="jform_use_virtuemart_pages_vars" 
						class="hasPopover" 
						title="Использовать" 
						data-content="Если включено будут использоваться все вложенные регионы.<br> Если отключено - нужно включать вложенные регионы" 
						data-original-title="Использовать">
						Использовать
				</label>'
			.'</div>' ;

		$html .= '<div class="controls">'
			.'<fieldset id="jform_use_virtuemart_pages_vars-'.$item['alias'].'" 
				class="btn-group btn-group-yesno radio">';


		$html .='<input type="radio" 
					data-evt="changeCityPublished"
					data-parent-alias="'.$parentAlias.'"
					id="jform_use_virtuemart_pages_vars0-'.$item['alias'].'" 
					name="'.$name.'"
					value="1"';
					if ($item['use']) $html .= 'checked="checked"'; #END IF

					$html .= '>'

				.'<label for="jform_use_virtuemart_pages_vars0-'.$item['alias'].'" ';
					$html .=' class="btn ' ;
						$html .= ($item['use']?' active btn-success ': '') .'"';
							$html .='>'.'ON
				</label>';
				$checked = !$item['use']?' checked="checked" ' : '' ;
				$html .= '<input type="radio" 
					data-evt="changeCityPublished"
					data-parent-alias="'.$parentAlias.'"
					id="jform_use_virtuemart_pages_vars1-'.$item['alias'].'" 
					name="'.$name.'"
					value="0" '
					.$checked
					.'>';
		                    $class = !$item['use']?' active btn-danger ' : '' ;
							$html .= '<label for="jform_use_virtuemart_pages_vars1-'.$item['alias'].'" class="btn '.$class.'">
					OFF
				</label>'
			.'</fieldset>'
			.'</div>'
		.'</div>';

		return $html ; 
	}
}