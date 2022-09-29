/***********************************************************************************************************************
 * ╔═══╗ ╔══╗ ╔═══╗ ╔════╗ ╔═══╗ ╔══╗  ╔╗╔╗╔╗ ╔═══╗ ╔══╗   ╔══╗  ╔═══╗ ╔╗╔╗ ╔═══╗ ╔╗   ╔══╗ ╔═══╗ ╔╗  ╔╗ ╔═══╗ ╔╗ ╔╗ ╔════╗
 * ║╔══╝ ║╔╗║ ║╔═╗║ ╚═╗╔═╝ ║╔══╝ ║╔═╝  ║║║║║║ ║╔══╝ ║╔╗║   ║╔╗╚╗ ║╔══╝ ║║║║ ║╔══╝ ║║   ║╔╗║ ║╔═╗║ ║║  ║║ ║╔══╝ ║╚═╝║ ╚═╗╔═╝
 * ║║╔═╗ ║╚╝║ ║╚═╝║   ║║   ║╚══╗ ║╚═╗  ║║║║║║ ║╚══╗ ║╚╝╚╗  ║║╚╗║ ║╚══╗ ║║║║ ║╚══╗ ║║   ║║║║ ║╚═╝║ ║╚╗╔╝║ ║╚══╗ ║╔╗ ║   ║║
 * ║║╚╗║ ║╔╗║ ║╔╗╔╝   ║║   ║╔══╝ ╚═╗║  ║║║║║║ ║╔══╝ ║╔═╗║  ║║─║║ ║╔══╝ ║╚╝║ ║╔══╝ ║║   ║║║║ ║╔══╝ ║╔╗╔╗║ ║╔══╝ ║║╚╗║   ║║
 * ║╚═╝║ ║║║║ ║║║║    ║║   ║╚══╗ ╔═╝║  ║╚╝╚╝║ ║╚══╗ ║╚═╝║  ║╚═╝║ ║╚══╗ ╚╗╔╝ ║╚══╗ ║╚═╗ ║╚╝║ ║║    ║║╚╝║║ ║╚══╗ ║║ ║║   ║║
 * ╚═══╝ ╚╝╚╝ ╚╝╚╝    ╚╝   ╚═══╝ ╚══╝  ╚═╝╚═╝ ╚═══╝ ╚═══╝  ╚═══╝ ╚═══╝  ╚╝  ╚═══╝ ╚══╝ ╚══╝ ╚╝    ╚╝  ╚╝ ╚═══╝ ╚╝ ╚╝   ╚╝
 *----------------------------------------------------------------------------------------------------------------------
 * @author Gartes | sad.net79@gmail.com | Telegram : @gartes
 * @date 29.09.22 09:42
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
/* global jQuery , Joomla   */
window.customfiltersAdminCore = function () {
    var $ = jQuery;
    var self = this;
    // Домен сайта
    var host = Joomla.getOptions('GNZ11').Ajax.siteUrl;
    // Медиа версия
    var __v = '?rm=';
    var passiveSupported = false;
    try {
        window.addEventListener("test", null,
            Object.defineProperty({}, "passive", {
                get: function () {
                    passiveSupported = true;
                }
            }));
    } catch (err) {
    }
    this.__type = false;
    this.__plugin = false;
    this.__name = false;
    this._params = {

        __module: false,
        RecentlyViewed: false,
    };
    // Ajax default options
    this.AjaxDefaultData = {
        group: null,
        plugin: null,
        module: null,
        method: null,
        option: 'com_customfilters',
        format: 'json',
        task: 'updateOnSeoElement',
    };
    // Default object parameters
    this.ParamsDefaultData = {
        // Медиа версия
        __v: '1.0.0',
        // Режим разработки
        development_on: false,
    }

    /**
     * Start Init
     * @constructor
     */
    this.Init = function () {
        this._params = Joomla.getOptions('customfiltersAdminCore', this.ParamsDefaultData);
        __v = self._params.development_on ? '' : '?v=' + self._params.__v;



        // Параметры Ajax Default
        this.setAjaxDefaultData();
        this.addEvtListener();
    };

    /**
     * Добавить слушателей событий
     * Для элементов с событиями должен быть установлен атрибут data-evt=""
     * etc. -   <a data-evt="map-go">
     *              <span class="icon-database" aria-hidden="true"></span>
     *              Map-Go
     *          </a>
     */
    this.addEvtListener = function () {

        // Event - change
        document.addEventListener('change', function (e) {
            console.log( 'customfilters.admin.core' , e.target.dataset );
            switch (e.target.dataset.evt) {
                case "on_seo_change" :
                    e.preventDefault();
                    self.updateOnSeoElement(e.target)
                    if (e.target.checked) {
                        console.log( 'customfilters.admin.core' ,  "Checkbox is checked..");
                        console.log( 'customfilters.admin.core' ,  e.target.checked);
                    } else {
                        console.log( 'customfilters.admin.core' ,  "Checkbox is not checked..");
                        console.log( 'customfilters.admin.core' ,  e.target.checked);
                    }
                    break;
            }
        });
        // Event - click
        document.addEventListener('click', function (e) {
            console.log( 'customfilters.admin.core' , e.target.dataset.evt );
            switch (e.target.dataset.evt) {
                case "" :
                    break;
            }
        });
    }

    this.updateOnSeoElement = function (El){
        var Data = JSON.parse(JSON.stringify( self.AjaxDefaultData ));
        Data.idField = $(El).closest('tr').find('input[name="cid[]"]').val();
        Data.status = El.checked?1:0
        self.AjaxPost( Data ).then(function (r){
            console.log( 'customfilters.admin.core' , r ); 

        },function (err){console.log(err)});
    }

    /**
     * Отправить запрос
     * @param Data - отправляемые данные
     *      Клонировать объект AjaxDefaultData  ---
     *      var Data = JSON.parse(JSON.stringify( self.AjaxDefaultData ));
     *      - Если обращение к компоненту Joomla Должен содержать ---
     *      Data.task = 'taskName';
     *
     * @param Params - Array
     *          Params = {
     *             URL : this._params.URL,
     *             dataType : this._params.dataType , 
     *         }
     *         <?php
     *          $doc = \Joomla\CMS\Factory::getDocument();
     *          $opt = [
     *              // Медиа версия
     *              '__v' => '1.0.0',
     *                 // Режим разработки
     *              'development_on' => false,
     *              // URL - Сайта
     *              'URL' => JURI::root(),
     *              'dataType' => 'html' , - по умлчанию 'json'
     *          ];
     *          $doc->addScriptOptions('customfiltersAdminCore' , $opt );
     *         ?>
     * @returns {Promise}
     * @constructor
     */
    this.AjaxPost = function (Data, Params) {
        var data = $.extend(true, this.AjaxDefaultData, Data);
        return new Promise(function (resolve, reject) {
            self.getModul("Ajax").then(function (Ajax) {
                // Не обрабатывать сообщения
                Ajax.ReturnRespond = true;
                // Отправить запрос
                Ajax.send(data, 'customfiltersAdminCore', Params).then(function (r) {
                    resolve(r);
                }, function (err) {
                    console.error(err);
                    reject(err);
                })
            });
        });
    };
    /**
     * Параметры Ajax Default
     */
    this.setAjaxDefaultData = function () {
        if (typeof Joomla !== 'undefined' ){
            this.AjaxDefaultData[1] = Joomla.getOptions('csrf.token', false );
        }
        this.AjaxDefaultData.group = this._params.__type;
        this.AjaxDefaultData.plugin = this._params.__name;
        this.AjaxDefaultData.module = this._params.__module;
        this._params.__name = this._params.__name || this._params.__module;
    }

    this.Init();
};
(function () {
    if (typeof window.GNZ11 === "undefined") {
        // Дожидаемся события GNZ11Loaded
        document.addEventListener('GNZ11Loaded', function (e) {
            start()
        }, false);
    } else {
        start()
    }

// Start prototype
    function start() {
        window.customfiltersAdminCore.prototype = new GNZ11();
        window.CustomfiltersAdminCore = new window.customfiltersAdminCore();
    }
})()
















