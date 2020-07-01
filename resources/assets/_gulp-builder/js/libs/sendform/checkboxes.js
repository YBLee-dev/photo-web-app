/**
 * Class Checkbox.
 */
export default class Checkbox{
    constructor(field, state, reference, settings, parentClass) {
        this.field = field;
        this.reference = reference;
        this.isValid = true;
        this.type = field.getAttribute('type');
        this.firstCheck = true;
        this.settings = settings;
        this.removeRequired();
        this.onKeyUp();
        this.onChange();
        this.parentClass = parentClass;
    }

    removeRequired(){
        this.field.removeAttribute("required");
        this.field.classList.add(this.settings.requiredClass);
    }

    onKeyUp(){
        this.field.addEventListener('keyup', (el)=> {
            if(this.firstCheck) return;
            let value = el.target.value;
            this.validate(value);
        });
    }

    onChange(){
        this.field.addEventListener('change', (el)=> {
            let value = el.target.value;
            this.validate(value);
            this.firstCheck = false;
        });
    }

    validate() {
        let value = this.field.value;
        let validationFunc = this.reference[this.type];
        if(validationFunc == undefined) validationFunc = ['isEmpty'] ;
        validationFunc.forEach((func)=> {
            let result = this[func](value);
            this.isValid = this.isValid * result;
        });

        if(this.isValid){
            this.field.classList.remove(this.settings.errorClass);
        }
        else{
            this.field.classList.add(this.settings.errorClass);
        }
        this.parentClass.eventField(this.isValid);
        this.isValid = true;
    }


//Is on no empty value testing
    isEmpty(val) {
        if (val == '') {
            return false;
        } else {
            return true;
        }
    }

    //Is email testing
    isEmail(val) {
        var email = /^[-\w.]+@([A-z0-9]+\.)+[A-z]{2,4}$/;
        return email.test(val);
    }

    //Is url testing
    isUrl(element) {
        var url = /[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,}/;
        return url.test($(element).val());
    }

    //Is min 5 charachter
    minLength(val) {
        if (val.length > 5)
            return true;
    }

    //Form reset
    resetForm(input_form) {
        var form = $(input_form);
        form.find('input[type=text],input[type=tel],input[type=email],textarea').val('');
        form.find('input:checkbox, input:radio').removeAttr('checked');
    }

    //Is cyrillic testing
    isCyr(val) {
        var cyr = /[\u0400-\u04FF]/gi;
        return cyr.test(val);
    }
}