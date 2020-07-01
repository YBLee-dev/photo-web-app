/**
 * Filter class.
 */


import { getToken } from './getToken';

/**
 *    paramFilterInput{String} - class of input which will be filter use
 *    filterParamsClass{String} - class of wrapper each of inputs parameters
 *    countIncrease{Number} - number how much cars should be increase when show more
 *    requestUrl{String} - url of route filter
 *    classSendRequest{String} - class of btn od sending request
 *    selectSorting{String} - class of select which call sorting.
 *    viewChangeClass{String} - class of change view
 *    itemsWrap {String} - class wrapper of products
 *    showMoreBtn{String} - class of btn show-more
 */
export class Filter{
    /**
     *
     * @param data{Object}
     */
    constructor(data){
        this.filterParamsClass = data.filterParamsClass;
        this.paramFilterInput = data.paramFilterInput;
        this.countIncrease = data.countIncrease;
        this.requestUrl = data.requestUrl;
        this.classSendRequest = data.classSendRequest;
        this.selectSorting = data.selectSorting;
        this.viewChangeClass = data.viewChangeClass;
        this.itemsWrap = data.itemsWrap;
        this.showMoreBtn = data.showMoreBtn;


        const TOKEN =  getToken();
        this.countIncrease = 10; // number of increase
        this.state = {
            count: this.countIncrease,
            sortBy: 'asc',
            view: 'vertical',
            car_state: 'new',
            _token : TOKEN
        };
    }

    init(){
        /**
         * Creating filter parameters
         */
        this.createFilterParams();

        /**
         * Filter on click checkbox model.
         */
        this.initCLickOnFilterParams();

        /**
         * Show more
         */
        if(this.showMoreBtn) {
            this.initShowMoreClick();
        }
        /**
         * On change view.
         */
        if(this.viewChangeClass) {
            this.initChangeView();
        }
        /**
         * On change select for sorting by price.
         */
        if(this.selectSorting) {
            this.initSortingOnChange();
        }
        /**
         * Send request
         */
        if(this.classSendRequest){
            this.initSendRequestBtn();
        }
    }
    /**
     * Init Filter on click checkbox model.
     */
    initCLickOnFilterParams(){
        let _this = this;
        $('body').on('click', this.paramFilterInput, function () {
            let $el = $(this).closest(_this.filterParamsClass);
            let value = $(this).val();
            let stateAttr = $el.data('name');
            _this.changeFilterState(value, stateAttr);
        });
    }

    /**
     * Init function on click show-more
     */
    initShowMoreClick(){
        let _this = this;
        $('body').on('click', this.viewChangeClass, function(e){
            e.preventDefault();
            $(_this.viewChangeClass).removeClass('__active');
            _this.state.view =  $(this).data('view');
            _this.request();
        });
    }

    /**
     * Init function on change sorting method
     */
    initSortingOnChange(){
        let _this = this;
        $('body').on('change', this.selectSorting, function(e){
            e.preventDefault();
            _this.state.sortBy = $(this).val();
            _this.request();
        });
    }

    /**
     * Init function on click send request
     */
    initSendRequestBtn(){
        let _this = this;
        $('body').on('click', this.classSendRequest, (event)=>{
            event.preventDefault();
            _this.request();
        });
    }

    /**
     * Init function on change view
     */
    initChangeView(){
        let _this = this;
        $('body').on('click', this.viewChangeClass, function(e){
            e.preventDefault();
            $(_this.viewChangeClass).removeClass('__active');
            _this.state.view =  $(this).data('view');
            _this.request();
        });
    }

    /**
     * Change filter state
     * remove/add/change items into array/attribute.
     *
     * @param val
     * @param attrName
     */
    changeFilterState(val, attrName){
        let stateAttr = this.state[attrName];
        if(typeof(stateAttr) !== 'object'){
            stateAttr = val;
            return;
        }

        if(!stateAttr.length){
            stateAttr.push(val);
            return;
        }
        let res = stateAttr.findIndex( (el)=>{
            return el == val;
        });
        if(res != -1){
            stateAttr.splice(res , 1);
        } else{
            stateAttr.push(val);
        }
    }

    /**
     * Append node items to wrap
     *
     * @param newItems
     */
    appendItems(newItems){
        $(this.itemsWrap).html(newItems);
    }

    /**
     * Changing count of cars
     */
    changeCount(){
        let count = $(this.findedCount).html();
        $(this.findedCountClass).html(count);
    }

    /**
     * Creating filter parameters
     */
    createFilterParams(){
        $(this.filterParamsClass).each((i, el)=>{
            let paramName = $(el).data('name');
            let type = $(el).data('type');
            if(paramName && !type){
                this.state[paramName] = [];
                return;
            }
            this.state[paramName] = '';
        })
    }

    /**
     * On success request
     *
     * @param data
     */
    success(data){
        this.appendItems(data);
    }

    /**
     * Request to server with state.
     *
     * @param url{String} - request url
     * @param method{String}
     */
    request(url = this.requestUrl, method = 'POST'){
        $.ajax({
            url: url,
            method: method,
            data: this.state,
            success: data => {
                this.success(data);
            }
        });
    }
}