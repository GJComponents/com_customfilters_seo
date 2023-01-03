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
defined( '_JEXEC' ) or die( 'Restricted access' );
extract( $displayData );
/**
 * @var string $name
 * @var string $parentAlias
 */

?>
<div class="vrap-meta">
	<!-- Шаблон тега h1 -->
	<div class="control-group">
		<div class="control-label">
			<label id="jform_default_h1_tag-lbl" for="jform_default_h1_tag"
			       class="hasPopover" title=""
			       data-content="CONFIG_DEFAULT_H_1_TAG_DESC"
			       data-original-title="Шаблон тега h1">
				Шаблон тега h1
			</label>
		</div>
		<div class="controls">
			<textarea name="jform[default_h1_tag]" id="jform_default_h1_tag" cols="4" rows="5" class="default_h1_tag" aria-invalid="false">{{CATEGORY_NAME}} - {{FILTER_VALUE_LIST}}</textarea>									</div>
	</div>
	<!-- Шаблон тега TITLE -->
	<div class="control-group">
		<div class="control-label">
			<label id="jform_default_title-lbl"
			       for="jform_default_title"
			       class="hasPopover" title=""
			       data-content="COM_CUSTOMFILTERS_SEO_SETTING_DEFAULT_TITLE_DESC"
			       data-original-title="Шаблон тега TITLE">
				Шаблон тега TITLE</label>
		</div>
		<div class="controls">
			<textarea name="jform[default_title]"
			          id="jform_default_title"
			          cols="5"
			          rows="10">
				{{CATEGORY_NAME}}
				{{FILTER_VALUE_LIST}}
			</textarea>
		</div>
	</div>
	<!-- Шаблон тега DESCRIPTION -->
	<div class="control-group">
		<div class="control-label">
			<label id="jform_default_description-lbl"
			       for="jform_default_description"
			       class="hasPopover" title=""
			       data-content="COM_CUSTOMFILTERS_SEO_SETTING_DEFAULT_DESCRIPTION_DESC"
			       data-original-title="Шаблон тега DESCRIPTION">
				Шаблон тега DESCRIPTION
			</label>
		</div>
		<div class="controls">
			<textarea name="jform[default_description]" id="jform_default_description" cols="5" rows="10">{{CATEGORY_NAME}} {{FILTER_VALUE_LIST}} купить 😍 - ➡️ Интернет-магазин 🧱 Маркет Профиль 🧱 от Металл Профиль ✅ доступные цены ➡️ Качественный сервис ☎️ +7 (495) 259-24-19</textarea>									</div>
	</div>
	<!-- Шаблон тега KEYWORDS -->
	<div class="control-group">
		<div class="control-label">
			<label id="jform_default_keywords-lbl" for="jform_default_keywords" class="hasPopover" title="" data-content="COM_CUSTOMFILTERS_SEO_SETTING_DEFAULT_KEYWORDS_DESC" data-original-title="Шаблон тега KEYWORDS">
				Шаблон тега KEYWORDS</label>
		</div>
		<div class="controls">
			<textarea name="jform[default_keywords]" id="jform_default_keywords" cols="5" rows="10">{{CATEGORY_NAME}} {{FILTER_VALUE_LIST}} </textarea>									</div>
	</div>
</div>
