<?php
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

JLoader::registerNamespace( 'GNZ11',JPATH_LIBRARIES.'/GNZ11',$reset=false,$prepend=false,$type='psr4');
JLoader::register('seoTools_uri' , JPATH_ROOT .'/components/com_customfilters/include/seoTools_uri.php');
JLoader::register('seoTools_filters' , JPATH_ROOT .'/components/com_customfilters/include/seoTools_filters.php');
JLoader::register('seoTools_shortCode' , JPATH_ROOT .'/components/com_customfilters/include/seoTools_shortCode.php');

JLoader::register('CfInput' , JPATH_ROOT .'/components/com_customfilters/include/input.php');



class seoTools
{
    /**
     * @var
     * @since    1.0.0
     */
    protected $app ;
    /**
     * @var
     * @since    1.0.0
     */
    protected $db ;
    /**
     * @var JDocument|null
     * @since    1.0.0
     */
    protected $doc;
    /**
     * @var Uri
     * @since    1.0.0
     */
    private $uri;
    /**
     * @var string
     * @since    1.0.0
     */
    protected $settingSeoTable = '#__cf_customfields_setting_seo' ;

    /**
     * Параметры компонента com_customfilters
     * @var \Joomla\Registry\Registry
     * @since    1.0.0
     */
    protected $paramsComponent;
	/**
	 * @var seoTools_filters
	 * @since version
	 */
	protected $seoTools_filters;

	/**
	 * @throws Exception
	 * @since 3.9
	 */
	public function __construct()
    {
        $this->app = JFactory::getApplication();
        $this->db = JFactory::getDbo();
        $this->doc = JFactory::getDocument();
        $this->uri = Uri::getInstance();

	    $seoTools_uri = seoTools_uri::instance();


	    JTable::addIncludePath(JPATH_SITE.'/administrator/components/com_customfilters/tables');

		$this->seoTools_filters = seoTools_filters::instance();

        $this->paramsComponent = JComponentHelper::getParams('com_customfilters');
		
		$debug_on = $this->paramsComponent->get('debug_on' , 0 ) ;
	    if (!defined('CF_FLT_DEBUG')) {
		    define('CF_FLT_DEBUG',     $debug_on );
		    if ( CF_FLT_DEBUG )
		    {
			    JLoader::register('seoTools_logger' , JPATH_ROOT .'/components/com_customfilters/include/seoTools_logger.php');
			    seoTools_logger::instance();
			}#END IF
	    }



        //load model
//        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_customfilters/models/setting_seo.php');

    }

	/**
	 * Route - ссылок для пагинации
	 *
	 * @param $data
	 *
	 * @return void
	 * @throws Exception
	 * @since    1.0.0
	 */
    public function getPagesLinksData( $data ){

        $data->all = $this->preparePaginationObj( $data->all );
        $data->start = $this->preparePaginationObj( $data->start );
        $data->previous = $this->preparePaginationObj( $data->previous );
        $data->next = $this->preparePaginationObj( $data->next );
        $data->end = $this->preparePaginationObj( $data->end );

        foreach ( $data->pages as &$page )
        {
            $page = $this->preparePaginationObj( $page );
        }
//	    echo'<pre>';print_r( seoTools_uri::$arrUrlSef );echo'</pre>'.__FILE__.' '.__LINE__;
//	    die(__FILE__ .' '. __LINE__ );

        return $data ;
    }

	/**
	 * Подготовить объект пагинации
	 *
	 * @param   \Joomla\CMS\Pagination\PaginationObject  $Object
	 *
	 * @return \Joomla\CMS\Pagination\PaginationObject
	 * @throws Exception
	 * @since    1.0.0
	 */
	protected function preparePaginationObj(\Joomla\CMS\Pagination\PaginationObject $Object): \Joomla\CMS\Pagination\PaginationObject
	{

		/**
		 *   $Object->link string (/filtr/vodostochnye-sistemy/?custom_f_23[0]=d093d0bbd18fd0bdd186d0b5d0b2d0b0d18f&custom_f_10 .....)
		 */
		if (!empty($Object->link))
		{

			$link         = \seoTools_uri::getSefUlrOption($Object->link);
			$Object->link = $link->sef_url;

		}

		return $Object;
	}

