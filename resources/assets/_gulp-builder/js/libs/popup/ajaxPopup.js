import { Popup } from './popup';


export class AjaxPopup extends Popup {

    openPopup(){
            $.magnificPopup.open({
                items:{
                    type: 'ajax',
                    src: this.popupAjaxUrl
                }
            });
        }

    closePopup(){
        $.magnificPopup.close();
    }

}