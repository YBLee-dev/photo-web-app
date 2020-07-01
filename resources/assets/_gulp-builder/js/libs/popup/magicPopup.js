import { Popup } from './popup';

const defaultSettings = {
    popupClass : 'js_magic-popup',
    bgId : 'js_magic-popup-bg'
};

export class MagicPopup extends Popup{
    /**
     * @param openClass
     * @param closeClass
     * @param data
     */
    constructor(openClass, closeClass, data = {}){
        super(openClass, closeClass);
        data = Object.assign(defaultSettings, data);
        //popup class
        this.popupClass = data.popupClass;
        //bg class
        this.bgId = data.bgId ;
        // Node for popup
        this.popup = $(`<div class="${this.popupClass}"></div>`);
        //Node for background
        this.bg = $(`<div id="${this.bgId}"></div>`);
        this.initMagicPopup()
    }

    initMagicPopup(){
        // Close on bg
        $('body')
            .on('click', `#${this.bgId}`, (e) => {
                this.closePopup();
            })
            // Close on modal which does not exist modal window
            .on('click', `.${this.popupClass}`, (e)=> {
                if($(e.target).hasClass(this.popupClass)){
                    this.closePopup();
                }
            })
    }
    openPopup(){
        $(this.popup).append($(this.popupId).show());
        $('body').append(this.bg).append(this.popup);
    }
    closePopup(){
        $(this.popup).remove();
        $(this.bg).remove();
    }
}