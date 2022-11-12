<?php
/**
 * @package    vm_seo_product_filter_grt
 *
 * @author     Максим <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;

/**
 * Vm_seo_product_filter_grt
 *
 * @package  vm_seo_product_filter_grt
 * @since    1.0.0
 */
class CustomfiltersModelForms_add extends AdminModel
{

	/**
	 * @var string Ссылка на файл Json с городами
	 * @since 3.9
	 */
	protected $urlCityJson = 'https://gist.githubusercontent.com/gartes/ab9534ac8c6440297b921285264a8dd1/raw/8d3ed3eb3b809a090c9f3f4fc993903c509bcbad/cities.json';
    /**
     * @var   string Префикс для использования с сообщениями контроллера  The prefix to use with controller messages.
     *
     * @since 1.0.0
     */
    protected $text_prefix = 'COM_VM_SEO_PRODUCT_FILTER_GRT';
	/**
	 * @var int Индекс в массиве
	 * @since 3.9
	 */
	protected $contentLevel = -1;
	/**
	 * @var array - одномерный массив с результатом
	 * @since 3.9
	 */
	protected $ArrData = [] ;
	/**
	 * @var array Список alias - для поддержки уникальности
	 * @since 3.9
	 */
	protected $ArrAlias = [] ;
	/**
	 * @var int - Количество населенных пунктов
	 * @since 3.9
	 */
	protected $countCity = 0 ;
	/**
	 * @var string Имя таблицы для списка городов
	 * @since 3.9
	 */
	protected $cityTableName = '#__cf_customfields_city';

	/**
	 * Способ получения формы записи.
	 * Method to get the record form.
	 *
	 * @param   array    $data      Необязательный массив данных для опроса формы.
	 *                              An optional array of data for the form to interrogate.
	 * @param   boolean  $loadData  Истина, если форма должна загружать свои собственные данные (случай по умолчанию),
	 *                              ложь, если нет.
	 *                              True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form|boolean    Объект Form в случае успеха, false в случае неудачи  / A Form object on success, false on failure
	 * @since   1.0.0
	 */
    public function getForm($data = [], $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm(
            'com_customfilters.forms_add',
            'forms_add', array('control' => 'jform', 'load_data' => $loadData));
	    return !empty($form) ? $form : false;
	}

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    mixed    The data for the form.
     *
     * @since   1.0.0
     *
     * @throws  Exception
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState(
            'com_vm_seo_product_filter_grt.edit.vm_seo_product_filter_grt.data',
            []
        );

