<?php


/***********************************************************************************************************************
 *  ///////////////////////////╭━━━╮╱╱╱╱╱╱╱╱╭╮╱╱╱╱╱╱╱╱╱╱╱╱╱╭━━━╮╱╱╱╱╱╱╱╱╱╱╱╱╭╮////////////////////////////////////////
 *  ///////////////////////////┃╭━╮┃╱╱╱╱╱╱╱╭╯╰╮╱╱╱╱╱╱╱╱╱╱╱╱╰╮╭╮┃╱╱╱╱╱╱╱╱╱╱╱╱┃┃////////////////////////////////////////
 *  ///////////////////////////┃┃╱╰╯╭━━╮╭━╮╰╮╭╯╭━━╮╭━━╮╱╱╱╱╱┃┃┃┃╭━━╮╭╮╭╮╭━━╮┃┃╱╭━━╮╭━━╮╭━━╮╭━╮////////////////////////
 *  ///////////////////////////┃┃╭━╮┃╭╮┃┃╭╯╱┃┃╱┃┃━┫┃━━┫╭━━╮╱┃┃┃┃┃┃━┫┃╰╯┃┃┃━┫┃┃╱┃╭╮┃┃╭╮┃┃┃━┫┃╭╯////////////////////////
 *  ///////////////////////////┃╰┻━┃┃╭╮┃┃┃╱╱┃╰╮┃┃━┫┣━━┃╰━━╯╭╯╰╯┃┃┃━┫╰╮╭╯┃┃━┫┃╰╮┃╰╯┃┃╰╯┃┃┃━┫┃┃/////////////////////////
 *  ///////////////////////////╰━━━╯╰╯╰╯╰╯╱╱╰━╯╰━━╯╰━━╯╱╱╱╱╰━━━╯╰━━╯╱╰╯╱╰━━╯╰━╯╰━━╯┃╭━╯╰━━╯╰╯/////////////////////////
 *  ///////////////////////////╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱┃┃//  (C) 2023  ///////////////////
 *  ///////////////////////////╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╰╯/////////////////////////////////
 *----------------------------------------------------------------------------------------------------------------------
 * @author     Gartes | sad.net79@gmail.com | Telegram : @gartes
 * @date       07.02.23 14:39
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2023 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 *
 * @since 3.9
 * @copyright
 * @license
 */


use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/**
 * @var  CustomfiltersViewSetting_seo_list $this
 */

HTMLHelper::_( 'bootstrap.tooltip' );
HTMLHelper::_( 'formbehavior.chosen' );
HTMLHelper::_( 'behavior.formvalidator' );

$listOrder     = $this->escape( $this->state->get( 'list.ordering' ) );
$listDirection = $this->escape( $this->state->get( 'list.direction' ) );
$loggedInUser  = Joomla\CMS\Factory::getUser();
$canEdit       = $this->canDo->get( 'core.edit'   );
$canChange     = $loggedInUser->authorise( 'core.edit.state' , 'com_customfilters' );




// TODO - разобратся с правими доступа
// https://docs.joomla.org/J3.x:Developing_an_MVC_Component/Adding_Access
//$canEdit      = true;
//$loggedInUser = true;


//echo'<pre>';print_r( $this->canDo );echo'</pre>'.__FILE__.' '.__LINE__;
//echo '<pre>'; print_r( $this->items ); echo '</pre>'.__FILE__.' '.__LINE__;
?>
<form action="<?=Joomla\CMS\Router\Route::_( 'index.php?option=com_customfilters&view=setting_seo_list' )?>" method="post" name="adminForm" id="adminForm">

    <div id="j-sidebar-container" class="span2">
		<?= $this->sidebar; ?>
    </div>

    <div id="j-main-container" class="span10">

        <!-- Инструменты поиска и фильтрации -->
        <div class="row-fluid">
			    <?= Text::_('COM_CUSTOMFILTERS_SETTING_SEO_LIST_LINKS_FILTER_SORT'); ?>
			    <?= JLayoutHelper::render( 'joomla.searchtools.default',  array('view' => $this)  ); ?>
        </div>

		<?php if ( empty( $this->items ) ) : ?>
            <div class="alert alert-no-items">
				<?php echo Text::_( 'JGLOBAL_NO_MATCHING_RESULTS' ); ?>
            </div>
		<?php else : ?>
            <table class="table table-striped" id="itemsList">
                <thead>
                <tr>
                    <th width="1%" class="nowrap center">
						<?php echo HTMLHelper::_( 'grid.checkall' ); ?>
                    </th>
                    <th width="1%" class="nowrap center">
						<?php echo HTMLHelper::_( 'searchtools.sort' , 'JSTATUS' , 'items.published' , $listDirection , $listOrder ); ?>
                    </th>
                    <th class="left">
						<?php echo HTMLHelper::_( 'searchtools.sort' , 'COM_CUSTOMFILTERS_LINKS_FILTER' , 'items.title' , $listDirection , $listOrder ); ?>
                    </th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td colspan="15">
						<?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
                </tfoot>
                <tbody>
				<?php


				foreach ( $this->items as $i => $item ) : ?>
                    <tr>
                        <td class="center">
							<?php if ( $canEdit || $canChange ) : ?>
								<?php echo HTMLHelper::_( 'grid.id' , $i , $item->id ); ?>
							<?php endif; ?>
                        </td>
                        <td class="center">
                            <div class="btn-group">
								<?php echo HTMLHelper::_( 'jgrid.published' , $item->published , $i , 'setting_seo.' , $canChange ); ?>
                            </div>
                        </td>
                        <td>
                            <div class="name break-word">
								<?php if ( $canEdit ) : ?>
                                    <a href="<?=Route::_( 'index.php?option=com_customfilters&view=setting_seo&layout=edit&id='.(int) $item->id );?>"
                                       title="<?=Text::sprintf( 'COM_VM_SEO_PRODUCT_FILTER_GRTS_EDIT_VM_SEO_PRODUCT_FILTER_GRT' , $this->escape( $item->title ) );?>">
										<?=$this->escape( $item->sef_url );?></a>
								<?php else : ?>
									<?php echo $this->escape( $item->sef_url ); ?>
								<?php endif; ?>
                                <div>
                                    <small><?php echo $this->escape( $item->url_params_hash ); ?></small>
                                </div>
                            </div>
                        </td>
                    </tr>
				<?php endforeach; ?>
                </tbody>
            </table>
		<?php endif; ?>

    </div>


    <input type="hidden" name="option" value="com_customfilters"/>
    <input type="hidden" name="view" value="setting_seo_list"/>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
	<?= JHtml::_( 'form.token' ); ?>
</form>
