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
    } catch (err) {  }
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
                case "changeCityPublished" :
                    self.onChangeCityPublished(e.target)
                    break ;
            }
        });
        // Event - click
        document.addEventListener('click', function (e) {


            console.log( 'customfilters.administrator.core' , e.target.dataset.evt );
            switch (e.target.dataset.evt) {
                case "loadChildrenArea" :
                    self.loadChildrenArea(e.target);
                    break;
                case 'onLoadSettingFilter' :
                    self.onLoadSettingFilter(e.target);
                    break ;
            }
        });
        // Event - keyup
        document.addEventListener("keyup", function (e) {
            if ( $(e.target).hasClass('translite')){
                self.onKeyupSetTranslite(e)
            }
            console.log( 'com_customfilters.administrator.core' , e.target );
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
                case 'setting_city.add_area_base':
                    self.onAddAreaBase();
                    break ;
                case 'add_filter_city_seo':
                    self.addFilterCitySeo();
                    break
                // TODO -- Добавить в шаблон файла
                case 'setting_city.save':
                     var form = document.getElementById("adminForm");
                     if ( document.formvalidator.isValid( form ) ){
                         JoomlaSubmitButtonClone (task)
                     }else{
                         var msg = [];
                         var $alertHeading = $('.alert-heading');
                         $alertHeading.text('Error');
     
                         msg.push('Invalid input, please verify again!');
                         var $invalidEl = $(form).find( '.invalid[aria-invalid="true"]' )
                         console.log( 'com_customfilters.administrator.core' , $invalidEl );

                         /*if (  $invalidEl.length > 0) {
                             var message = Joomla.JText._('JLIB_FORM_FIELD_INVALID');
                             var error = {"error": []};
                             for (var i = $invalidEl.length - 1; i >= 0; i--) {
                                var label = jQuery($invalidEl[i]).data("label");
                                 if (label) {
                                     error.error.push(message + label.text().replace("*", ""));
                                 }
                             }
                             console.log( 'com_customfilters.administrator.core' , error ); 

                             Joomla.renderMessages(error);
                             return false;
                         }*/


                         /*if($('email').hasClass('invalid')){
                             msg.push('Invalid Email');
                         }
                         $alertHeading.parent().append( '<div>'+msg.join('\n')+'</div>' )
                         // document.getElementById('system-message-container').innerHTML = '<div>'+msg.join('\n')+'</div>';
                         return false;*/
                     }
                    break
                default : JoomlaSubmitButtonClone (task)
            }
        };
    }
    this.onAddAreaBase = function (){
        var Data = JSON.parse( JSON.stringify( self.AjaxDefaultData ) );
        Data.task = 'onAjaxAddAreaBase' ;
        Data.view = 'setting_city' ;

        var AjaxPost = self.AjaxPost( Data )
        var getModal = self.__loadModul.Fancybox();

        Promise.all([AjaxPost , getModal  ]).then(function (DataPromise){
            var Html = DataPromise[0].data.form_html;
            var Modal = DataPromise[1]

            Modal.open( Html, {
                baseClass: "addFilterCitySeo form-add-area-base devBridge-AutoComplete", // Класс основного элемента
                touch: false,
                // Перед началом анимации открытия
                beforeShow: function (instance, current) {},

                // Когда контент загружен и анимирован
                afterShow: function (instance, current) {},

                /**
                 * Init AutoComplete
                 * ---
                 * для запуска этого метода параметр baseClass должен содержать класс "devBridge-AutoComplete"
                 * @param FancyBox - объект модального окна
                 * @param current - текущее модальное окно
                 */
                onInitAutoComplete : function ( FancyBox, current ) {
                    var $ = jQuery ,
                        $Form ,
                        $inputParentId ,
                        $AutoCompleteElem ;

                    // Находим требуемый элемент
                    $Form = $(current.$content[0]).find('#add-area-form')
                    $AutoCompleteElem = $Form.find('[name="jform[parent_area]"]')
                    $inputParentId = $Form.find('[name="jform[parent_id]"]')

                    Data.task = 'onAjaxGetParentsAreaAutoComplete' ;

                    // Запускаем devBridgeAutocomplete на найденном элементе
                    $AutoCompleteElem.devbridgeAutocomplete({
                        dataType : 'json',
                        serviceUrl: '/administrator/index.php?option=com_customfilters&view=setting_city',
                        params : Data ,
                        transformResult: function(response) {
                            return {
                                suggestions: response.data
                            };
                        }, 
                         
                        /**
                         * Функция обратного вызова вызывается, когда пользователь выбирает предложение из списка.
                         * this внутренний обратный вызов относится к вводу HtmlElement.
                         * @param suggestion
                         */
                        onSelect: function (suggestion) {
                            $inputParentId.val( suggestion.data )
                            // alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
                        }
                    });
                }
            });
            console.log('com_customfilters.administrator.core', DataPromise);


        },function (err){console.log(err)});
    }
    /**
     * Сохранить новый регион
     * @param el_btn
     */
    this.onSaveNewArea = function (el_btn){
        var $Form = $(el_btn).closest('form#add-area-form')
        var Data = JSON.parse( JSON.stringify( self.AjaxDefaultData ) );
        Data.task = 'onAjaxSaveNewArea' ;
        Data.view = 'setting_city' ;
        Data.formData = $Form.serialize() ;

        var AjaxPost = self.AjaxPost( Data )
        Promise.all([ AjaxPost  ,  ]).then(function (PromiseResult){
            var saveResult = PromiseResult[0] ;
                 console.log( 'com_customfilters.administrator.core::' , saveResult );
                self.renderMessages(saveResult.messages) ;
        },function (err){console.log(err)});
        console.log( 'com_customfilters.administrator.core::onSaveNewArea' , el_btn );
        
    }
    
    this.SettingFilterModal ;
    /**
     * Загрузить форму настроек фильтра 
     */
    this.onLoadSettingFilter = function (elem){

        var Data = JSON.parse(JSON.stringify( self.AjaxDefaultData ));
        Data.cid = + $(elem).closest('tr').find('input[name="cid[]"]').val() ;
        Data.task = 'onAjaxLoadSettingFilter' ;
        Data.view = 'setting_filter' ;
        Data.custom_id = $(elem).data('custom_id') ;


        var AjaxPost = self.AjaxPost( Data )
        var getModal = self.__loadModul.Fancybox();
        var Css = self.load.css('/administrator/components/com_customfilters/assets/css/setting_filter.css');

        Promise.all([ AjaxPost , getModal , Css  ]).then(function (DataPromise){
            var Html = DataPromise[0].data.html;
            self.SettingFilterModal = DataPromise[1]

            self.SettingFilterModal.open(Html, {
                baseClass: "editSettingFilter setting_filter_modal", // Класс основного элемента
                touch: false,
                // Перед началом анимации открытия
                beforeShow: function (instance, current) { },

                // Когда контент загружен и анимирован
                afterShow: function (instance, current) {
                    self.checkBoxRadioInit('.setting_filter_modal');
                    // var $select = $('.editSettingFilter').find('select').chosen();
                    // console.log( 'com_customfilters.administrator.core::beforeShow' , $select );



                    // Modal.setTimeOut(8000); // Окно будет закрыто через 8 секунд
                },
            });
            console.log('com_customfilters.administrator.core', DataPromise);


        },function (err){console.log(err)});

        console.log( 'com_customfilters.administrator.core::onLoadSettingFilter' , elem );
        console.log( 'com_customfilters.administrator.core::onLoadSettingFilter' , Data );
        
    }
    /**
     * Сохранение формы Параметров фильтра
     *
     */
    this.onSaveSettingFilter = function (){
        var $Form = $('#setting_filter')
        var Data = JSON.parse(JSON.stringify( self.AjaxDefaultData ));
        Data.formData = $Form.serialize();
        Data.task = 'setting_filter.save' ;
        Data.view = 'setting_filter' ;
        self.SettingFilterModal
        var AjaxPost = self.AjaxPost( Data );
        Promise.all([ AjaxPost ]).then(function (DataPromise){
            var resultAjax = DataPromise[0];
            self.renderMessages(resultAjax.messages)
        });
    }
    /**
     * Ввод текста в поле - получение Translite
     * @param event
     */
    this.onKeyupSetTranslite  = function (event) {
        var $sefAlias = $(event.target).closest('div.subform-repeatable-group').find('input.sef_alias')
        var Data = JSON.parse(JSON.stringify(self.AjaxDefaultData));
        Data.view = 'setting_city';
        Data.task = 'onKeyupSetTranslite';
        Data.val = event.target.value;
        self.AjaxPost(Data).then(function (r) {
            $sefAlias.val(r.data)
        }, function (err) {
            console.log('com_customfilters.administrator.core', err);
        });
    }
    /**
     * Событие снятие с публикации региона
     * @param target
     */
    this.onChangeCityPublished = function (target){
        var $form = $(target).closest('div#form_content')
        var changeValue = +$(target).val();
        var parentAlias = $(target).data('parent-alias')
        if ( parentAlias.length ){
            var $parentGroup = $form.find('div.accordion-group.' + parentAlias );
            var $parentHeading = $parentGroup.children('div.accordion-heading');
            var $parentUseRadio = $parentHeading.find('input[type="radio"]')

            // disabled - для родительского региона
            $parentUseRadio.attr("disabled",true) ;
        }



        console.log( 'com_customfilters.administrator.core' , parentAlias );
        
        var $accordionGroup = $(target).closest('.accordion-body');
        var $accordionHeading = $accordionGroup.find('.accordion-heading');

        var $accordionBody = $(target).closest('.accordion-body');


        console.log( 'com_customfilters.administrator.core $accordionHeading' , $accordionHeading );

        if ( !changeValue ) self._cleanChildrenArea( target ) ;


        // accordion-body
        console.log( 'com_customfilters.administrator.core' , target ); 

        var $Form = $(target).closest('form');

        var Data = JSON.parse(JSON.stringify( self.AjaxDefaultData ));
        Data.jform = $Form.serialize();
        // Data.view = 'forms_add' ;
        Data.view = 'setting_city' ;
        // Data.task = 'onAjaxSaveForm' ;
        Data.task = 'save' ;
        Data.layout = 'on_ajax_save_form' ;
        self.AjaxPost( Data ).then(function (r){
            if ( r.success ){
                $Form.find('#jform_id').val( r.data.id );
                console.log( 'com_customfilters.administrator.core' , $Form ); 

            }
            console.log( 'com_customfilters.administrator.core' , r );
            
        },function (err){console.log(err)});

        
    }
    /**
     * Удалить дочерние регионы
     * @param target
     * @private
     */
    this._cleanChildrenArea = function ( target ){
        var $accordionGroup = $(target).closest('div.accordion-group');
        $accordionGroup.find('.accordion-heading + .accordion-body').collapse('hide');
        var $accordionInner = $accordionGroup.find('div.accordion-inner');

        console.log( 'com_customfilters.administrator.core' , $accordionInner );
        
        if ( $accordionGroup.hasClass('is_open')){
            $accordionInner.find('div.accordion').remove();
            $accordionGroup.removeClass('is_open')

        }
    }
    /**
     * Загрузить дочерние регионы
     * @param target
     */
    this.loadChildrenArea = function (target){
        var $Form = $(target).closest('form');
        var $accordionGroup = $(target).closest('div.accordion-group');
        var $accordionInner = $accordionGroup.find('div.accordion-inner');

        if ( $accordionGroup.hasClass('is_open')){
            self._cleanChildrenArea( target )
            return ;
        }
        var Data = JSON.parse(JSON.stringify( self.AjaxDefaultData ));
        Data.view = 'setting_city' ;
        Data.task = 'onAjaxGetChildrenArea' ;
        Data.layout = 'add_city_seo_cities_settings' ;
        Data.id = $Form.find('input#jform_id').val() ;
        Data.parentRegion = $accordionGroup.find('input.city_setting_city_id').val();
        Data.parentAlias = $accordionGroup.find('input.city_setting_city_alias').val();
        Data.parentName = $accordionGroup.find('fieldset input[type="radio"]:checked').attr('name');
        
        console.log( 'com_customfilters.administrator.core' , Data.parentName );
        
        
        self.AjaxPost( Data ).then(function (r){
            $accordionInner.append(r.data.form_html);
            $accordionGroup.addClass('is_open');
        },function (err){console.log(err)});
        console.log( 'com_customfilters.administrator.core target' , target ); 
        
    }
    /**
     * Создание фильтра - "Отбор по городам"
     */
    this.addFilterCitySeo = function (){
        var Data = JSON.parse(JSON.stringify( self.AjaxDefaultData ));
        Data.task = 'onAjaxGetFormAddFilterCitySeo' ;
        Data.view = 'setting_city' ;
        // Data.layout = 'default' ;

        var AjaxPost = self.AjaxPost( Data )
        var getModal = self.__loadModul.Fancybox();

        // var loadCss = self.load.css('/administrator/components/com_customfilters/assets/css/formCitySeo.css');
        var subformRepeatable = self.load.css('/media/system/js/subform-repeatable.js');
        Promise.all([AjaxPost , getModal , subformRepeatable /*, loadCss*/ ]).then(function (DataPromise){
            var Html = DataPromise[0].data.form_html;
            var Modal = DataPromise[1]

            Modal.open(Html, {
                baseClass: "addFilterCitySeo setting_city_modal", // Класс основного элемента
                touch: false,
                // Перед началом анимации открытия
                beforeShow: function (instance, current) {},

                // Когда контент загружен и анимирован
                afterShow: function (instance, current) {
                    var $subForm = $('.setting_city_modal').find('div.subform-repeatable')
                    console.log( 'com_customfilters.administrator.core' , $subForm );



                    // Modal.setTimeOut(8000); // Окно будет закрыто через 8 секунд
                },
            });
            console.log('com_customfilters.administrator.core', DataPromise);


        },function (err){console.log(err)});

        /*self.AjaxPost( Data ).then(function (r){
            console.log( 'customfilters.administrator.core' , r );

        },function (err){console.log(err)});*/
    }
    /**
     * Изменения запрет для фильтра - генерить страницы результата поиска с robot INDEX
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
        Data.tbl = El.dataset.tbl ;

        self.AjaxPost( Data ).then(function (r){
            self.renderMessages(r.messages)
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
                    console.log( 'com_customfilters.administrator.core' , r ); 
                    
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
















