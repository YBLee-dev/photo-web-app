import { Popup } from './popup';

export class MfPopup extends Popup {
    openPopup() {
        $.magnificPopup.open({
            showCloseBtn: false,
            type: 'inline',
            tLoading: 'Загрузка...',
            items: {
                src: this.popupId
            }
        });
    }

    closePopup() {
        $.magnificPopup.close();
    }
}