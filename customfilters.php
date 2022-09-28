<?php
    /**
     *
     * Customfilters entry point
     *
     * @package        customfilters
     * @author        Sakis Terz
     * @link        http://breakdesigns.net
     * @copyright    Copyright (c) 2012-2021 breakdesigns.net. All rights reserved.
     * @license        http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
     *                customfilters is free software. This version may have been modified
     *                pursuant to the GNU General Public License, and as distributed
     *                it includes or is derivative of works licensed under the GNU
     *                General Public License or other free or open source software
     *                licenses.
     */

    // Check to ensure this file is included in Joomla!
    defined('_JEXEC') or die('Restricted access');

    use Joomla\CMS\Language\Text;

    // Access check.
    if (!JFactory::getUser()->authorise('core.manage', 'com_customfilters'))
    {
        throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'));
    }

    if (!class_exists('VmConfig'))
    {
        $vmconfigPath = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php';
        if (!file_exists($vmconfigPath))
        {
            throw new \RuntimeException('Virtuemart is not installed or it\'s files are not accessible');
        }
        require($vmconfigPath);
    }
    VmConfig::loadConfig();
    if (!class_exists('cfHelper'))
    {
        require(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_customfilters' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cfhelper.php');
    }
    if (!class_exists('VmCompatibility'))
    {
        require(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_customfilters' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'vmcompatibility.php');
    }

    /**
     *  TODO - Разобраться с обновлениями
     */
    JLoader::register('Breakdesigns\Customfilters\Admin\Model\UpdateManager',
        dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'UpdateManager.php');


    // Подключение GNZ11
    try
    {
        JLoader::registerNamespace( 'GNZ11' , JPATH_LIBRARIES . '/GNZ11' , $reset = false , $prepend = false , $type = 'psr4' );
        $GNZ11_js =  \GNZ11\Core\Js::instance();
    }
    catch( Exception $e )
    {
        if( !\Joomla\CMS\Filesystem\Folder::exists( $this->patchGnz11 ) && $this->app->isClient('administrator') )
        {
            $this->app->enqueueMessage('The GNZ11 library must be installed GNZ11' , 'error');
        }#END IF
    }


    // Add stylesheets and Scripts
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root() . 'administrator/components/com_customfilters/assets/css/display.css');
    JHtml::_('behavior.framework');
    JHtml::_('behavior.modal');

    // Include dependencies
    jimport('joomla.application.component.controller');
    $input = JFactory::getApplication()->input;

    $controller = JControllerLegacy::getInstance('customfilters');
    $controller->execute($input->get('task', 'display', 'cmd'));
    $controller->redirect();
