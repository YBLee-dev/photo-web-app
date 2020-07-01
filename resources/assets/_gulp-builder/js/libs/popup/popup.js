/*POPUP*/
import { Component } from '../../components/component';

export class Popup extends Component{
    /**
     *
     * @param callClass{string} - class on open popup
     * @param closeClass{string} - class on close
     */
    constructor(callClass, closeClass){
        super(callClass);
        // check on parent errors
        if(this.errors) return ;
        // get url of popup if exist
        this.popupAjaxUrl = this.element.data('url');
        // get id of popup if exist
        this.popupId = this.element.data('popup');
        this.closeClass = closeClass ? closeClass : '.js_popup-close';
        // if url for ajax does not exist check
        // for existing id of modal
        if(!this.popupAjaxUrl) {
            this.checkOnExistPopupElements();
        }
        if(this.errors) return ;
        this.dataPush = null;
        this.identificator = callClass;
        this.initPopup();
    }

    /**
     * Init all settings for popup
     */
    initPopup(){
        const _this = this;
        // On click open popup
        $('body')
            .on('click', this.identificator, function (event) {
                event.preventDefault();
                // get data for hidden inputs
                _this.dataPush = $(this).data('info');

                if(_this.dataPush) _this.pushData();
                this.dataPush = null;
                _this.openPopup()
            })
            // On close popup
            .on('click', this.closeClass, function (event) {
                event.preventDefault();
                _this.closePopup();
            });
    }

    /**
     * Preparing and add data for hidden inputs into forms
     */
    pushData(){
        if(!this.validateData(this.dataPush)) return;
        let arr = this.breakStr();
        let form = $(this.popupId).find('form');

        if(typeof(arr[0]) === 'string'){
            form.append(`<input type="hidden" name="${arr[0]}" value="${arr[1]}">`);
            return;
        }
        arr.forEach((el)=>{
            console.log(el);
            form.append(`<input type="hidden" name="${el[0]}" value="${el[1]}">`);
        });
    }

    /**
     * Validating data for hidden inputs
     *
     * @param str
     * @returns {boolean}
     */
    validateData(str){
        if(typeof(str) !== 'string'){
            console.warn(` Warning in Popup ${this.identificator}:the value ${str} should be string `);
            return false;
        }
        if(str.indexOf(':') < 0){
            console.warn(` Warning in Popup ${this.identificator}:the value ${str} should contain ":"`);
            return false;
        }

        return true;
    }

    /**
     * Break string for array
     *
     * @returns {Array}
     */
    breakStr(){
        if(this.dataPush.indexOf(',') < 0 ){
            return this.dataPush.split(':');
        }

        return this.dataPush.split(',').map((el)=>{
            if(this.validateData(el)){
                return el.split(':');
            }
            return [];
        });

    }

    openPopup() {
        console.warn('Warn in Popup: you use default function of open popup, you should redeclare it');
    }

    closePopup() {
        console.warn('Warn in Popup: you use default function of close popup, you should redeclare it');
    }

    checkOnExistPopupElements(){
        if(this.checkOnExistElement(this.closeClass)){
            console.warn(`on ${this.identificator} wrong close class element ${this.closeClass}`);
        }
        if (!this.checkOnExistElement(this.popupId)) {
            console.warn(`on ${this.identificator} do not set or wrong set data-popup or data-url`);
        }
    }
}