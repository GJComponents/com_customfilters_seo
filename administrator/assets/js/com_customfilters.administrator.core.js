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
        // Перехват событий JoomlaSubmit
        this.JoomlaSubmitInit();


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
        // Event - change for select
        /*document.querySelectorAll('select.knownLanguages').forEach( function (el , i){

            
            el.addEventListener('change' , function (e){
               console.log( 'com_customfilters.administrator.core' , e ); 
                
            })
        })*/
        // Event - change
        document.addEventListener('change', function (e) {
            console.log( 'customfilters.administrator.core' , e.target.dataset );
            console.log( 'customfilters.administrator.core' , e   );
            switch (e.target.dataset.evt) {
                case "on_seo_change" :
                    e.preventDefault();
                    self.updateOnSeoElement(e.target)
                    if (e.target.checked) {
                        console.log( 'customfilters.administrator.core' ,  "Checkbox is checked..");
                        console.log( 'customfilters.administrator.core' ,  e.target.checked);
                    } else {
                        console.log( 'customfilters.administrator.core' ,  "Checkbox is not checked..");
                        console.log( 'customfilters.administrator.core' ,  e.target.checked);
                    }
                    break;
                case "known_languages_change" :
                    self.updateKnownLanguagesElement(e.target)
                    break ;
            }
        });
        // Event - click
        document.addEventListener('click', function (e) {
            console.log( 'customfilters.administrator.core' , e.target.dataset.evt );
            switch (e.target.dataset.evt) {
                case "" :
                    break;
            }
        });
    }
    /**
     * Установка перехвата для событий Joomla.submitbutton
     * @constructor
     */
    this.JoomlaSubmitInit = function (){
        var JoomlaSubmitButtonClone = Joomla.submitbutton
        Joomla.submitbutton = function(task)
        {
            console.log( 'com_customfilters.administrator.core' , task );
            switch (task) {
                case 'add_filter_city_seo':
                    self.addFilterCitySeo();
                    break
                default : JoomlaSubmitButtonClone (task)
            }
        };
    }
    /**
     * Создание фильтра - "Отбор по городам"
     */
    this.addFilterCitySeo = function (){
        var Data = JSON.parse(JSON.stringify( self.AjaxDefaultData ));
        Data.task = 'onAjaxGetFormAddFilterCitySeo' ;
        Data.view = 'forms_add' ;
        Data.layout = 'add_city_seo' ;

        var AjaxPost = self.AjaxPost( Data )
        var getModal = self.__loadModul.Fancybox();
        var loadCss = self.load.css('/libraries/GNZ11/assets/js/modules/Bxslider/4.2.15/jquery.bxslider.min.css'),
        Promise.all([AjaxPost , getModal ]).then(function (DataPromise){
            var Html = DataPromise[0].data.form_html;
            var Modal = DataPromise[1]

            Modal.open(Html, {
                baseClass: "addFilterCitySeo", // Класс основного элемента
                touch: false,
            });
            console.log('com_customfilters.administrator.core', DataPromise);


        },function (err){console.log(err)});

        /*self.AjaxPost( Data ).then(function (r){
            console.log( 'customfilters.administrator.core' , r );

        },function (err){console.log(err)});*/
    }
    /**
     * Изменения запрат для фильтра - генерить страницы результата поиска с robot INDEX
     * @param El
     */
    this.updateOnSeoElement = function (El){
        var Data = JSON.parse(JSON.stringify( self.AjaxDefaultData ));
        Data.idField = $(El).closest('tr').find('input[name="cid[]"]').val();
        Data.status = El.checked?1:0
        self.AjaxPost( Data ).then(function (r){
            console.log( 'customfilters.administrator.core' , r );

        },function (err){console.log(err)});
    }
    /**
     * Изменение использования фильтра для языка (для всех -* | ru-RU | ua-UA)
     * @param El
     */
    this.updateKnownLanguagesElement = function (El) {
        var Data = JSON.parse(JSON.stringify( self.AjaxDefaultData ));
        Data.task = 'updateKnownLanguagesElement' ;
        Data.idField = $(El).closest('tr').find('input[name="cid[]"]').val();
        Data.status = El.value ;
        console.log( 'com_customfilters.administrator.core' , Data );
        self.AjaxPost( Data ).then(function (r){
            console.log( 'customfilters.administrator.core' , r );

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
                }, function (err) { console.error(err);   reject(err); })
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
















