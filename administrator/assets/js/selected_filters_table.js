/***********************************************************************************************************************
 * ╔═══╗ ╔══╗ ╔═══╗ ╔════╗ ╔═══╗ ╔══╗  ╔╗╔╗╔╗ ╔═══╗ ╔══╗   ╔══╗  ╔═══╗ ╔╗╔╗ ╔═══╗ ╔╗   ╔══╗ ╔═══╗ ╔╗  ╔╗ ╔═══╗ ╔╗ ╔╗ ╔════╗
 * ║╔══╝ ║╔╗║ ║╔═╗║ ╚═╗╔═╝ ║╔══╝ ║╔═╝  ║║║║║║ ║╔══╝ ║╔╗║   ║╔╗╚╗ ║╔══╝ ║║║║ ║╔══╝ ║║   ║╔╗║ ║╔═╗║ ║║  ║║ ║╔══╝ ║╚═╝║ ╚═╗╔═╝
 * ║║╔═╗ ║╚╝║ ║╚═╝║   ║║   ║╚══╗ ║╚═╗  ║║║║║║ ║╚══╗ ║╚╝╚╗  ║║╚╗║ ║╚══╗ ║║║║ ║╚══╗ ║║   ║║║║ ║╚═╝║ ║╚╗╔╝║ ║╚══╗ ║╔╗ ║   ║║
 * ║║╚╗║ ║╔╗║ ║╔╗╔╝   ║║   ║╔══╝ ╚═╗║  ║║║║║║ ║╔══╝ ║╔═╗║  ║║─║║ ║╔══╝ ║╚╝║ ║╔══╝ ║║   ║║║║ ║╔══╝ ║╔╗╔╗║ ║╔══╝ ║║╚╗║   ║║
 * ║╚═╝║ ║║║║ ║║║║    ║║   ║╚══╗ ╔═╝║  ║╚╝╚╝║ ║╚══╗ ║╚═╝║  ║╚═╝║ ║╚══╗ ╚╗╔╝ ║╚══╗ ║╚═╗ ║╚╝║ ║║    ║║╚╝║║ ║╚══╗ ║║ ║║   ║║
 * ╚═══╝ ╚╝╚╝ ╚╝╚╝    ╚╝   ╚═══╝ ╚══╝  ╚═╝╚═╝ ╚═══╝ ╚═══╝  ╚═══╝ ╚═══╝  ╚╝  ╚═══╝ ╚══╝ ╚══╝ ╚╝    ╚╝  ╚╝ ╚═══╝ ╚╝ ╚╝   ╚╝
 *----------------------------------------------------------------------------------------------------------------------
 * @author Gartes | sad.net79@gmail.com | Skype : agroparknew | Telegram : @gartes
 * @date 29.11.2020 03:00
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
/**
 * Doc chosen - https://harvesthq.github.io/chosen/
 * Subform  -   https://docs.joomla.org/Subform_form_field_type
 *
 *  Subform Events - subform-ready | subform-row-add | subform-row-remove
 *
 */
