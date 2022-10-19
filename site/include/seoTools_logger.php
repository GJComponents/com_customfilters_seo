<?php

/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */
class seoTools_logger
{

	/**
	 * @var seoTools_logger
	 * @since version
	 */
	public static $instance;

	/**
	 * helper constructor.
	 * @throws Exception
	 * @since 3.9
	 */
	private function __construct($options = array() )
	{
		if (!isset($options['file'])) $options['file'] =  'cFilters.seoTools.debug.php' ; #END IF
		JLog::addLogger(
			array(
				// Устанавливает имя файла.
				'text_file' => $options['file'] ,
				// Устанавливает формат каждой строки.
				'text_entry_format' => '{DATETIME} {PRIORITY} {MESSAGE}'
			),
			// Устанавливает все, кроме сообщений уровня журнала DEBUG, которые будут отправлены в файл.
			JLog::ALL  ,
			// Категория журнала, которая должна быть записана в этом файле.
			array('Services_debug')
		);
		return $this;
	}#END FN

	/**
	 * @param   array  $options
	 *
	 * @return seoTools_logger
	 * @throws Exception
	 * @since 3.9
	 */
	public static function instance(array $options = array()): seoTools_logger
	{
		if (self::$instance === null)
		{
			self::$instance = new self($options);
		}
		return self::$instance;
	}#END FN

	public static function add( $textData ){
		JLog::add(JText::_( $textData ), JLog::DEBUG, 'Services_debug');
	}
}