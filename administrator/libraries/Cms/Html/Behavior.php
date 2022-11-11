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
	 *
	 * @param   string  $class     Class of the accordion group.
	 *
	 * @return  string  HTML to add the slide
	 *
	 * @since   3.0
	 */
	public static function addSlideCitySlider(string $selector, array $item ): string
	{
		$text = $item['name'] ;
		$id = $item['alias'] ;
		$class = $item['alias'] ;




		$in = (static::$loaded[__CLASS__ . '::startAccordion'][$selector]['active'] == $id) ? ' in' : '';
		$collapsed = (static::$loaded[__CLASS__ . '::startAccordion'][$selector]['active'] == $id) ? '' : ' collapsed';
		$parent = static::$loaded[__CLASS__ . '::startAccordion'][$selector]['parent'] ?
			' data-parent="' . static::$loaded[__CLASS__ . '::startAccordion'][$selector]['parent'] . '"' : '';
		$class = (!empty($class)) ? ' ' . $class : '';

		$html = '<div class="accordion-group' . $class . '">'
			. '<div class="accordion-heading">'
			. '<strong>'
				. '<a href="#' . $id . '" data-evt="loadChildrenArea" data-toggle="collapse"' . $parent . ' class="accordion-toggle' . $collapsed . '">'
					. $text
				. '</a>' ;

			$html.= '<input type="hidden" class="city_setting_city_id" name="jform[city_setting_city_id][]" value="'.$item['id'].'" />' ;
			$html.= '<input type="hidden" class="city_setting_city_alias" name="jform[alias][]" value="'.$item['alias'].'" />' ;

			$html.= '</strong>'
			. self::getRadioOnOffCity($item)
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

		$name = 'jform[use_city_setting]['.$item['alias'].'][use]';
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
						title="Использовать для" 
						data-content="Использовать" 
						data-original-title="Использовать">
						Использовать
				</label>'
			.'</div>'
			.'<div class="controls">'
			.'<fieldset id="jform_use_virtuemart_pages_vars-'.$item['alias'].'" 
				class="btn-group btn-group-yesno radio">'
				.'<input type="radio" 
					data-evt="changeCityPublished"
					data-parent-alias="'.$parentAlias.'"
					id="jform_use_virtuemart_pages_vars0-'.$item['alias'].'" 
					name="'.$name.'"
					value="1" 
					checked="checked">'
				.'<label for="jform_use_virtuemart_pages_vars0-'.$item['alias'].'" class="btn active btn-success">
					ON
				</label>'

				.'<input type="radio" 
					data-evt="changeCityPublished"
					data-parent-alias="'.$parentAlias.'"
					id="jform_use_virtuemart_pages_vars1-'.$item['alias'].'" 
					name="'.$name.'"
					value="0">'
				.'<label for="jform_use_virtuemart_pages_vars1-'.$item['alias'].'" class="btn">
					OFF
				</label>'
			.'</fieldset>'
			.'</div>'
		.'</div>';

		return $html ; 
	}
}