/* global jQuery , Joomla   */
window.selected_filters_table = function () {
    var $ = jQuery;
    var self = this;
    // Домен сайта
    var host = Joomla.getOptions('GNZ11').Ajax.siteUrl;
    // Медиа версия
    var __v = '?rm=';
    var passiveSupported = false;
    try {
        window.addEventListener( "test", null,
            Object.defineProperty({}, "passive", { get: function() { passiveSupported = true; } }));
    } catch(err) {}
    this.__type = false;
    this.__plugin = false;
    this.__name = false;
    this._params = {
        __module: false,
        RecentlyViewed : false ,
    };
    // Ajax default options
    this.AjaxDefaultData = {
        group   : null ,
        plugin  : null ,
        module  : null ,
        method  : null ,
        option  : 'com_customfilters' ,
        view    : 'setting_seo' ,
        format  : 'json' ,
        task    : 'onAjaxGetAllValueField' ,
    };
    // Default object parameters
    this.ParamsDefaultData = {
        // Медиа версия
        __v: '1.0.0',
        // Режим разработки
        development_on: false,
    }
    this.useChosen = true ;
    /**
     * Start Init
     * @constructor
     */
    this.Init = function () {

        self.chosenInit();

        this._params = Joomla.getOptions('selected_filters_table', this.ParamsDefaultData);
        __v = self._params.development_on ? '' : '?v=' + self._params.__v;

        // Параметры Ajax Default
        this.setAjaxDefaultData();
        this.addEvtListener();
        this.InitEditForm();

        self.filterNameFields();
        self.URL.onChangeData();

        // self.onVmCategoryChange({ target : 'select[name="jform[vmcategory_id]"]' })

    };

    /**
     * Добавить слушателей событий
     */
    this.addEvtListener = function () {
        // изменение названия поля
        $('div.subform-repeatable-wrapper')
            .on('change' , '.subform-repeatable-group select.title_field_list' , self.onTitleFieldList )
            .on('change' , '.subform-repeatable-group select.title_field_list , .subform-repeatable-group select.list-value' , self.URL.onChangeData )

        $('select[name="jform[vmcategory_id]"]').on('change' , self.onVmCategoryChange )

        // Добавление поля формы
        $(document).on('subform-row-add',  self.onSubformRowAdd ) ;
        $(document).on('subform-row-remove',  function (){
            console.log( 'on','subform-row-remove' );
            setTimeout( self.filterNameFields  ,1 )
            setTimeout( function (){
                self.URL.onChangeData(1)
            }  , 1 )

        } ) ;
    }


    this.CategorySlug = null ;
    /**
     * Изменение категории
     */
    this.onVmCategoryChange = function (e){

        var Data = {
            catId : $( e.target ).val() ,
            task : 'onAjaxChangeCategory' ,
        }
        if ( !Data.catId ) return ;

        self.AjaxPost(Data).then(function (res){
            var slug = res.data.category.slug
            self.CategorySlug = '/filter/' + slug + '/'
            $('#jform_url_params').val( self.CategorySlug )

            console.log( res );
        },function (err){console.log( err );})

    }
    /**
     * строка url
     * @type {{print: window.url.print, _update: window.url._update, data: *[], onchangedata: window.url.onchangedata}}
     */
    this.URL = {
        Data : [] ,
        onChangeData : function (e){
            if ( typeof e === "undefined" ) return ;
            self.URL.Data = [] ;
            self.URL._update();
            self.URL._buildUrl();



        },
        _buildUrl : function (){
            var connector = '?' ;
            var sringUrl = '' ;
            var urlParamCount = 0 ;
            self.URL.print();
            self.URL.Data.forEach(function(item, iU, arr) {

                if ( urlParamCount ) sringUrl += '&'

                for (var i = 0 ; i< item.valueField.length ; i++ ){

                    if ( !sringUrl.length  ) {
                        sringUrl += '?'
                    }else{
                        sringUrl += '&'
                    }
                    sringUrl += 'custom_f_' + item.optId

                    if ( item.conditionalOperator === 'AND' ){
                        sringUrl += '['+i+']';
                    }

                    sringUrl += '=' + item.valueField[i]
                }
                console.log( item );
            });

            $('#jform_url_params').val( self.CategorySlug + sringUrl );
            console.log( sringUrl );
        },
        print : function (){
            console.log( self.URL.Data );
        },
        _update : function (){

            var $subformRepeatableGroups = $('#adminForm .subform-repeatable .subform-repeatable-group');
            for ( var i = 0; i < $subformRepeatableGroups.length ; i++ ){
                var $lineField = $( $subformRepeatableGroups[i] );
                var $fieldTitle = $lineField.find('select.title_field_list > option:selected')

                var orderingItem = $fieldTitle.attr('data-ordering');

                var valueArr = [];
                var $ValueData = $fieldTitle.closest('.subform-repeatable-group').find('select.list-value > option:selected')
                if ($ValueData.length){
                    for (vI = 0; vI<$ValueData.length;vI++){
                        valueArr[vI] = $($ValueData[vI]).val()
                    }
                }
                self.URL.Data[orderingItem] = {
                    optText : $fieldTitle.text(),
                    optId : $fieldTitle.val(),
                    conditionalOperator : $fieldTitle.attr('data-conditional_operator'),
                    valueField : valueArr ,
                }
            }

        }
    }
    /**
     * Инициализация формы редактирования.
     * @constructor
     */
    this.InitEditForm = async function () {
        var id = $('form#adminForm').find('input#jform_id').val();
        if (!id) return;
        $subformRepeatableGroup = $('.subform-repeatable-group')
        var $select = $subformRepeatableGroup.find('.title_field_list');

        // Подгрузить значения полей в соответствии м выбранным полем
        forRepeatable( 0 )
        function forRepeatable (i ){
            self.onTitleFieldList({ target: $select[i] }).then(function (a){
                if ( $select[i+1] ){
                   forRepeatable( i+1 )
                } else{
                     setDataValue()

                }
            },function (err){});
        }

        /**
         * Отметить выбранные option
         */
        function setDataValue(){
            var indexGroup = 0 ;

            for ( var key in self._params.selected_filters_table){
                var $group = $($subformRepeatableGroup[indexGroup]);
                var $select =  $group.find('select.list-value')
                self.chosenUpdated($select);
                var paramValue = self._params.selected_filters_table[key].value

                for ( pI = 0; pI < paramValue.length; pI++ ){
                    var $options = $select.find('option')
                    $select.find('option[value="'+paramValue[pI]+'"]').prop('selected', true)
                }
                self.chosenUpdated($select)
                self.chosenReinit($select)
                indexGroup++;
            }

        }
    }

    /**
     * Добавить значения поля в select
     * @param event
     */
    this.onTitleFieldList = async function (event){
        return new Promise(function(resolve, reject) {
            var $el = $(event.target);
            var $optionSelected = $el.find('option:selected');
            var Data = {
                val : $optionSelected.val(),
                task : 'onAjaxGetAllValueField',
            }
            self.AjaxPost(Data).then(function (r) {
                /**
                 * Параметр сравнения "AND" - "OR"
                 * @type {string}
                 */
                var conditionalOperator = $optionSelected.data('conditional_operator');
                var $selected_filters_table = $el.closest('div.subform-repeatable-group').find('select.list-value')
                $selected_filters_table.empty() ;

                var valList = r.data.valList ;
                for ( let key in valList ) {
                    setOptions( valList[key] , $selected_filters_table )
                }

                $selected_filters_table.chosen("destroy");
                if ( conditionalOperator === 'AND' ) {
                    $selected_filters_table.attr('multiple','multiple');
                }else{
                    $selected_filters_table.removeAttr('multiple' );
                }
                self.chosenInit( $selected_filters_table ) ;
                $selected_filters_table.removeAttr('disabled');

                self.chosenUpdated();
                // option которые уже выбранные - делаем недоступными в других подформах
                self.filterNameFields();

                resolve()
            }, function (err) {
                console.log(err);
                reject()
            })
        });


        function setOptions(obj, $selected_filters_table) {
            if ( !$selected_filters_table.children().length ) {
                // $($('<option />', { })).appendTo($selected_filters_table)
            }

            $($('<option />', {
                'value': obj.id,
                'text': obj.name
            })).appendTo($selected_filters_table)
            return true ;
        }
    }
    /**
     * option которые уже выбранные - делаем недоступными в других подформах
     */
    this.filterNameFields = function (){
        resetDisabled()

        var $selectAll = $('select.title_field_list');
        var $selectAllClone = $selectAll ;


        for (var i = 0; i < $selectAll.length; i++ ){
            var $currentSelectOption = $( $selectAll[i] ).find('option:selected')
            var currentSelectOptionIndex = $currentSelectOption.index();
            // var optionText = $currentSelectOption.text();
            // console.log( optionText );

            for ( cI = 0 ; cI < $selectAllClone.length ; cI++ ){
                if ( cI === i ) continue ;
                var $_select = $( $selectAllClone[cI] )
                $_select.find('option:eq(' + currentSelectOptionIndex + ')' )
                    .attr('disabled','disabled')
                self.chosenUpdated( $_select )
            }
        }
        function resetDisabled(){
            $('select.title_field_list').find('option:disabled').removeAttr('disabled')

        }

    }

    /**
     * Событие добавление строки в Subform
     * @param event
     * @param row
     */
    this.onSubformRowAdd = function (event, row){
        var subformRepeatable
        self.filterNameFields();

        self.chosenInit();
        self.chosenUpdated();
    }

    /**
     *
     * @param $el
     */
    this.chosenInit = function($el){
        if ( !self.useChosen ) return ;
        if ( typeof $el === "undefined" )  $el = $("select");
        $el.chosen({disable_search_threshold: 10});
    }
    /**
     *
     * @param $el
     */
    this.chosenUpdated = function($el){
        if ( !self.useChosen ) return ;
        if ( typeof $el === "undefined" ) $el = $("select")
        $el.trigger("chosen:updated").trigger("liszt:updated");
    }
    /**
     *
     * @param $el
     */
    this.chosenReinit = function ($el){
        if ( !self.useChosen ) return ;
        if ( typeof $el === "undefined" ) $el = $("select")
        $el.chosen("destroy");
        self.chosenInit( $el )
    }
    /**
     * Отправить запрос
     * @param Data - отправляемые данные
     * Должен содержать Data.task = 'taskName';
     * @returns {Promise}
     * @constructor
     */
    this.AjaxPost = function (Data) {
        var data = $.extend(true, this.AjaxDefaultData, Data);
        return new Promise(function (resolve, reject) {
            self.getModul("Ajax").then(function (Ajax) {
                // Не обрабатывать сообщения
                Ajax.ReturnRespond = true;
                // Отправить запрос
                Ajax.send(data, self._params.__name).then(function (r) {
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
        this.AjaxDefaultData.group = this._params.__type;
        this.AjaxDefaultData.plugin = this._params.__name;
        this.AjaxDefaultData.module = this._params.__module ;
        this._params.__name = this._params.__name || this._params.__module ;
    }
    this.Init();
};

(function (){
    if (typeof window.GNZ11 === "undefined"){
        // Дожидаемся события GNZ11Loaded
        document.addEventListener('GNZ11Loaded', function (e) {
            start()
        }, false);
    } else {
        start()
    }
    // Start prototype
    function start(){
        window.selected_filters_table.prototype = new GNZ11();
        window.Mod_jshopping_slider_module = new window.selected_filters_table();
    }
})()
