    /**
     * Установить Мета данные для страницы в соответствии с включенными фильтрами
     *
     * @param $res
     * @param $table
     * @return void
     * @since    1.0.0
     */
    public function setMetaData(   ){

		$DataFilters = $this->app->get('seoToolsActiveFilter' );
	    $table = $this->app->get('seoToolsActiveFilter.table' );

		
        $vmCategoryId = $this->app->input->get('virtuemart_category_id' , [] , 'ARRAY') ;

        /**
         * @var VirtueMartModelCategory
         */
        $categoryModel = VmModel::getModel('category');
        $vmCategory = $categoryModel->getCategory($vmCategoryId[0] );

        $filterOrdering = [];


        foreach ( $table as $key => $item)
        {

            $filter = $this->seoTools_filters->_getFilterById( $key );
            // Подготовить массив со значениями
            $filter->valueArr = self::prepareHex2binArr( $item );
            $filterOrdering[$filter->ordering] = $filter ;
        }



        $findReplaceArr = [
            '{{FILTER_LIST}}' => seoTools_shortCode::getFilterListText( $filterOrdering ) ,
            '{{FILTER_VALUE_LIST}}' => seoTools_shortCode::getFilterValueListText( $filterOrdering ) ,
            '{{CATEGORY_NAME}}' => $vmCategory->category_name ,
        ];


	
        $default_h1_tag = $this->paramsComponent->get('default_h1_tag' , false );
        $default_h1_tag = str_replace( array_keys($findReplaceArr) , $findReplaceArr ,  $default_h1_tag );

	    $default_h1_tag = $this->getLanguageText( $default_h1_tag );

	    $this->app->set('filter_data_h1' ,  $default_h1_tag  );



        $default_title = $this->paramsComponent->get('default_title' , false );
        $default_title = str_replace( array_keys($findReplaceArr) , $findReplaceArr ,  $default_title );
	    $default_title = $this->getLanguageText( $default_title );


        $default_description = $this->paramsComponent->get('default_description' , false );
        $default_description = str_replace( array_keys($findReplaceArr) , $findReplaceArr ,  $default_description );
	    $default_description = $this->getLanguageText( $default_description );

        $default_keywords = $this->paramsComponent->get('default_keywords' , false );
        $default_keywords = str_replace( array_keys($findReplaceArr) , $findReplaceArr ,  $default_keywords );
	    $default_keywords = $this->getLanguageText( $default_keywords );


        $this->doc->setTitle($default_title );
        $this->doc->setDescription( $default_description );
        $this->doc->setMetaData( 'keywords', $default_keywords );

    }

	/**
	 * Создание SEF URL - Для опции фильтра
	 *
	 * @param   CfFilter  $filter
	 * @param   stdClass  $option_url  etc. /filtr/metallocherepitsa/?custom_f_22[0]=41474e45544....
	 *
	 * @return stdClass Объект SEF данных
	 *
	 * @throws Exception
	 * @since version
	 */
	public function createSefUrl(\CfFilter $filter ,   stdClass $option ): stdClass
	{

        $var_name = $filter->getVarName();
        $option_url = $option->option_url ;

		/**
		 * TODO - Разобраться с фильтром для категорий
		 */
		if ( $var_name == 'virtuemart_category_id' )
        {
            $Options = $filter->getOptions();
//                    $option->option_sef_url->sef_url = $option->option_url ;

        }#END IF


		return \seoTools_uri::getSefUlrOption( $option_url );
	}

