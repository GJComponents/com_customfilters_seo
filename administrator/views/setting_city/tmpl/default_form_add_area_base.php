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
 * @date       16.01.23 23:07
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2023 Open Source Matters, Inc. All rights reserved.
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

 ?>
<div class="form_add_area_base" >
    <form id="add-area-form">

        <div class="tab-content" id="addAreaFormContent">
            <div id="tab1_id" class="tab-pane active">
                <fieldset class="form-horizontal">
                    <legend>Создание нового региона</legend>
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="name-element">
                                <div class="control-group">
                                    <div class="control-label">
                                        <label id="jform_alias-lbl" for="jform_name" class="required" data-placement="bottom">
                                            Название региона<span class="star">&nbsp;*</span></label>
                                    </div>
                                    <div class="controls">
                                        <input type="text" name="jform[name]" id="jform_name" value="" class="name required" size="20"
                                               required="required" aria-required="true"
                                               pattern="[A-Za-zА-Яа-я\s0-9-_\(\)]+"
                                               aria-invalid="false">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="name-element">
                                <div class="control-group">
                                    <div class="control-label">
                                        <label id="jform_alias-lbl" for="jform_parent_area" data-placement="bottom">
                                            Родительский регион
                                        </label>
                                    </div>
                                    <div class="controls">
                                        <input type="text" name="jform[parent_area]" id="jform_parent_area" value="" class="parent_area" size="20"
                                               pattern="[A-Za-zА-Яа-я\s0-9-_]+"
                                               aria-invalid="false">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

        <div class="buttons-bar">
            <div class="row-fluid">
                <div class="span12">
                    <div class="btn-wrapper " id="toolbar-save-new-area">
                        <button onclick="window.CustomfiltersAdminCore.onSaveNewArea(this);return false;" class="btn btn-small button-save float-right">
                            <span class="icon-save" aria-hidden="true"></span>
                            Сохранить
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="jform[parent_id]" value="0" />
        <input type="hidden" name="option" value="com_customfilters" />
        <input type="hidden" name="view" value="setting_city" />
        <input type="hidden" name="task" value="" />
    </form>


</div>
