<?php
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

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
     * Массив со всеми опубликованными фильтрами из таблицы #__cf_customfields
     * @var array
     * @since    1.0.0
     */
    protected $AllFilters = [] ;
    /**
     * Параметры компонента com_customfilters
     * @var \Joomla\Registry\Registry
     * @since    1.0.0
     */
    protected $paramsComponent;

    public function __construct()
    {
        $this->app = JFactory::getApplication();
        $this->db = JFactory::getDbo();
        $this->doc = JFactory::getDocument();
        $this->uri = Uri::getInstance();
        JLoader::registerNamespace( 'GNZ11',JPATH_LIBRARIES.'/GNZ11',$reset=false,$prepend=false,$type='psr4');
	    JLoader::register('seoTools_shortCode' , JPATH_ROOT .'/components/com_customfilters/include/seoTools_shortCode.php');
	    JTable::addIncludePath(JPATH_SITE.'/administrator/components/com_customfilters/tables');

        // Получить все опубликованные фильтры
        $this->AllFilters = $this->_getAllFilters();

        $this->paramsComponent = JComponentHelper::getParams('com_customfilters');

        //load model
//        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_customfilters/models/setting_seo.php');

    }

    /**
     * Находим данные для Sef ссылки и устанавливаем параметры фильтра в APP - INPUT
     * Устанавливаем Мета данные
     *
     * @return void
     * @since    1.0.0
     */
    public function checkUrlPage(){

        $path = $this->uri->getPath();
        $pathCopySub = substr( $path ,0,-1);

        // Удалить параметры пагинации
        $path = preg_replace('/\/start=\d+/' , '' , $path );

        $Query = $this->db->getQuery(true );
        $Query->select('*')->from('#__cf_customfields_setting_seo')
            ->where(
                [
                    $this->db->quoteName( 'sef_url') . ' = ' . $this->db->quote( $path ),
                    $this->db->quoteName( 'sef_url') . ' = ' . $this->db->quote( $pathCopySub ),
                ],'OR'
            );
		

        $this->db->setQuery( $Query );
        $res = $this->db->loadObject();

	    if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
	    {
//		    echo $Query->dump();
//			echo'<pre>';print_r( $res );echo'</pre>'.__FILE__.' '.__LINE__;
			
	    }
		
        if ( !$res ) return ;


        $param = explode( '?' , $res->url_params ,2 ) ;
        if (!isset( $param[1] ) ) return ;
        parse_str( html_entity_decode( $param[1] ), $table );


        $this->setMetaData( $res , $table );

        foreach ( $table as $key => $item)
        { 
            $this->app->input->set( $key , $item );
        }
    }

    /**
     * Route - ссылок для пагинации
     * @param $data
     * @return void
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
        return $data ;
    }

    /**
     * Подготовить объект пагинации
     * @param \Joomla\CMS\Pagination\PaginationObject $Object
     * @return \Joomla\CMS\Pagination\PaginationObject
     * @since    1.0.0
     */
    protected function preparePaginationObj( \Joomla\CMS\Pagination\PaginationObject $Object ){

        if (!empty( $Object->link )){

            if ( empty( $Object->base )  &&  (int)$Object->base !== 0 ){
                $Res =  $this->getSefUrl( $Object->link );
                $Object->link = $Res->sef_url   ;
                return $Object ;
            }

            $uriLink = $this->uri::getInstance($Object->link);
            $queryArr = $uriLink->getQuery(true);

	        unset( $queryArr ['start'] );

            $uriLink->setQuery( $queryArr );

            $Object->link = $uriLink->toString();
            $Res =  $this->getSefUrl( $Object->link );

            $Object->link = $Res->sef_url . ( (int)$Object->base == 0 ? '' : 'start='.$Object->base  )  ;

        }
        return $Object ;
    }

    /**
     * Установить Мета данные для страницы в соответствии с включенными фильтрами
     *
     * @param $res
     * @param $table
     * @return void
     * @since    1.0.0
     */
    protected function setMetaData( $res , $table ){

        $vmCategoryId = $this->app->input->get('virtuemart_category_id' , [] , 'ARRAY') ;

        /**
         * @var VirtueMartModelCategory
         */
        $categoryModel = VmModel::getModel('category');
        $vmCategory = $categoryModel->getCategory($vmCategoryId[0] );

        $filterOrdering = [];

        foreach ( $table as $key => $item)
        {
            $filter = $this->_getFilterById( $key );
            // Подготовить массив со значениями
            $filter->valueArr = $this->prepareHex2binArr( $item );
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
     * Находим ссылки для элементов фильтра
     *
     * @param $option_url string  /filter/metallocherepitsa/?custom_f_27[0]=d09bd090d09cd09ed09dd0a2d095d0a0d0a0d090
     *
     * @return stdClass Object
     * @since    1.0.0
     */
    public function getSefUrl(string $option_url ){

        $res = $this->_getUrlObject( $option_url );
		// Если в DB нет ссылки, то - сохраняем
        if ( !$res ) {
            $res =  $this->_saveUrlObject( $option_url );
        }
        return $res    ;
    }

	/**
	 * Создание SEF URL - Для опции фильтра
	 *
	 * @param string $option_url etc. /filtr/metallocherepitsa/?custom_f_22[0]=41474e45544....
	 *
	 * @return stdClass
	 *
	 * @since version
	 */
	public function createSefUrl( $option_url ): stdClass
	{
		$resultData = new stdClass();
		$uri = \Joomla\CMS\Uri\Uri::getInstance( $option_url );
		$path =  $uri->getPath();
		$uriQuery = $uri->getQuery(true);
		$settingSeoOrdering = [] ;
		$resultData->vmcategory_id = $this->app->input->get('virtuemart_category_id' , 0 , 'INT') ;
		$resultData->url_params = $option_url ;

		$i_filterCount = 0;
		foreach ( $uriQuery as $fieldId => $valueCustomHashArr ){

			$filter = $this->_getFilterById( $fieldId );

			$filter->aliasTranslite = \GNZ11\Document\Text::rus2translite( $filter->alias ) ;
			$filter->aliasTranslite = mb_strtolower( $filter->aliasTranslite );
			$filter->aliasTranslite = str_replace(' ' , '_' , $filter->aliasTranslite );
			$filter->sef_url   = $filter->aliasTranslite ;

			$i_optionCount = 0 ;

			// Подготовить массив со значениями
			$valueCustomHashArr = $this->prepareHex2binArr( $valueCustomHashArr );

			foreach ( $valueCustomHashArr as $i => $valueCustom ){

				if ($i_optionCount) $filter->sef_url .= '-and';

				$valueCustomTranslite = \GNZ11\Document\Text::rus2translite($valueCustom) ;
				$valueCustomTranslite = mb_strtolower( $valueCustomTranslite );
				$valueCustomTranslite = str_replace(' ' , '_' , $valueCustomTranslite );

				$filter->sef_url  .= '-'.$valueCustomTranslite .'' ;
				$i_optionCount++;
			}

			$i_filterCount++;
			$settingSeoOrdering[$filter->ordering] = $filter ;
		}

		ksort($settingSeoOrdering);

		$resultData->sef_url = '';
		$iArrCount = 0 ;
		foreach ( $settingSeoOrdering as $ordering => $filter  ){
			if ( $iArrCount ) $resultData->sef_url .= '-and-';
			$resultData->sef_url .= $filter->sef_url ;
			$iArrCount++;
		}

		$resultData->no_ajax = false ;
		/**
		 * Если Sef link не пустой - Добавляем путь и в конец слэш
		 */
		if ( strlen( $resultData->sef_url ) ){
			$resultData->sef_url .= '/' ;
		}
		else{
			//  $path = str_replace('/filter/' , '/catalog/' , $path );
			$resultData->no_ajax = 1 ;
			$path = $this->getPatchToVmCategory();

		}

		$resultData->sef_url = $path . $resultData->sef_url   ;

		// Очистим от не нужных символов
		$resultData->sef_url = $this->cleanSefUrl( $resultData->sef_url );

		$resultData->url_params_hash = md5( $resultData->url_params ) ;

		if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
		{
//			echo'<pre>';print_r( $resultData );echo'</pre>'.__FILE__.' '.__LINE__;
//			die(__FILE__ .' '. __LINE__ );

		}

		return $resultData ;
	}

	/**
	 * Добавить созданные ссылки для опций фильтра в '#__cf_customfields_setting_seo'
	 * @param $optionsFilterArr
	 *
	 *
	 * @since version
	 */
	public function updateSeoTable($optionsFilterArr){

		if ( empty( $optionsFilterArr) )
		{
			return ;
		}#END IF

		$columns = [ 'vmcategory_id', 'url_params', 'url_params_hash' , 'sef_url' ];

		$this->db     = JFactory::getDBO();
		$query = $this->db->getQuery( true );

		foreach ( $optionsFilterArr as $options)
		{
			$values =
				$this->db->quote( $options->vmcategory_id) . ","
				. $this->db->quote(  $options->url_params ) . ","
				. $this->db->quote(  $options->url_params_hash ) . ","
				. $this->db->quote(  $options->sef_url )  ;

			$query->values( $values );

		}#END FOREACH
		$query->insert( $this->db->quoteName( '#__cf_customfields_setting_seo' ) )->columns( $this->db->quoteName( $columns ) );
		$this->db->setQuery(
			// Заменяет INSERT INTO на другой запрос
			// substr_replace($query, '******', 0, 12 )
			 $query . ' ON DUPLICATE KEY UPDATE url_params_hash=url_params_hash ; ' );

		if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
		{
			echo $query->dump();
			echo'<pre>';print_r( $optionsFilterArr );echo'</pre>'.__FILE__.' '.__LINE__;
			
			die(__FILE__ .' '. __LINE__ );

		}
		$this->db->execute();

//		echo'<pre>';print_r( $this->db );echo'</pre>'.__FILE__.' '.__LINE__;
		
//		die(__FILE__ .' '. __LINE__ );



	}


    /**
     * Сохранить параметры фильтра если его еще не создавали
     * @param $option_url
     * @return object|false
     * @since    1.0.0
     */
    protected function _saveUrlObject( $option_url ){

		$resultData = $this->createSefUrl( $option_url );


        try {

            /**
             * @var CustomfiltersTableSetting_seo
             */
            $settingSeoTable = JTable::getInstance('Setting_seo'  ,'CustomfiltersTable'  );
        } catch (Exception $e) {
            echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
            return false;
        }




        $data = new JRegistry($resultData);
        $resultDataArr = $data->toArray();
        $settingSeoTable->bind( $resultDataArr );

        try {
            $settingSeoTable->check();
        } catch (Exception $e) {
            echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
            die( __FILE__ .' ' . __LINE__);
        }

        /**
         * записать данные в ТБЛ. ==========================================
         */
        try {
			/**
			 * TODO**** - Отключено сохранение ссылок фильтра
			 */
			if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
			{
//				 $settingSeoTable->store(false );
			}

        } catch (Exception $e) {
            echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
            die( __FILE__ .' ' . __LINE__);
        }
        /**
         * записать данные в ТБЛ. ==========================================
         */

	    if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
	    {
		    echo'<pre>';print_r( $settingSeoOrdering );echo'</pre>'.__FILE__.' '.__LINE__;
		    
			echo'<pre>';print_r( $option_url );echo'</pre>'.__FILE__.' '.__LINE__;
		    echo'<pre>';print_r( $uriQuery );echo'</pre>'.__FILE__.' '.__LINE__;
		    echo'<pre>';print_r( $resultData );echo'</pre>'.__FILE__.' '.__LINE__;



	    }
        return $resultData ;
    }

    /**
     * Получить ссылку на категорию
     * @param $virtuemart_category_id
     * @return string
     * @since    1.0.0
     */
    public function getPatchToVmCategory( $virtuemart_category_id = false ){
        if (!$virtuemart_category_id) $virtuemart_category_id =  $this->app->input->get('virtuemart_category_id' , 0  , 'INT' );

        $path = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$virtuemart_category_id.'&virtuemart_manufacturer_id=0');
        return $path ;
    }

    /**
     * Получить фильтр по ID
     * @param $fieldId
     * @return mixed
     * @since    1.0.0
     */
    protected function _getFilterById( $fieldId ){
        $fieldId = str_replace('custom_f_' , '' , $fieldId );
        return $this->AllFilters[$fieldId];
    }

    /**
     * Подготовить массив со значениями
     * @param $valueCustomHashArr
     * @return array
     * @since    1.0.0
     */
    protected function prepareHex2binArr( $valueCustomHashArr ){
        foreach ( $valueCustomHashArr as $i => &$value ){
            $value = hex2bin( $value );
        }
        asort($valueCustomHashArr );
        return $valueCustomHashArr;
    }

    /**
     * Получить объект
     * @param $option_url
     * @return mixed|null
     * @since    1.0.0
     */
    protected function _getUrlObject($option_url){

	    $option_urlMd5 = md5( $option_url ) ;

	    if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
	    {
//		    echo'<pre>';print_r( $option_url );echo'</pre>'.__FILE__.' '.__LINE__;
//		    echo'<pre>';print_r( $option_urlMd5 );echo'</pre>'.__FILE__.' '.__LINE__;
//		    die(__FILE__ .' '. __LINE__ );

	    }



        $Query = $this->db->getQuery(true );
        $Query->select('*')->from( $this->settingSeoTable )
            ->where(
                ' '. $this->db->quoteName( 'url_params_hash')
                . ' = "'. $option_urlMd5 .'" '  );
//        echo $Query->dump();
        $this->db->setQuery( $Query );
        return $this->db->loadObject();
    }

    /**
     * Получить все опубликованные фильтры
     * @return array|mixed
     * @since    1.0.0
     */
    protected function _getAllFilters(){

        $Query = $this->db->getQuery(true);
        $Query->select([
            'cf.*' ,
            'customs.custom_title' ,
        ])->from( $this->db->quoteName( '#__cf_customfields' , 'cf' )   );
        $Query->leftJoin('#__virtuemart_customs AS customs ON customs.virtuemart_custom_id = cf.vm_custom_id');
        $Query->where('cf.published = 1' )->order('cf.ordering');
//        echo $Query->dump();
        $this->db->setQuery($Query);

        return $this->db->loadObjectList('vm_custom_id');
    }

    /**
     * Очистить sef ссылку от лишних символов
     * @param $sef_url
     * @return array|string|string[]|null
     * @since    1.0.0
     */
    public function cleanSefUrl( $sef_url ){
        return preg_replace('/[^\/\-_\w\d]/i', '', $sef_url);
    }

    /**
     * Проверить - есть ли среди выбранных фильтров - отключенные
     * или в настройках выбранных фильтров установлено NO-INDEX -
     *
     * @param $inputs
     *
     * @return bool - Если есть отключенные фильтры  - то TRUE
     * @since 3.9
     */
    public function checkOffFilters( $inputs ): bool
    {
	    $paramsComponent = \Joomla\CMS\Component\ComponentHelper::getParams('com_customfilters');

		// Максимальное количество активных фильтров
		$max_count_filters_no_index = $paramsComponent->get('max_count_filters_no_index' , 3 ) ;
	    // Максимальное количество активных опций во всех фильтре
	    $limit_filter_no_index = $paramsComponent->get('limit_filter_no_index' , 3 ) ;


		// Если общее количество активных фильтров больше чем лимит - Def : 3
	    if ( ( count( $inputs ) -1 ) >=  $max_count_filters_no_index ) return true ; #END IF

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




        $Query = $this->db->getQuery( true ) ;
		$select = [
			$this->db->quoteName( 'id' ) ,
			$this->db->quoteName( 'alias' ) ,
			$this->db->quoteName( 'vm_custom_id' ) ,
			$this->db->quoteName( 'on_seo' ) ,
			$this->db->quoteName( 'params' ) ,
		];
        $Query->select( $select );
        $Query->from('#__cf_customfields');
//        $Query->where( $this->db->quoteName('id') . 'IN ( "'.implode('","' , $idFieldActive  ).'")' );
        $Query->where( $this->db->quoteName('vm_custom_id') . 'IN ( "'.implode('","' , $idFieldActive  ).'")' );
//        $Query->where( $this->db->quoteName('on_seo') . ' = 0 ' );
        $this->db->setQuery( $Query ) ;
        $result = $this->db->loadObjectList();

	    foreach ( $result as $item)
	    {
		    $keyInput = 'custom_f_' . $item->vm_custom_id ;
			$params = json_decode( $item->params ) ;

			// Если фильтр использовать только как единственный
		    if ( $params->use_only_one_opt && count( $result ) > 1 ) return true ; #END IF
			
		    /*if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
		    {
		    echo'<pre>';print_r( $params->use_only_one_opt );echo'</pre>'.__FILE__.' '.__LINE__;
		    echo'<pre>';print_r( $result );echo'</pre>'.__FILE__.' '.__LINE__;
		    echo'<pre>';print_r( $inputs );echo'</pre>'.__FILE__.' '.__LINE__;

		    }*/

			// Проверка - если есть выбранные фильтры запрещенные для индексации
		    if ( !$item->on_seo ) return true ; #END IF

			// Если количество выбранных опций для фильтра больше чем установлено в расширенных настройках фильтра
		    if ( $params->limit_options_select_for_no_index && count( $inputs[$keyInput] ) > $params->limit_options_select_for_no_index )
				return true ; #END IF


			
		}#END FOREACH
		
	    if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
	    {
		    echo'<pre>';print_r(  'Эта страница разрешена для индексации'  );echo'</pre>'.__FILE__.' '.__LINE__;
//			die(__FILE__ .' '. __LINE__ );

 	    }


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














