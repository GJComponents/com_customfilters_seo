<?php

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
//        echo $Query->dump() ;
        $this->db->setQuery( $Query );
        $res = $this->db->loadObject();


        if ( !$res ) return ;


        $param = explode( '?' , $res->url_params ,2 ) ;
        if (!isset( $param[1] ) ) return ;
        parse_str( html_entity_decode( $param[1] ), $table );


        $this->setMetaData( $res , $table );

        // TODO : Проверить -- похоже что не нужно
        //        $this->app->input->set( 'start' , 72 );

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
        JLoader::register('seoTools_shortCode' , JPATH_ROOT .'/components/com_customfilters/include/seoTools_shortCode.php');
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


        $this->app->set('filter_data_h1' , $default_h1_tag );



        $default_title = $this->paramsComponent->get('default_title' , false );
        $default_title = str_replace( array_keys($findReplaceArr) , $findReplaceArr ,  $default_title );

        $default_description = $this->paramsComponent->get('default_description' , false );
        $default_description = str_replace( array_keys($findReplaceArr) , $findReplaceArr ,  $default_description );

        $default_keywords = $this->paramsComponent->get('default_keywords' , false );
        $default_keywords = str_replace( array_keys($findReplaceArr) , $findReplaceArr ,  $default_keywords );

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
     * Сохранить параметры фильтра если его еще не создавали
     * @param $option_url
     * @return object|false
     * @since    1.0.0
     */
    protected function _saveUrlObject( $option_url ){




        $resultData = new stdClass();
        $uri = \Joomla\CMS\Uri\Uri::getInstance( $option_url );
        $path =  $uri->getPath();
        $uriQuery = $uri->getQuery(true);

//        echo'<pre>';print_r( $uri );echo'</pre>'.__FILE__.' '.__LINE__ .'<br>';
//        echo'<pre>';print_r( $uriQuery );echo'</pre>'.__FILE__.' '.__LINE__ .'<br>';
//        die( __FILE__ .' ' . __LINE__);


        try {
            JTable::addIncludePath(JPATH_SITE.'/administrator/components/com_customfilters/tables');
            /**
             * @var CustomfiltersTableSetting_seo
             */
            $settingSeoTable = JTable::getInstance('Setting_seo'  ,'CustomfiltersTable'  );
        } catch (Exception $e) {
            echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
            return false;
        }

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
         * Если Sef link не пустой - Добавляем путь и в конец слеш
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
            $settingSeoTable->store(false );
        } catch (Exception $e) {
            echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
            die( __FILE__ .' ' . __LINE__);
        }
        /**
         * записать данные в ТБЛ. ==========================================
         */

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
     * @param $inputs
     * @return bool - Если есть отключенные фильтры  - то TRUE
     * @since 3.9
     */
    public function checkOffFilters( $inputs ): bool
    {
        $idFieldActive = [] ;

        foreach ( $inputs as $key => $input)
        {
            preg_match('/custom_f_(\d+)/', $key, $matches);
            if (empty($matches)) continue; #END IF
            $idFieldActive[] = $matches[1];
        }#END FOREACH
        $Query = $this->db->getQuery( true ) ;
        $Query->select('id');
        $Query->from('#__cf_customfields');
        $Query->where( $this->db->quoteName('id') . 'IN ( "'.implode('","' , $idFieldActive  ).'")' );
        $Query->where( $this->db->quoteName('on_seo') . ' = 0 ' );
        $this->db->setQuery( $Query ) ;
        $result = $this->db->loadColumn();
        if ( !empty( $result ) )
        {
            return true ;
        }#END IF
        return false ;


    }
}

/***

 * vid-poverhnosti
 *      -glyancevye
 * -and -imitaciya_naturalnyh_materialov
 * -and -matovye
 *
 */














