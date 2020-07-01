/**
 * Will set params:
 * this.element{node}
 * this.errors{bool}  error on define this.element
 */

export class Component{
    constructor(el){
        this.errors = false;
        this.init = el;
    }

    /**
     * Set this.element, before check for correctness.
     *
     * @param el{string} - class of init element.
     */
    set init(el) {
        let elementClass = this.checkOnExistElement(el);
        if(elementClass) {
            this.element = elementClass;
            this.errors = false;
            return;
        }
        this.errors = true;
    }

    /**
     * Getter for element.
     *
     * @returns {node} - element of slider init.
     */
    get getElement() {
        return this.element;
    }

    /**
     * Check is element exist.
     * Check is element has ('.' or '#').
     *
     * @param el{string} - element.
     * @returns {*} - element.
     */
    checkOnExistElement(el){
        let elementClass = $(el);
        if(!el){
            this.errors = true;
            console.error(`Error in Component: '${el}' has error with init`);
            return false;
        }
        if(!elementClass.length){
            if(el.indexOf('.') != 0 && el.indexOf('#') != 0){
                this.errors = true;
                console.error(`Error in Component: '${el}' does not contain the identification '.' or '#'`);
                return false;
            }
            this.errors = true;
            console.warn(`Warning in Component: '${el}' is not define on page`);
            return false;
        }
        return elementClass;
    }
}