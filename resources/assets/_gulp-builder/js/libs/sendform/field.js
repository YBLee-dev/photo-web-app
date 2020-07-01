/**
 * Class Field.
 */
export default class Field{
    constructor(field, state, reference, settings, position) {
        this.field = field;
        this.reference = Object.assign({}, reference);
        this.isValid = true;
        this.type = this.field.getAttribute('type');
        this.isRequired = this.field.hasAttribute('required');
        this.firstCheck = true;
        this.settings = settings;
        this.regularExp = this.field.getAttribute('pattern');
        this.position = position;

        this.removeRequired();
        this.onKeyUp();
        this.onChange();
        if(this.regularExp != null){
            this.createValidation();
        }
    }

    createValidation(){
        this.field.removeAttribute('pattern');
        if(this.reference[this.type] == undefined || null){
            this.reference[this.type] = ['regExp'];
            return;
        }
        this.reference[this.type] = [...this.reference[this.type], 'regExp'];
    }

    removeRequired(){
        if(!this.isRequired) return;

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
        if(!this.needValidate()){
            this.removeError();
            return;
        }

        let value = this.field.value;
        let validationFunc = this.reference[this.type];
        let valid = true;

        if(validationFunc == undefined){
            validationFunc = this.reference['required'];
        }
        validationFunc.forEach((func)=> {
            let result = this[func](value);
            valid = valid * result;
        });

        if(valid){
            this.removeError();
        }
        else{
            this.addError();
        }
        this.isValid = valid;
        return {
            valid: this.isValid,
            position : this.position
        };
    }

    addError(){
        this.field.classList.add(this.settings.errorClass);
    }

    removeError(){
        this.field.classList.remove(this.settings.errorClass);
    }

    needValidate(){
        let noEmpty = this.field.value != 0;

        if(this.isRequired){
            return true;
        }

        if(noEmpty && this.type == 'email'){
            return true;
        }

        if(noEmpty && this.regularExp != null){
            return true;
        }

        return false;
    }

    resetSelf(){
        this.field.value = '';
        this.field.checked = false;
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

    isChecked(){
        if(this.field.checked){
            return true;
        }
        return false;
    }

    //Is cyrillic testing
    isCyr(val) {
        var cyr = /[\u0400-\u04FF]/gi;
        return cyr.test(val);
    }

    isCheckedRadio(){
        let collection = document.querySelectorAll(`input[name=${this.field.name}]`);
        let isSomeChecked = false;
        collection.forEach( el =>{
            if(el.checked) isSomeChecked = true;
        });
        return isSomeChecked;
    }

    regExp(val){
        let reqular = new RegExp(this.regularExp);
        return reqular.test(val);
    }
}