	/**
	 * Добавить созданные ссылок для опций фильтра в '#__cf_customfields_setting_seo'
	 * @param $optionsFilterArr
	 *
	 *
	 * @since version
	 */
	public function updateSeoTable($optionsFilterArr)
	{

		/*if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
		{
			echo'<pre>';print_r( $optionsFilterArr );echo'</pre>'.__FILE__.' '.__LINE__;
			die(__FILE__ .' '. __LINE__ );

		}*/



		// Исключаем ссылки для опций - индексирование которых запрещено
		foreach ($optionsFilterArr as $i => &$item)
		{
			if ($item->option_sef_url->no_index) {
				unset($optionsFilterArr[$i]);
            } #END IF
		}#END FOREACH



		if (empty($optionsFilterArr)) return; #END IF



		// ключ кеша страницы
		$optRegistry = new JRegistry( $optionsFilterArr );
		$key         = md5($optRegistry->toString());


		$cache = JFactory::getCache('cf_customfields_setting_seo', '');
		$cache->setCaching(1);


		if (!$cacheFilterArr = $cache->get($key))
		{
			// сохраняем $optionsFilterArr в кэше
			$cache->store($optionsFilterArr, $key);


		}
		else
		{
			if ($_SERVER['REMOTE_ADDR'] == DEV_IP)
			{
				$text = 'Обновление таблицы #__cf_customfields_setting_seo не требуется';
				$this->app->enqueueMessage($text);
            }

			return;
		}

		if ( CF_FLT_DEBUG ){
			seoTools_logger::add( 'UPD TBL - #__cf_customfields_setting_seo count (' .count($optionsFilterArr) .')'   );
		}

		$columns = ['vmcategory_id', 'url_params', 'url_params_hash', 'sef_url', 'no_index'];

		$this->db   = JFactory::getDBO();
		$query      = $this->db->getQuery(true);
		$countLines = 0;
		foreach ($optionsFilterArr as $options)
		{
			$values =
				$this->db->quote($options->option_sef_url->vmcategory_id) . ","
				. $this->db->quote($options->option_sef_url->url_params) . ","
				. $this->db->quote($options->option_sef_url->url_params_hash) . ","
				. $this->db->quote($options->option_sef_url->sef_url) . ","
				. $this->db->quote($options->option_sef_url->no_index);

			$query->values($values);
			$countLines++;
		}#END FOREACH
		$query->insert($this->db->quoteName('#__cf_customfields_setting_seo'))
			->columns($this->db->quoteName($columns));

		$this->db->setQuery(
		// Заменяет INSERT INTO на другой запрос
		// substr_replace($query, '******', 0, 12 )
			(string) $query . ' ON DUPLICATE KEY UPDATE url_params_hash = url_params_hash ; ');
		$this->db->execute();

		if ($_SERVER['REMOTE_ADDR'] == DEV_IP)
		{
			$text = 'Произведено обновление таблицы #__cf_customfields_setting_seo';
			$text .= '<br>Добавлено|Обновлено ' . $countLines . ' значений.';
			$this->app->enqueueMessage($text);

		}


	}


	/**
	 * Получить ссылку на текущую категорию Vm
	 *
	 * @param   bool|int  $category_id
	 *
	 * @return string
	 * @throws Exception
	 * @since    1.0.0
	 */
    public static function getPatchToVmCategory( $category_id = false ): string
    {
	     return seoTools_uri::getPatchToVmCategory( $category_id ) ;
    }



    /**
     * Подготовить массив со значениями
     * @param $valueCustomHashArr
     * @return array
     * @since    1.0.0
     */
    public static function prepareHex2binArr( $valueCustomHashArr ): array
    {
	    if ( !is_array($valueCustomHashArr) && !is_object( $valueCustomHashArr ))
	    {
			try
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

			echo'<pre>';print_r( $valueCustomHashArr );echo'</pre>'.__FILE__.' '.__LINE__;
			
			die(__FILE__ .' '. __LINE__ );

	    }#END IF


        foreach ( $valueCustomHashArr as $i => &$value ){
            $value = hex2bin( $value );
        }
        asort($valueCustomHashArr );
        return $valueCustomHashArr;
    }


	/**
	 * Очистить sef ссылку от лишних символов
	 *
	 * @param $sef_url
	 *
	 * @return array|string|string[]|null
	 * @throws Exception
	 * @since    1.0.0
	 */
    public static function cleanSefUrl( $sef_url ){
	    $app = JFactory::getApplication();
        $sef_suffix = $app->get('sef_suffix' , 0 ) ;
        $suffix = '';
        if ( $sef_suffix )
        {
            $suffix = '.html' ;
            $sef_url = str_replace($suffix , '' , $sef_url ) ;

        }#END IF
        return preg_replace('/[^\/\-_\w\d]/i', '', $sef_url) . $suffix ;
    }

