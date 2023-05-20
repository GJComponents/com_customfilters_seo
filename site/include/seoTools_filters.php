<?php

use Joomla\CMS\Application\CMSApplication;

/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 * @since 3.9
 */
class seoTools_filters
{
	/**
	 * @var CMSApplication|null
	 * @since 3.9
	 */
	private $app;
	/**
	 * @var JDatabaseDriver|null
	 * @since 3.9
	 */
	private $db;
	public static $instance;
	/**
	 * @var array Все опубликованные фильтры
	 * @since version
	 */
	public static $AllFilters ;

	/**
	 * helper constructor.
	 * @throws Exception
	 * @since 3.9
	 */
	private function __construct($options = array())
	{
		$this->db = JFactory::getDbo() ;
		$this->_getAllFilters();
		return $this;
	}#END FN

	/**
	 * @param   array  $options
	 *
	 * @return seoTools_filters
	 * @throws Exception
	 * @since 3.9
	 */
	public static function instance(array $options = array() ): seoTools_filters
	{
		if (self::$instance === null)
		{
			self::$instance = new self($options);
		}

		return self::$instance;
	}#END FN

	/**
	 * Получить все опубликованные фильтры
	 *
	 * @since    1.0.0
	 */
	public function _getAllFilters(){

		/**
		 * @var array $published_cf - Все опубликованные фильтры
		 */
		self::$AllFilters = \cftools::getCustomFilters('');

		return ;
		$Query = $this->db->getQuery(true);
		$Query->select([
			'cf.*' ,
			'customs.custom_title' ,
		])
			->from( $this->db->quoteName( '#__cf_customfields' , 'cf' )   )
			->leftJoin(
				'#__virtuemart_customs AS customs ON customs.virtuemart_custom_id = cf.vm_custom_id'
			)
			->where('cf.published = 1' )
			->order('cf.ordering');
//        echo $Query->dump();
		$this->db->setQuery($Query);


		
		self::$AllFilters = $this->db->loadObjectList('vm_custom_id');



	}
	/**
	 * Получить фильтр по ID
	 * @param $fieldId
	 * @return mixed
	 * @since    1.0.0
	 */
	public  function _getFilterById( $fieldId ): mixed
	{
		$fieldId = str_replace('custom_f_' , '' , $fieldId );

		if ( key_exists( $fieldId , self::$AllFilters ) )
		{
			return self::$AllFilters[$fieldId];
		}#END IF
		return false ;
	}
}