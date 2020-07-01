import Field from './field'

export default class Form {
    /**
     * @param formElement {Element} - class of form.
     * @param settings {Object} - settings object.
     * @param reference {Object} - reference for validation.
     */
    constructor (formElement, settings, reference) {
        this.form = this.form = document.querySelector(formElement);
        if (this.form == null || undefined) return
        this.inputs = Array.from(this.form.querySelectorAll('input:not([type="hidden"]), select, textarea'))
        /**
         * Set action(url for request)
         */
        let action = this.form.getAttribute('action')

        this.action = action != null ? action : '/'

        this.dispalyStatus = true

        // Form state if it contain errors
        this.state = true
        //determine if there are any mistakes now.
        this.error = false
        // Show spunner activity
        this.isSpinnerActive = false
        // Text of status field.
        this.statusText = null
        // Contain all field of this form
        this.items = []
        // Contain errors field with position
        this.errorItems = {}
        // settings
        let customSettings = {
            resetAfterSubmit: true,
            onlyValidate: false,
            statusClass: 'form-status',
            statusErrorClass: 'with_error',
            statusSuccessClass: 'with_success',
            errorClass: 'error',
            successClass: 'success-valid',
            validateClass: '.js_sendform-validate',
            requiredClass: 'form-required',
            modalOpen: true,
            modalId: '#thanks',
            msgSend: '',
            msgDone: 'Done',
            msgError: 'Sending error',
            msgValError: 'One of required field is empty',
            spinnerColor: '#000',
            formPosition: 'relative',
            resetClass: '.js_senform-reset',
            method: 'POST',
            sendAllCheckbox: false,
            success: data => {
                this.successSubmit()
            },
            error: data => {
                this.errorSubmit()
            },
            validationSuccess: () => {},
            validationError: () => {
                this.validationErrorCallback()
            }
        }
        // validation rules
        let customReference = {
            email: ['isEmail', 'isEmpty'],
            text: ['isEmpty'],
            textarea: ['isEmpty'],
            phone: ['minLength'],
            required: ['isEmpty'],
            checkbox: ['isChecked'],
            radio: ['isCheckedRadio'],

        }

        this.settings = Object.assign({}, customSettings, settings)
        this.reference = Object.assign({}, customReference, reference)

        this.onInit()
    }

    /**
     * On initialize class.
     * Creating all inputs of this form.
     * if setting for only validate true init this func.
     * else init function on submitting.
     * creating status text field.
     * init function for reset field.
     */
    onInit () {
        this.createInputsValidate()

        if (this.settings.onlyValidate) {
            this.onValidate()
        }
        else {
            this.form.addEventListener('submit', event => {
                event.preventDefault()
                this.preSubmit()
            })
        }
        this.dispalyStatus = $(this.form).data('status') === undefined ? true : $(this.form).data('status')

        if (this.dispalyStatus) {
            this.createStatusField()
        }
        this.onReset()
    }

    /**
     * Creating for each input, select, checkboxes own class.
     * And pushing this classes into array.
     */
    createInputsValidate () {
        this.inputs.forEach((el, i) => {
            let item = new Field(el, this.state, this.reference, this.settings, i)
            this.items.push(item)
        })
    }

    /**
     * Create hidden field for status text.
     */
    createStatusField () {
        if (this.form.querySelector(`.${this.settings.statusClass}`) !== null) {
            this.statusText = this.form.querySelector(`.${this.settings.statusClass}`)
            return
        }
        var div = document.createElement('div')
        div.innerHTML = ''
        div.classList.add(this.settings.statusClass)
        this.form.appendChild(div)
        this.statusText = this.form.querySelector(`.${this.settings.statusClass}`)
    }

    /**
     * checking on error.
     * prepare for submitting:
     * add spinner, add status text.
     * call submit function.
     */
    preSubmit () {
        this.validateField()
        if (!this.state) {
            this.errorOnForm()
            return
        }
        let checkbox = Array.from(this.form.querySelectorAll('input[type="checkbox"]'))
        checkbox.forEach((item) => {
            if (item.checked) {
                item.value = 1
            }
            if (this.settings.sendAllCheckbox && !item.checked) {
                item.value = 0
            }
        })

        this.error = false
        if (!this.isSpinnerActive) this.addSpinner()
        if (this.dispalyStatus) this.statusText.innerHTML = this.settings.msgSend
        this.submitData()
    }

    /**
     * Foreach in all items call validation function.
     * @param result {object} - variable keep return from
     * validation function.Object contain 2 attr
     * result.valid {boolean} -show is field pass validation.
     * result.position {string} - position of field.
     *
     */
    validateField () {
        let localState = true
        this.items.forEach((item) => {
            let result = item.validate()
            if (result == undefined) return
            localState = localState * result.valid

            if (localState) {
                delete this.errorItems[result.position]
            }
            else {
                this.errorItems[result.position] = false
            }
        })

        this.state = localState
        if (this.state) {
            this.removeStatusText()
        }
    }