    /**
     * Проверить - есть ли среди выбранных фильтров - отключенные
     * или в настройках выбранных фильтров установлено NO-INDEX -
     *
     * @param   array  $inputs  - массив с отфильтрованными категориями и отмеченными опциями фильтров
     *
     *
     * @return bool - Если есть отключенные фильтры  - то TRUE
     * @since 3.9
     */
    public static function checkOffFilters( array $inputs  ): bool
    {

	    $paramsComponent = \Joomla\CMS\Component\ComponentHelper::getParams('com_customfilters');

	    /**
	     * @var int $max_count_filters_no_index Максимальное количество активных фильтров. DEF:3
	     *                                      PARAM administrator/config.xml - max_count_filters_no_index
	     */
		$max_count_filters_no_index = $paramsComponent->get('max_count_filters_no_index' , 3 ) ;
	    /**
	     * @var int $limit_filter_no_index Максимальное количество активных опций во всех фильтре. DEF:3
	     *                                 PARAM administrator/config.xml - limit_filter_no_index
	     */
	    $limit_filter_no_index = $paramsComponent->get('limit_filter_no_index' , 3 ) ;

		if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
		{
//		    echo'<pre>';print_r( $inputs );echo'</pre>'.__FILE__.' '.__LINE__;
//		    die(__FILE__ .' '. __LINE__ );

		}

		// Если общее количество активных фильтров больше чем лимит - Def : 3
	    // (-1) - так как содержаться еще и категория
	    if ( ( count( $inputs ) -1 ) >=  $max_count_filters_no_index ) return true ; #END IF

		// --- Считаем опции во всех фильтрах
		$_allOptionCount = 0 ;
	    foreach ( $inputs as $filter => $option )
	    {
		    if ( $filter == 'virtuemart_category_id') continue ; #END IF
			$_allOptionCount += count( $option ) ;
		}#END FOREACH
	    if ( $_allOptionCount > $limit_filter_no_index ) return true ;  #END IF
	    // ---



        $idFieldActive = [] ;

        foreach ( $inputs as $key => $input)
        {

	        /**
	         * Считаем - количество выбранных опций в для каждого фильтра("любые фильтры когда 2 значение из 1-й группы")
	         */
	        if ( count( $input ) >= $limit_filter_no_index ) {  return true ;  }#END IF


            preg_match('/custom_f_(\d+)/', $key, $matches);
            if (empty($matches)) continue; #END IF
            $idFieldActive[] = $matches[1];
        }#END FOREACH


	    $result = [] ;
	    foreach ( $idFieldActive as $item)
	    {
		    $result[] = \seoTools_filters::$AllFilters[ $item ] ;

		}#END FOREACH


		if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
		{
//			echo'<pre>';print_r( $result );echo'</pre>'.__FILE__.' '.__LINE__;
//			die(__FILE__ .' '. __LINE__ );

		}



	    foreach ( $result as $item)
	    {
		    $keyInput = 'custom_f_' . $item->custom_id ;
			$params = json_decode( $item->params ) ;

			if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
			{
//			    echo'<pre>';print_r( $params );echo'</pre>'.__FILE__.' '.__LINE__;
//			    die(__FILE__ .' '. __LINE__ );

			}

			// Если фильтр использовать только как единственный
		    if ( $params->use_only_one_opt && count( $result ) > 1 ) return true ; #END IF

			// Проверка - если есть выбранные фильтры запрещенные для индексации
		    if ( !$item->on_seo ) return true ; #END IF

			// Если количество выбранных опций для фильтра больше чем установлено в расширенных настройках фильтра
		    if ( $params->limit_options_select_for_no_index && count( $inputs[$keyInput] ) > $params->limit_options_select_for_no_index )
				return true ; #END IF


			
		}#END FOREACH



        return false ;


    }

	/**
	 * Поиск и замена Языковых констант $pattern = '~\{\K.+?(?=})~';
	 * @param   string  $enterText  Текст с языковыми константами.
	 *                              Языковые константы должны быть окружены фигурными скобками
	 *
	 * @return string
	 *
	 * @since version
	 */
	public function getLanguageText( string $enterText ): string
	{
		$pattern = '~\{\K.+?(?=})~';
		preg_match_all($pattern, $enterText, $out);

		foreach ($out[0] as $item)
		{
			$text      = Text::_($item);
			$enterText = str_replace('{' . $item . '}', $text, $enterText);
		}#END FOREACH

		return $enterText ;
	}
}

/***

 * vid-poverhnosti
 *      -glyancevye
 * -and -imitaciya_naturalnyh_materialov
 * -and -matovye
 *
 */














