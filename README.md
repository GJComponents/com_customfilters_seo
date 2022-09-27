# com_customfilters_seo
Фильтр товаров Virtuemart + SEF ссылки

Модуль для вывода фильтров https://github.com/GJModules/mod_cf_filtering
<br><br><br>




#### Установка тега `<h1 />` с параметрами фильтрации 
Перед выводом текста тега `<h1 />` вставить код PHP <br>
* {{CATEGORY_NAME}} - Названия категории товаров в которой работает фильтрация
* {{FILTER_LIST}} - Название фильтра и перечисление значений :<br>
  *-> Вид поверхности: Глянцевые, Цвет: 5005 Синий насыщенный и 9006 Белый алюминий, Тип покрытия: Colorcoat Prisma®*
* {{FILTER_VALUE_LIST}} Только значения фильтров через запятую : <br>
    *-> Глянцевые, 5005 Синий насыщенный, 9006 Белый алюминий, Colorcoat Prisma®*
```php
/**
 * Установка значения для тега <h1 />.
 * Настраивается в настройках компонента фильтра
 */
$app = \Joomla\CMS\Factory::getApplication();
$tag_h1 = $app->get('filter_data_h1' , false );
if ( $tag_h1 && !empty($this->category->category_name) )
{
    $this->category->category_name = $tag_h1 ;
}#END IF
```