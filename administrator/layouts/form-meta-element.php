<?php


/***********************************************************************************************************************
 *  ///////////////////////////╭━━━╮╱╱╱╱╱╱╱╱╭╮╱╱╱╱╱╱╱╱╱╱╱╱╱╭━━━╮╱╱╱╱╱╱╱╱╱╱╱╱╭╮////////////////////////////////////////
 *  ///////////////////////////┃╭━╮┃╱╱╱╱╱╱╱╭╯╰╮╱╱╱╱╱╱╱╱╱╱╱╱╰╮╭╮┃╱╱╱╱╱╱╱╱╱╱╱╱┃┃////////////////////////////////////////
 *  ///////////////////////////┃┃╱╰╯╭━━╮╭━╮╰╮╭╯╭━━╮╭━━╮╱╱╱╱╱┃┃┃┃╭━━╮╭╮╭╮╭━━╮┃┃╱╭━━╮╭━━╮╭━━╮╭━╮////////////////////////
 *  ///////////////////////////┃┃╭━╮┃╭╮┃┃╭╯╱┃┃╱┃┃━┫┃━━┫╭━━╮╱┃┃┃┃┃┃━┫┃╰╯┃┃┃━┫┃┃╱┃╭╮┃┃╭╮┃┃┃━┫┃╭╯////////////////////////
 *  ///////////////////////////┃╰┻━┃┃╭╮┃┃┃╱╱┃╰╮┃┃━┫┣━━┃╰━━╯╭╯╰╯┃┃┃━┫╰╮╭╯┃┃━┫┃╰╮┃╰╯┃┃╰╯┃┃┃━┫┃┃/////////////////////////
 *  ///////////////////////////╰━━━╯╰╯╰╯╰╯╱╱╰━╯╰━━╯╰━━╯╱╱╱╱╰━━━╯╰━━╯╱╰╯╱╰━━╯╰━╯╰━━╯┃╭━╯╰━━╯╰╯/////////////////////////
 *  ///////////////////////////╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱┃┃//  (C) 2022  ///////////////////
 *  ///////////////////////////╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╱╰╯/////////////////////////////////
 *----------------------------------------------------------------------------------------------------------------------
 * @author     Gartes | sad.net79@gmail.com | Telegram : @gartes
 * @date       12.12.22 19:28
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
/**
 *
 * @since 3.9
 * @copyright
 * @license
 */
// Check to ensure this file is included in Joomla!
use Joomla\CMS\Language\Text;

defined( '_JEXEC' ) or die( 'Restricted access' );
extract( $displayData );
/**
 * @var array $cityItem - Данные города + Meta params
 * @var string $name - значение атрибута name - для элемента формы
 * @var string $parentAlias
 */

$default_h1_tag_name = str_replace('[use]' , '[default_h1_tag]' , $name);
$default_title_name = str_replace('[use]' , '[default_title]' , $name);
$default_description_name = str_replace('[use]' , '[default_description]' , $name);
$default_keywords_name = str_replace('[use]' , '[default_keywords]' , $name);



//echo'<pre>';print_r( $cityItem );echo'</pre>'.__FILE__.' '.__LINE__;
//die(__FILE__ .' '. __LINE__ );

if ( $parentAlias )
{

//	echo'<pre>';print_r( $name );echo'</pre>'.__FILE__.' '.__LINE__;
//	echo'<pre>';print_r( $parentAlias );echo'</pre>'.__FILE__.' '.__LINE__;
//	die(__FILE__ .' '. __LINE__ );
}#END IF




?>
<div class="vrap-meta">
	<!-- Шаблон тега h1 -->
	<div class="control-group">
		<div class="control-label">
			<label id="jform_default_h1_tag-lbl" for="jform_default_h1_tag-<?= $alias ?>"
			       class="hasPopover"
			       data-content="<?= Text::_('COM_CUSTOMFILTERS_CONFIG_DEFAULT_H_1_TAG_DESC' )?>"
			       data-original-title="Шаблон тега h1">
				Шаблон тега h1
			</label>
		</div>
		<div class="controls">
			<textarea name="<?= $default_h1_tag_name ?>" id="jform_default_h1_tag-<?= $alias ?>"
                      class="default_h1_tag span8"
                      cols="4" rows="3"
                      aria-invalid="false"><?= $cityItem['params']['default_h1_tag'] ?></textarea>
            </div>
	</div>
	<!-- Шаблон тега TITLE -->
	<div class="control-group">
		<div class="control-label">
			<label id="jform_default_title-lbl" for="jform_default_title-<?= $alias ?>"
			       class="hasPopover" title=""
			       data-content="<?= Text::_('COM_CUSTOMFILTERS_SEO_SETTING_DEFAULT_TITLE_DESC' )?>"
			       data-original-title="Шаблон тега TITLE">
				Шаблон тега TITLE</label>
		</div>
		<div class="controls">
			<textarea name="<?= $default_title_name ?>" id="jform_default_title-<?= $alias ?>"
                      class="default_title span8"
                      cols="4" rows="3"
                      aria-invalid="false"><?= $cityItem['params']['default_title'] ?></textarea>
		</div>
	</div>
	<!-- Шаблон тега DESCRIPTION -->
	<div class="control-group">
		<div class="control-label">
			<label id="jform_default_description-lbl" for="jform_default_description-<?= $alias ?>"

                   data-content="COM_CUSTOMFILTERS_SEO_SETTING_DEFAULT_DESCRIPTION_DESC"
			       data-original-title="Шаблон тега DESCRIPTION">
				Шаблон тега DESCRIPTION
			</label>
		</div>
		<div class="controls">
			<textarea name="<?= $default_description_name ?>" id="jform_default_description-<?= $alias ?>"
                      class="default_description span8"
                      cols="4" rows="3"
                      aria-invalid="false">{{CATEGORY_NAME}} {{FILTER_VALUE_LIST}} купить  ✅ доступные цены ➡️ Качественный сервис</textarea>									</div>

        <?php
        /*if ( $alias == 'ukraina' || $alias == 'makedonovka'  )
        {
            */?><!--
            <textarea name="<?php /*= $default_description_name */?>" id="jform_default_description-<?php /*= $alias  */?>"
                      class="default_description span8"
                      cols="4" rows="3"
                      aria-invalid="false"><?php /*= $cityItem['params']['default_description'] */?></textarea>
            --><?php
/*        }#END IF*/

        ?>

    </div>
	<!-- Шаблон тега KEYWORDS -->
	<div class="control-group">
		<div class="control-label">
			<label id="jform_default_keywords-lbl" for="jform_default_keywords-<?= $alias ?>"
                   class="hasPopover" title=""
                   data-content="<?= Text::_('COM_CUSTOMFILTERS_SEO_SETTING_DEFAULT_KEYWORDS_DESC' )?>"
                   data-original-title="Шаблон тега KEYWORDS">
				Шаблон тега KEYWORDS</label>
		</div>
		<div class="controls">
			<textarea name="<?= $default_keywords_name ?>" id="jform_default_keywords-<?= $alias ?>"
                      class="default_keywords span8"
                      cols="4" rows="3"
                      aria-invalid="false"><?= $cityItem['params']['default_keywords'] ?></textarea>
        </div>
	</div>
</div>
