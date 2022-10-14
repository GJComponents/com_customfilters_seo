# com_customfilters_seo
Фильтр товаров Virtuemart + SEF ссылки

INSTALL : <br>
[Library GNZ11.zip](https://github.com/gartes/GNZ11/archive/refs/heads/master.zip)<br>
[Component - Filter-Seo.zip](https://github.com/GJComponents/com_customfilters_seo/archive/refs/heads/main.zip)<br>
[Module - mod_cf_filtering.zip](https://github.com/GJModules/mod_cf_filtering/archive/refs/heads/main.zip)<br>

Модуль для вывода фильтров https://github.com/GJModules/mod_cf_filtering
<br><br><br>

 


#### Установка meta-tag canonical
-- **!!! Удалить этот код из файла /templates/[YOUR-TEMPLATE]/index.php**

-- он больше не нужен !!!

~~Установить в начало файла /templates/[YOUR-TEMPLATE]/index.php~~ 
```php
/**
 * START - Изменение canonical для результатов фильтрации --  
 */
$app = \Joomla\CMS\Factory::getApplication();
$doc = \Joomla\CMS\Factory::getDocument();
$option = $app->input->get('option' , false );

if ( $option == 'com_customfilters'){
    foreach ($doc->_links as $url => $link)
    {
        if ($link['relation'] == 'canonical')   unset( $doc->_links[$url] ); #END IF

    }#END FOREACH
    $docBase  = preg_replace('/start=\d+\/$/', '', $doc->base );
    $canUrl = '<link href="' . $docBase . '" rel="canonical" />';
    $doc->addCustomTag($canUrl);
}
/**
 * END - Изменение canonical для результатов фильтрации 
 */
```
<hr>

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