    /**
     * Call reset method on all items.
     */
    resetField () {
        this.items.forEach((item) => {
            item.resetSelf()
        })
    }

    /**
     * Adding spinner.
     */
    addSpinner () {
        let div = document.createElement('div')
        div.innerHTML = '<div class="form-loading"></div>'
        div.id = 'formsendHover'
        //this.form.appendChild(div)
        document.body.appendChild(div);
        this.isSpinnerActive = true
    }
    /**
     * Removing spinner.
     */
    removeSpinner () {
        if (!document.querySelector('#formsendHover')) return
        document.querySelector('#formsendHover').remove()
        this.isSpinnerActive = false
    }

    /**
     * init validation by press on btn.
     */
    onValidate () {
        let validateBtn = this.form.querySelector(this.settings.validateClass)
        validateBtn.addEventListener('click', (event) => {
            event.preventDefault()
            this.validateField()
            if (this.state) {
                this.settings.validationSuccess()
                return
            }
            this.settings.validationError()
        })
    }

    /**
     * init function reseting by press btn.
     */
    onReset () {
        let resetClass = this.form.querySelector(this.settings.resetClass)
        if (resetClass == null || undefined) return
        resetClass.addEventListener('click', () => {
            this.resetField()
        })
    }

    /**
     * Add text and set error on true.
     * And add text error.
     */
    errorOnForm () {
        if (this.error) return
        this.error = true
        if (this.dispalyStatus) {
            this.statusText.innerHTML = this.settings.msgValError
            this.statusText.classList.add('with_error')
        }
    }

    /**
     * On error validation
     */
    validationErrorCallback () {
        if (this.dispalyStatus) {
            this.errorStatusClass()
            this.printText(this.settings.msgValError)
        }
    }

    /**
     * Set text in status in form.
     * @param text{string}
     */
    printText (text) {
        this.statusText.innerHTML = text
    }

    /**
     * Clean status text
     */
    removeStatusText () {
        if (this.dispalyStatus) {
            this.statusText.innerHTML = ''
            this.statusText.classList = this.settings.statusClass
        }
    }

    /**
     * Set error class on status text in form
     */
    errorStatusClass () {
        this.statusText.classList.add(this.settings.statusErrorClass)
    }

    /**
     * Set success class on status text in form
     */
    successStatusClass () {
        this.statusText.classList.add(this.settings.statusSuccessClass)
    }

    /**
     * Submitting data
     * @param event
     */
    submitData (event) {
        let request = new XMLHttpRequest()

        let data = new FormData(this.form)

        if (this.settings.method == 'GET') {
            let firstRun = true
            let _this = this
            for (let key of data.keys()) {
                if (firstRun) {
                    _this.action += '?'
                    firstRun = false
                }
                else _this.action += '&'
                _this.action += key + '=' + data.get(key)
            }
        }

        request.open(this.settings.method, this.action, true)
        request.setRequestHeader('X-Requested-With', 'XMLHttpRequest')

        let filesInput = this.form.querySelectorAll('input[type="file"]')
        if (filesInput.length) {
            filesInput = Array.from(filesInput)
            data = this.prepareFiles(filesInput, data)
        }

        request.onload = data => {
            // Success!
            if (request.status >= 200 && request.status < 400) {
                if (request.getResponseHeader('Content-Type') === 'application/json') {
                    let data = JSON.parse(request.response)
                    //redirect
                    if (data.redirect !== undefined) {
                        window.location.replace(data.redirect)
                        return
                    }
                }
                this.settings.success(request)

            } else {
                // We reached our target server, but it returned an error
                this.settings.error(request)
            }
            this.removeSpinner()
        };
        request.send(data)

    }

    /**
     * Check input with files on existing files
     *  run function to prepare files
     *
     * @param inputsWithFile
     * @param data
     * @returns {*}
     */
    prepareFiles (inputsWithFile, data) {

        inputsWithFile.forEach((input) => {
            if (!input.files.length) return

            data = this.appendFilesIntoData(input, data)
        })

        return data
    }

    /**
     * Add files into data with new names
     * @param input
     * @param data
     * @returns {*}
     */
    appendFilesIntoData (input, data) {

        let files = Array.from(input.files)

        files.forEach((file, i) => {
            data.append(`${input.name}${i}`, file)
        })

        return data
    }

    /**
     * On error submit
     */
    errorSubmit () {
        if (this.dispalyStatus) {
            this.errorStatusClass()
            this.printText(this.settings.msgError)
        }
    }

    /**
     * On success submit
     */
    successSubmit () {
        if (this.dispalyStatus) {
            this.successStatusClass()
            this.printText(this.settings.msgDone)
        }
    }

}