        if (empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Returns a Table object, always creating it.
     *
     * @param string    The table type to instantiate.
     * @param string    A prefix for the table class name. Optional.
     * @param array    Configuration array for model. Optional.
     * @return        Table    A database object.
     * @access        public
     * @since        1.0
     */
    public function getTable($type = 'Setting_seo', $prefix = 'CustomfiltersTable', $config = array())
    {
        return Table::getInstance($type, $prefix, $config);
    }

    public function publishTogle( $publish = 0 ){
        $app = \JFactory::getApplication();
        $user = \JFactory::getUser();
        $input = $app->input;
        $recordIdArr = $input->get('cid');

        /**
         * @var CustomfiltersTableSetting_seo Object
         */
        $table = $this->getTable();

        if ( empty( $recordIdArr ))
        {
            die('Не переданный Id записи ' . __FILE__ .' '. __LINE__ );
        }#END IF

        if (!$table->publish($recordIdArr, $publish , $user->get('id')))
        {

            // обрабатываем ошибки
            die(__FILE__ .' ^^^ // обрабатываем ошибки ^^^ '. __LINE__ );

        }

        // Clear the component's cache
        $this->cleanCache();
        return true ;
    }

	/**
	 * Сохранение формы фильтра городов
	 * @return void
	 * @throws Exception
	 * @since 3.9
	 */
	public function onAjaxSaveForm(){
		$app = \Joomla\CMS\Factory::getApplication();

		$Query = $this->_db->getQuery(ture);
		$formData = $app->input->get('formData', false, 'RAW');
		$params = array();
		parse_str($formData, $params);
		$paramsArr = [
			'use_city_setting'=> json_encode( $params['jform']['use_city_setting'] )
		];

		$columns = [ 'alias' ,   'published',   'on_seo',   'type_id', 'params', 'data_type' ];
		$values = [
			$this->_db->quote( $params['jform']['name_genirator'] ),

			$this->_db->quote( 1 ),    // published
			$this->_db->quote( 0 ),    // on_seo
			$this->_db->quote( 13 ),    // type_id
			$this->_db->quote( json_encode( $paramsArr ) ),    // params
			$this->_db->quote( 'string' ),    // data_type
		];
		$Query->values( implode( ',' , $values ) );
		$Query->insert( $this->_db->quoteName( '#__cf_customfields_setting_city' ) )
			->columns( $this->_db->quoteName( $columns ) );
		$this->_db->setQuery( $Query );



		try
		{

			$this->_db->execute();
		}
		catch ( \Exception $e )
		{



		    echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
//		    echo'<pre>';print_r( $e );echo'</pre>'.__FILE__.' '.__LINE__;
//		    die(__FILE__ .' '. __LINE__ );
		}


//		echo '<br>------------<br>Query Dump :'.__FILE__ .' '.__LINE__ .$Query->dump().'------------<br>';




		echo'<pre>';print_r( $params['jform'] );echo'</pre>'.__FILE__.' '.__LINE__;
		die(__FILE__ .' '. __LINE__ );
	}

	public function getListCity( $area_id = false ){
		$parentRegion = \Joomla\CMS\Factory::getApplication()->input->get('parentRegion' , 0 , 'INT' );


		$db = JFactory::getDbo();
		$Query = $db->getQuery(true);

		$Query->select('*');
		$Query->from( $this->cityTableName );
		$Query->where( $db->quoteName( 'parent_id') . '='. $db->quote( $parentRegion ) );
		$db->setQuery( $Query );
		try
		{
		    // Code that may throw an Exception or Error.
			$cityAssocList = $db->loadAssocList();
		    // throw new \Exception('Code Exception '.__FILE__.':'.__LINE__) ;
		}
		catch (\Exception $e)
		{
			// Если таблицы #__cf_customfields_city не существует
			if ( $e->getCode() == 1146 )
			{
				$this->_loadCityList();
			}#END IF
		    // Executed only in PHP 5, will not be reached in PHP 7
		    echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
		    echo'<pre>';print_r( $e );echo'</pre>'.__FILE__.' '.__LINE__;
		    die(__FILE__ .' '. __LINE__ );
		}
		return $cityAssocList ;

	}


	/**
	 * Загрузить список городов из Gist и установить в DB
	 * @return void
	 * @throws Exception
	 * @since 3.9
	 */
	protected function _loadCityList(){
		$app = \Joomla\CMS\Factory::getApplication();

		$contentsJson = file_get_contents( $this->urlCityJson );
		$contents = json_decode( $contentsJson ) ;
		$registry = new JRegistry($contents);
		$contents = $registry->toArray();

        $this->_getOneLevelArr( $contents );
		$app->enqueueMessage('Будет добавлено '. $this->countCity . ' городов');
		$this->_createCityTable( );
		$this->_loadCityDataInTable();

 	}

	/**
	 * Загрузить данные городов в таблицу
	 * @return void
	 * @since 3.9
	 */
	protected function _loadCityDataInTable(){
		$columns = [ 'id', 'parent_id', 'name', 'alias' ];
		$db    = JFactory::getDBO();
		$Query = $db->getQuery( true );
		foreach ( $this->ArrData as $item )
		{
			$values =
				$db->quote( $item[ 'id' ] ) . ","
				. $db->quote( $item[ 'parent_id' ] ) . ","
				. $db->quote( $item[ 'name' ] ) . ","
				. $db->quote( $item[ 'alias' ] )  ;

			$Query->values( $values );
		}//foreach
		$Query->insert( $db->quoteName( $this->cityTableName ) )->columns( $db->quoteName( $columns ) );
		$db->setQuery( $Query );
		echo $Query->dump();
		$db->execute();
	}

	/**
	 * Создание таблицы для городов
	 * @return void
	 * @since 3.9
	 */
	protected function _createCityTable( )
	{
		$db    = JFactory::getDBO();
		$query = "CREATE TABLE IF NOT EXISTS "
			. $db->quoteName($this->cityTableName)
			. " ( 
				`id` int(10) NOT NULL, 
				`parent_id` int(10) NOT NULL, 
				`name` varchar(120) NOT NULL, 
				`alias` varchar(120) NOT NULL, 
				
				`lat` varchar(12) NOT NULL, 
				`lng` varchar(12) NOT NULL, 
				
				PRIMARY KEY (`id`),
				KEY `parent_id` (`parent_id`), 
				UNIQUE KEY `alias` (`alias`) 
				) 
				ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$db->setQuery($query);
		$db->execute();

	}

	/**
	 * Подготовить данные в виде одномерного массива
	 *
	 * @param $contents - Данные в виде многомерного массива
	 *
	 * @return void
	 * @since 3.9
	 */
	protected function _getOneLevelArr( $contents ){
		$result = [];
		array_walk_recursive($contents, function ( $content, $key ) use (&$result) {

			if ( $key == 'id') { $this->contentLevel ++ ; }#END IF

			$this->ArrData[$this->contentLevel][$key] = $content ;
			// Если название города - делаем Alias
			if ( $key == 'name' )
			{
				$alias = \GNZ11\Document\Text::rus2translite($content);
				$alias = preg_replace('/[^A-Z0-9]/i', '-', $alias);
				$alias = str_replace('--', '-', $alias);
				$alias = preg_replace('/-$/i', '', $alias);
				$alias = mb_strtolower($alias) ;
				// для того что бы алиас был уникальным
				if (key_exists($alias, $this->ArrAlias))
				{
					$this->ArrAlias[$alias]++;
					$numIndex = $this->ArrAlias[$alias];
					$alias    .= '-' . $numIndex;
				}else{
					$this->ArrAlias[$alias] = 0 ;
				}#END IF

				$this->ArrData[$this->contentLevel]['alias'] = $alias ;
			}#END IF

			if ( $key == 'areas' && !empty( $content ) )
			{
				$this->_getOneLevelArr( $content );
			}else{
				// Считаем населенные пункты
				$this->countCity ++ ;
			}#END IF

		});
    }

}
