<?php
namespace FiltersSeoNamespace\Cms\Html ;

use Joomla\CMS\Layout\LayoutHelper;

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
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static $loaded = array();

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
	public static function addSlideCitySlider(string $selector, array $item/* , $use_city_setting */): string
	{
		$text = $item['name'] ;
		$id = $item['alias'] ;
		$class = $item['alias'] ;
		$alias = $item['alias'] ;
		// Количество Активных регионов
		$ActiveChildArea = $item['ActiveChildArea'];


		
//		$item['use'] = $item[$alias] == 'NOT'? 0 : $use_city_setting[$alias] ;

//		echo'<pre>';print_r( $item );echo'</pre>'.__FILE__.' '.__LINE__;
//		die(__FILE__ .' '. __LINE__ );

		
//		$in = ( static::$loaded[__CLASS__ . '::startAccordion'][$selector]['active'] == $id) ? ' in' : '';
		$in = ( static::$loaded[ 'JHtmlBootstrap' . '::startAccordion'][$selector]['active'] == $id) ? ' in' : '';

//		$collapsed = (static::$loaded[__CLASS__ . '::startAccordion'][$selector]['active'] == $id) ? '' : ' collapsed';
		$collapsed = (static::$loaded[ 'JHtmlBootstrap' . '::startAccordion'][$selector]['active'] == $id) ? '' : ' collapsed';

//		$parent = static::$loaded[__CLASS__ . '::startAccordion'][$selector]['parent'] ?
//			' data-parent="' . static::$loaded[__CLASS__ . '::startAccordion'][$selector]['parent'] . '"' : '';
		$parent = static::$loaded[ 'JHtmlBootstrap' . '::startAccordion'][$selector]['parent'] ?
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

				$html .='<div class="statistic-area" style="pointer-events: none;">'
							.'Активных регионов: ' . $ActiveChildArea
						.'</div>' ;

			$html .= '</a>' ;
			/*$html .= '<div class="btn-wrapper add-area" id="toolbar-plus-2">
						<button title="Удалить регион"  class="btn btn-small button-delete">
							<span class="icon-delete" aria-hidden="true"></span>
				 		</button> 
					</div>' ;*/

		$html.= '</strong>' ;

			$html.= '<input type="hidden" class="city_setting_city_id" name="jform[city_setting_city_id][]" value="'.$item['id'].'" disabled="disabled" />' ;
			$html.= '<input type="hidden" class="city_setting_city_alias" name="jform[alias-city][]" value="'.$item['alias'].'" disabled="disabled" />' ;

			$html.='<div class="line-toolbar" >' ;
				$html.= self::getRadioOnOffCity($item) ;
				$html.=  '
					
					';
			$html.= '</div>';

		$html.= '</div>'
			. '<div class="accordion-body collapse' . $in . '" id="' . $id . '">'
			. '<div class="accordion-inner">';

		return $html;
	}

	/**
	 * Блок настроек Meta для региона
	 * @param   array  $item
	 *
	 * @return string
	 * @since 3.9
	 */
	public static function getMetaFormElementHtml (array $item):string
	{
		$layout    = new \JLayoutFile( 'form-meta-element' ,JPATH_ADMINISTRATOR . '/components/com_customfilters/layouts' );





		$name = 'jform[params][use_city_setting]['.$item['alias'].'][use]';
		$parentAlias = '';
		if ( isset ($item['parentName']) )
		{
			$item['parentName'] = str_replace('[use]' , '' , $item['parentName'] );
			$name =  $item['parentName'].'['.$item['alias'].'][use]' ;
			
			$parentAlias = $item['parentAlias'];

		}#END IF

		$displayData = [
			'cityItem' => $item ,
			'alias' => $item['alias'],
			'name' => $name ,
			'parentAlias' => $parentAlias
		];
		if ( !isset($displayData[ 'cityItem' ][ 'params' ] )  )
		{
			// echo '<pre>'; print_r( $displayData  );echo '</pre>'.__FILE__.' '.__LINE__;

			/*try
			{
			    // Code that may throw an Exception or Error.

			     throw new \Exception('Code Exception '.__FILE__.':'.__LINE__) ;
			}
			catch (\Exception $e)
			{
			    // Executed only in PHP 5, will not be reached in PHP 7
			    echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
			    echo'<pre>';print_r( $e );echo'</pre>'.__FILE__.' '.__LINE__;
			    die(__FILE__ .' '. __LINE__ );
			}


			die( __FILE__.' '.__LINE__ );*/
		}#END IF

		# Расположение слоя
		# administrator/layouts/form-meta-element.php
		return LayoutHelper::render('form-meta-element', $displayData);
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

		$html = '<div class="control-group">' ;

		$html .=    '<div class="control-label">'
						.'<label id="jform_use_virtuemart_pages_vars-lbl" for="jform_use_virtuemart_pages_vars" 
								class="hasPopover" 
								title="Использовать" 
								data-content="Если включено будут использоваться все вложенные регионы.<br> Если отключено - нужно включать вложенные регионы" 
								data-original-title="Использовать">
								Использовать
						</label>'
					.'</div>' ;

		$html .= '<div class="controls">' ;
			$html .='<fieldset id="jform_use_virtuemart_pages_vars-'.$item['alias'].'" 
				class="btn-group btn-group-yesno radio">';


		if ( !isset($item['params']) )
		{
			$item['params'] = [] ;
			$item['params']['use'] = 0 ;
		}#END IF



		$html .='<input type="radio" 
					data-evt="changeCityPublished"
					data-parent-alias="'.$parentAlias.'"
					id="jform_use_virtuemart_pages_vars0-'.$item['alias'].'" 
					name="'.$name.'"
					value="1"';
					if ($item['params']['use']) $html .= 'checked="checked"'; #END IF

					$html .= '>' ;

		$html .='<label for="jform_use_virtuemart_pages_vars0-'.$item['alias'].'" ';
					$html .=' class="btn ' ;
						$html .= ($item['params']['use']?' active btn-success ': '') .'"';
							$html .='>';
								$html .= 'ON' ;
								$html .= '</label>';

				$checked = !$item['params']['use']?' checked="checked" ' : '' ;
				$html .= '<input type="radio" 
					data-evt="changeCityPublished"
					data-parent-alias="'.$parentAlias.'"
					id="jform_use_virtuemart_pages_vars1-'.$item['alias'].'" 
					name="'.$name.'"
					value="0" '
					.$checked
					.'>';
		                    $class = !$item['params']['use']?' active btn-danger ' : '' ;
							$html .= '<label for="jform_use_virtuemart_pages_vars1-'.$item['alias'].'" class="btn '.$class.'">
					OFF
				</label>'
			.'</fieldset>'
			.'</div>'
		.'</div>';

		return $html ; 
	}
}