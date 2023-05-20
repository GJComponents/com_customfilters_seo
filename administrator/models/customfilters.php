<?php
/**
 * @package    customfilters
 * @author        Sakis Terz
 * @copyright    Copyright (c) 2012-2021 breakdesigns.net. All rights reserved.
 * @license        http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

/**
 * The basic model class
 *
 * @author    Sakis Terz
 * @since    1.0
 */
class CustomfiltersModelCustomfilters extends \Joomla\CMS\MVC\Model\ListModel
{
    /**
     * Model context
     *
     * @var string
     * @since 1.0
     */
    var $extension = 'com_customfilters';

    /**
     * @var string
     * @since 1.0
     */
    var $name = 'Custom Filters';

    /**
     * Constructor.
     *
     * @param array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.0
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'filter_id', 'cf.filter_id',
                'alias', 'cf.alias',
                'ordering', 'cf.ordering',
                'data_type', 'cf.data_type',
                'custom_title', 'vmc.custom_title',
                'field_type', 'vmc.field_type',
                'type_id', 'cf.type_id',
                'published', 'cf.published',
                'custom_id', 'cf.vm_custom_id',
            ];
        }
        parent::__construct($config);

    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param string $ordering
     * @param string $direction
     * @throws Exception
     * @since 1.0
     */
    protected function populateState($ordering = 'cf.ordering', $direction = 'ASC')
    {
        // Initialise variables.

        $app = Factory::getApplication('administrator');

        // Adjust the context to support modal layouts.
        if ($layout = $app->input->get('layout', 'default')) {
            $this->context .= '.' . $layout;
        }

        // Load the filter published.
        $this->setState('filter.published', $app->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', ''));

        // Load the filter search.
        $this->setState('filter.search', $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', ''));

        // Load the filter type_id
        $formSubmited = $app->input->post->get('form_submited');
        if($formSubmited) {
            $type_ids = $app->input->post->get('filter.type_id');
            $this->setState('filter.category_id', $type_ids);
        }

        parent::populateState($ordering, $direction);
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param string $id A prefix for the store id.
     * @return    string        A store id.
     * @author    Sakis Terz
     * @since 1.0
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.published');
        $id .= ':' . $this->getState(serialize('filter.type_id'));
        return parent::getStoreId($id);
    }

    /**
     * Создайте запрос SQL для загрузки данных списка.
     *
     * Build an SQL query to load the list data.
     *
     * @param boolean $use_filters
     * @return    JDatabaseQuery
     * @author    Sakis Terz
     * @since    1.0
     */
    protected function getListQuery($use_filters = true)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        //table cf_customfields
        $query->select('cf.id AS id');
        $query->select('cf.ordering AS ordering');
        $query->select('cf.vm_custom_id AS vm_custom_id');
        $query->select('cf.alias AS alias');
        $query->select('cf.published AS published');

		// if false - field no SEF proc.
        $query->select('cf.on_seo AS on_seo');
	    if ( JLanguageMultilang::isEnabled() )
	    {
		    $query->select('cf.known_languages AS known_languages');
	    }


        $query->select('cf.type_id AS type_id');
        $query->select('cf.data_type AS data_type');
        $query->select('cf.order_by AS order_by');
        $query->select('cf.order_dir AS order_dir');
        $query->select('cf.params AS params');
        $query->from('#__cf_customfields AS cf');

        //table vituemart_customfields
        $query->select('vmc.virtuemart_custom_id AS custom_id');
        $query->select('vmc.custom_title AS custom_title');
        $query->select('vmc.field_type AS field_type');
        $query->select('vmc.custom_element AS custom_element');
        $query->select('vmc.custom_desc AS custom_descr');

        //joins
        $query->join('INNER', '#__virtuemart_customs AS vmc ON cf.vm_custom_id=vmc.virtuemart_custom_id');

        //set the wheres
        if ($use_filters) {
            $where = array();
            $where_q = '';

            //display type filter
            $disp_types = $this->getState('filter.type_id');

            if (!empty($disp_types)) {
                $disp_types = array_map([$db, 'quote'], $disp_types);
                $query->where('cf.type_id IN (' . implode(',', $disp_types) .')');
            }
            //published filter
            $published = $this->getState('filter.published');

            if (is_numeric($published)) {
                $query->where('cf.published = ' . (int)$published);
            } else if ($published === '') {
                $query->where('(cf.published = 0 OR cf.published = 1)');
            }

            //search filter
            $search = trim($this->getState('filter.search'));
            if (!empty($search)) {
                if (stripos($search, 'id:') === 0) {
                    $query->where('cf.filter_id = ' . (int)substr($search, 3));
                } else {
                    $search = $db->quote('%' . $db->escape($search, true) . '%');
                    $query->where('(vmc.custom_title LIKE ' . $search . ' || vmc.custom_desc LIKE ' . $search . ')');
                }
            }
        }

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering', 'cf.ordering');
        $orderDirn = $this->state->get('list.direction', 'ASC');
        if ($orderCol == 'ordering') {
            $orderCol = 'cf.ordering';
        }
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
        $query = (string)$query;
        return $query;
    }


    /**
     * Получить список всех фильтров для админ панели
     * Method to get a list of custom fields.
     * Overridden to add a check for access levels.
     *
     * Метод для получения списка настраиваемых полей.
     * Переопределено для добавления проверки уровней доступа.
     *
     *
     * @return    mixed    An array of data items on success, false on failure.
     * @since    1.0
     */
    public function getItems()
    {

        $items = parent::getItems();
        $customFieldsTypes = $this->getField_types();

		$app = \Joomla\CMS\Factory::getApplication();
		$view = $app->input->get('view' , false , 'STRING') ;
//		echo'<pre>';print_r( $app->input );echo'</pre>'.__FILE__.' '.__LINE__;
//		die(__FILE__ .' '. __LINE__ );

	    // Выборка для фильтров Городов
	    $db = Factory::getDbo();
	    $Query = $db->getQuery(true );
	    $Query->select('*')->from('#__cf_customfields_setting_city');
	    $where = [
		    $db->quoteName('type_id') .'='. $db->quote( '13' ),
	    ];
	    $Query->where($where);
	    $db->setQuery($Query);
//	    $resArrCityFiler = $db->loadObjectList();

		/*foreach ( $resArrCityFiler as $item)
	    {
		    $items[] = $item ;
	    }#END FOREACH*/

//		echo'<pre>';print_r( $resArrCityFiler );echo'</pre>'.__FILE__.' '.__LINE__;
//		echo'<pre>';print_r( $items );echo'</pre>'.__FILE__.' '.__LINE__;
		


        foreach ($items as &$item) {

//	        echo'<pre>';print_r( $item  );echo'</pre>'.__FILE__.' '.__LINE__;
//	        die(__FILE__ .' '. __LINE__ );

            $params = new Registry;
            $params->loadString($item->params);


            $item->smart_search = $params->get('smart_search', 0);
            $item->expanded = $params->get('expanded', 1);
            $item->scrollbar_after = $params->get('scrollbar_after', '');
            $item->slider_min_value = $params->get('slider_min_value', 0);
            $item->slider_max_value = $params->get('slider_max_value', 300);
            $item->filter_category_ids = $params->get('filter_category_ids', array());
            $item->display_if_filter_exist = $params->get('display_if_filter_exist', array());
            $item->conditional_operator = $params->get('conditional_operator', 'AND');
            $item->field_type_string = $customFieldsTypes[$item->field_type];
            if ($item->field_type == 'E') {
                $custom = $this->getCustomfield($item->custom_id);
                //write also which custom element/plugin it is
                $item->field_type_string .= ' (' . $custom->custom_element . ')';
            }
        }

//		echo'<pre>';print_r( $items );echo'</pre>'.__FILE__.' '.__LINE__;
//		die(__FILE__ .' '. __LINE__ );


        return $items;
    }

    /**
     * @param string $type
     * @param string $prefix
     * @param array $config
     * @return bool|\Joomla\CMS\Table\Table|JTable
     * @since    1.0.0
     */
    public function getTable($type = 'Customfilter', $prefix = 'Customfilters', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Метод создания фильтров в таблице #__cf_customfields.
     *  Проверка созданный фильтров на соответствие настраиваемых полей ADMINISTRATOR
     *
     *
     * Method to insert the existing records to the table cf_customfields.
     *
     *
     * It checks the virtuemart_customs table for new or deleted custom fields and updates the cf filters table accordingly
     * Он проверяет таблицу virtuemart_customs на наличие новых или удаленных настраиваемых полей
     * и соответствующим образом обновляет таблицу фильтров cf.
     * @return    mixed    true on success, JError on failure
     * @author    Sakis Terz
     * @since    1.0
     */
    public function createFilters()
    {

        $db = Factory::getDbo();
	    // Edit Joomla 4
	    $dispatcher = Joomla\CMS\Factory::getApplication()->getDispatcher();

        PluginHelper::importPlugin('vmcustom');

		


        //the accepted custom fields
        $params = ComponentHelper::getParams('com_customfilters');
        $field_types_ar = $params->get('used_cf');
        if (!isset($field_types_ar)) {
            $this->insertCfTypes();
            $field_types_ar = array("S", "I", "B", "D", "T", "V", "E");
        }
        //get the existing custom fields from the vm table
        $custom_fields = $this->getCustomfields('*', $field_types_ar);
		

		
        //the existing custom filters
        $cf_customfilters = $this->getCustomFilters();

        $slugs = array();

        //if there are no filters
        if (empty($cf_customfilters)) {
            $inserted_q = array();
            $counter = 1;

            foreach ($custom_fields as $vm_c) {

                $query2 = '';
                $slug = OutputFilter::stringURLUnicodeSlug($vm_c->custom_title);
                //check if the slug exists and format it accordingly
                while (in_array($slug, $slugs)) {
                    $slug = $slug . $vm_c->virtuemart_custom_id;
                }
                $slugs[] = $slug;
                //if not plugin



                if ($vm_c->field_type != 'E') {
                    if ($vm_c->field_type == 'I') $data_type = 'int';
                    else if ($vm_c->field_type == 'D') $data_type = 'date';
                    else $data_type = 'string';


	                $fieldVal = new \stdClass();
	                $fieldVal->vm_custom_id = $vm_c->virtuemart_custom_id ;
	                $fieldVal->alias = $db->quote($slug) ;
	                $fieldVal->ordering=$counter;
	                $fieldVal->published=1;
	                $fieldVal->data_type = $db->quote($data_type) ;

					try
					{
						// Insert the object into the user profile table.
						$result = JFactory::getDbo()->insertObject('#__cf_customfields', $fieldVal );
					    // throw new \Exception('Code Exception '.__FILE__.':'.__LINE__) ;
						$counter++;
						continue ;
					}
					catch (\Exception $e)
					{
					    // Executed only in PHP 5, will not be reached in PHP 7
					    echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
					    echo'<pre>';print_r( $e );echo'</pre>'.__FILE__.' '.__LINE__;
					    die(__FILE__ .' '. __LINE__ );
					}

					/*$query2 = "INSERT INTO #__cf_customfields (
                                 vm_custom_id,
                                 alias,
                                 ordering,
                                 published,
                                 data_type
                                 ) 
								VALUES (
								        $vm_c->virtuemart_custom_id
								        ," . $db->quote($slug) . "
								        ," . $counter . "
								        ,1
								        ," . $db->quote($data_type) . "
								        )
								        ";*/
                } //if its plugin call the plugin hook
                else {
                    $data_type = 'string';
                    $name = $vm_c->custom_element;
                    $virtuemart_custom_id = $vm_c->virtuemart_custom_id;

					// Edit Joomla 4
	                $event = new Joomla\Event\Event('onGenerateCustomfilters', [$name, $virtuemart_custom_id, &$data_type]);
	                $ret = $dispatcher->dispatch('onGenerateCustomfilters', $event);

					// old - Joomla 3
//	                $dispatcher = JEventDispatcher::getInstance();
//					$ret = $dispatcher->trigger('onGenerateCustomfilters', array($name, $virtuemart_custom_id, &$data_type));


					if ($ret == true && !empty($data_type)) {
                        $query2 = "INSERT INTO #__cf_customfields ( vm_custom_id, alias, ordering, published, data_type)";
                        $query2 .= " VALUES (" . $vm_c->virtuemart_custom_id . "," . $db->quote($slug) . "," . $counter . ",1," . $db->quote($data_type) . ")";
						$db->setQuery($query2);

						try {
							$db->execute();
						} catch ( RuntimeException $e) {
							echo'<pre>';print_r( $e );echo'</pre>'.__FILE__.' '.__LINE__;
							die(__FILE__ .' '. __LINE__ );
						}

						$counter++;
                    } else continue;
                }

            }

        }
		else {
            //filter custom ids
            $cf_customfilters_ids = array_keys($cf_customfilters);
            //custom fields custom ids
            $vm_customfield_ids = array_keys($custom_fields);

            //Находим новые customs fields - и добавляем в список фильтров
            $tobeAdded = array_diff($vm_customfield_ids, $cf_customfilters_ids);
            if (count($tobeAdded)) {
                foreach ($tobeAdded as $tba) {
                    $query = '';
                    //get the title
                    $current_custom = $custom_fields[$tba];
                    $title = $current_custom->custom_title;
                    $slug = $db->quote(JFilterOutput::stringURLUnicodeSlug($title));
                    if ($this->isSlugExists($slug)) $slug = $slug . $tba; //check if slug exists

                    if ($current_custom->field_type != 'E') {
                        if ($current_custom->field_type == 'I') $data_type = 'int';
                        else if ($current_custom->field_type == 'D') $data_type = 'date';
                        else $data_type = 'string';
                        $query = "INSERT INTO #__cf_customfields (vm_custom_id,alias,ordering,data_type) VALUES ($tba,$slug,(SELECT MAX(cf.ordering) FROM #__cf_customfields AS cf)+1," . $db->quote($data_type) . ")";
                    } //plugin
                    else {
                        $dispatcher = JEventDispatcher::getInstance();
                        $data_type = 'string';
                        $name = $current_custom->custom_element;
                        $virtuemart_custom_id = $current_custom->virtuemart_custom_id;
                        $ret = $dispatcher->trigger('onGenerateCustomfilters', array($name, $virtuemart_custom_id, &$data_type));
                        if ($ret == true && !empty($data_type)) {
                            $query = "INSERT INTO #__cf_customfields (vm_custom_id,alias,ordering,data_type)";
                            $query .= " VALUES (" . $current_custom->virtuemart_custom_id . "," . $slug . ",(SELECT MAX(cf.ordering) FROM #__cf_customfields AS cf)+1," . $db->quote($data_type) . ")";
                        } else continue;
                    }

                    $db->setQuery($query);
                    try {
                        $db->execute();
                    } catch (RuntimeException $e) {
                        //suck it
                    }
                }
            }

            //delete or update
            $tobeDeleted = array_diff( $cf_customfilters_ids , $vm_customfield_ids);

			// Перебираем фильтры
            foreach ($cf_customfilters as $cflt) {

	            if ( $cflt->type_id == 13 ) continue ;  #END IF

				$vm_custom_id = $cflt->vm_custom_id;

                //check if delete
                if (in_array($vm_custom_id, $tobeDeleted)) {
                    $query = "DELETE FROM #__cf_customfields WHERE vm_custom_id = $vm_custom_id";
                    $db->setQuery($query);
                    try {
                        $db->execute();
                    } catch (RuntimeException $e) {
                        echo'<pre>';print_r( $cflt );echo'</pre>'.__FILE__.' '.__LINE__;
                        die(__FILE__ .' '. __LINE__ );

                        throw $e;
                    }
                }
				else {
                    //check if we should update the data_type
                    if ($cflt->field_type == 'E') {


                        $data_type = 'string';
                        $name = $cflt->custom_element;

	                    // Edit Joomla 4
	                    $event = new Joomla\Event\Event('onGenerateCustomfilters', [$name, $vm_custom_id, &$data_type]);
	                    $ret = $dispatcher->dispatch('onGenerateCustomfilters', $event);


                    } else {
                        if ($cflt->field_type == 'I') $data_type = 'int';
                        else if ($cflt->field_type == 'D') $data_type = 'date';
                        else $data_type = 'string';
                    }

                    if ($data_type != $cflt->data_type) {//update
                        $query = "UPDATE `#__cf_customfields` SET `data_type`=" . $db->quote($data_type) . " WHERE id=$cflt->id";
                        $db->setQuery($query);
                        try {
                            $db->execute();
                        } catch (RuntimeException $e) {
                            throw $e;
                        }
                    }

                }

            }
        }
        return true;
    }

    /**
     * Получить существующие фильтры из таблиц #__cf_customfields && #__virtuemart_customs
     * Get the existing filters form tables #__cf_customfields && #__virtuemart_customs
     *
     *
     * @return array|bool  the filters Array
     * @since 1.9.0
     * @@ TODO - добавить выборку полей типа City SEO
     */
    public function getCustomFilters()
    {
        $query = $this->getListQuery($use_filters = false);
        $filters = $this->_getList($query);

//		echo'<pre>';print_r( $filters );echo'</pre>'.__FILE__.' '.__LINE__;
//		echo'<pre>';print_r( $query );echo'</pre>'.__FILE__.' '.__LINE__;
//		die(__FILE__ .' '. __LINE__ );


		try
		{
		    // Code that may throw an Exception or Error.

//		      throw new \Exception('Code Exception '.__FILE__.':'.__LINE__) ;
		}
		catch (\Exception $e)
		{
		    // Executed only in PHP 5, will not be reached in PHP 7
		    echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
		    echo'<pre>';print_r( $e );echo'</pre>'.__FILE__.' '.__LINE__;
		    die(__FILE__ .' '. __LINE__ );
		}



		if (empty($filters) || !is_array($filters)) return false;

        //create an assoc array with the vm_custom_id as key
        $new_array = array();
        foreach ($filters as $flt) {
	        if ( isset( $flt->vm_custom_id ) )
	        {
		        $new_array[$flt->vm_custom_id] = $flt;
	        }else{
		        $new_array[] = $flt;
	        }#END IF

        }

//	    echo'<pre>';print_r( $new_array );echo'</pre>'.__FILE__.' '.__LINE__;
//	    die(__FILE__ .' '. __LINE__ );





        return $new_array;
    }

    /**
     * Получить существующие настраиваемые поля / Get the existing custom fields
     *
     *
     * @param string $fields the fields to load from the database
     * @param string $custom_types a string containing the custom types
     * @return array
     * @since    1.9.0
     */
    public function getCustomfields($fields, $custom_types_ar)
    {
        if (empty($custom_types_ar)) {
            return [];
        }
        $db = Factory::getDbo();
        $new_ft_ar = array();
        if ($custom_types_ar) {
            $new_ft_ar = array();
            foreach ($custom_types_ar as $fta) {
                //if($fta!='E')
                $new_ft_ar[] = $db->quote($fta);//not plugins
                //else $load_plugin=true;
            }
        }
        if (isset($new_ft_ar)) {
            $field_types = implode(',', $new_ft_ar);
        }

        $query = $db->getQuery(true);
        $query->select($fields);
        $query->from('#__virtuemart_customs');
        $query->where("field_type IN ($field_types)");
        $query->order("ordering");
        $db->setQuery($query);
        if (strpos($fields, '*') !== false || strpos($fields, ',') !== false) {
            $results = $db->loadObjectList();
        } //single field
        else $results = $db->loadColumn();
        //create an assoc array using as key the virtuemart_custom_id
        $new_results = [];
        foreach ($results as $res) {
            $new_results[$res->virtuemart_custom_id] = $res;
        }
        return $new_results;
    }

    /**
     * Проверяет, существует ли SLUG в БД - Checks if the slug already exists in the db
     *
     * @param string $slug
     * @return string
     * @since    1.9.0
     */
    public function isSlugExists($slug)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('1');
        $query->from('#__cf_customfields');
        $query->where('alias=' . $db->quote($slug));
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

    /**
     * Get a specific custom field
     *
     * @param int $custom_id the fields to load from the database
     * @return \stdClass
     * @since    1.9.0
     */
    public function getCustomfield($custom_id)
    {
        if (empty($custom_id)) return;
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__virtuemart_customs');
        $query->where("virtuemart_custom_id=" . (int)$custom_id);
        $db->setQuery($query);
        $result = $db->loadObject();
        return $result;
    }

    /**
     * Метод для получения доступных типов отображения фильтра.
     * Method to get the available filter display types.
     * 1.Select, 2.Radios, 3.Checkboxes, 4.Links
     *
     * @return    array
     * @since    1.0
     */
    public function getAllDisplayTypes()
    {
        $joptions = $this->getDisplayTypes();
        return $joptions;
    }

    /**
     * Метод для получения доступных типов отображения фильтра.
     * Method to get the available filter display types.
     * 1.Select, 2.Radios, 3.Checkboxes, 4.Links
     *
     * @param string $datatype
     * @return array
     * @since 1.6.1
     */
    public function getDisplayTypes($datatype = '')
    {
        $options = array(
            array('id' => '1', 'type' => 'drop-down'),
            array('id' => '2', 'type' => 'radio'),
            array('id' => '3', 'type' => 'checkbox'),
            array('id' => '4', 'type' => 'link'),
            array('id' => '5', 'type' => 'range_inputs'),
            array('id' => '6', 'type' => 'range_slider'),
            array('id' => '5,6', 'type' => 'range_input_slider'),
            array('id' => '8', 'type' => 'range_calendars'),
            array('id' => '9', 'type' => 'color_btn_sinlge'),
            array('id' => '10', 'type' => 'color_btn_multi'),
            array('id' => '11', 'type' => 'button_single'),
            array('id' => '12', 'type' => 'button_multi'),
            array('id' => '13', 'type' => 'city_seo'),
        );

        $joptions = array();
        foreach ($options as $opt) {
            $opt = (object)$opt;
            if (!empty($datatype)) {
                if (($datatype != 'int' && $datatype != 'float') && ($opt->type == 'range_inputs' || $opt->type == 'range_slider' || $opt->type == 'range_input_slider')) {
                } else if ($datatype != 'date' && ($opt->type == 'range_calendars')) {
                } else if (($datatype != 'color_hex' && $datatype != 'color_name') && ($opt->type == 'color_btn_sinlge' || $opt->type == 'color_btn_multi')) {
                } else $joptions[] = HTMLHelper::_('select.option', $opt->id, $opt->type);
            } else {
                $joptions[] = HTMLHelper::_('select.option', $opt->id, $opt->type);
            }
        }
        return $joptions;
    }

    /**
     * @return    array    autorized Types of data
     * @author    Sakis Terz
     * @since    1.0
     */
    public function getField_types()
    {
        return array(
            'S' => Text::_('CF_STRING'),
            'I' => Text::_('CF_INTEGER'),
            'P' => Text::_('PARENT'),
            'B' => Text::_('CF_BOOLEAN'),
            'D' => Text::_('CF_DATE'),
            'T' => Text::_('CF_TIME'),
            'M' => Text::_('IMAGE'),
            'V' => Text::_('CF_CART_VARIANT'),
            'E' => Text::_('CF_PLUGIN')
        );
    }

    /**
     * Inserts the allowed custom field types in the extensions table as params
     * Вставляет разрешенные настраиваемые типы полей в таблицу расширений в качестве параметров.
     *
     * @since 1.0
     * @author Sakis Terz
     */
    public function insertCfTypes()
    {
        $db = Factory::getDbo();
        $q = 'UPDATE `#__extensions`  SET `params`=\'{"used_cf":["S","I","B","D","T","V","E"]}\' WHERE `element`="com_customfilters"';
        $db->setQuery($q);
        try {
            $db->execute();
        } catch (RuntimeException $e) {
            //suck it
        }
    }
}
