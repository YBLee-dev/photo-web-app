import { Sendform } from '../libs/sendform/sendform2';
import { getToken } from "../libs/getToken";


$( document ).ready(function() {
    let projectApp = new App();
    projectApp.init();
});

class App{
    constructor(){}

    init() {
        if($('#modal-digital-package').length){
            $.magnificPopup.open({
                showCloseBtn: false,
                type: 'inline',
                alignTop:true,
                tLoading: 'Loading...',
                items: {
                    src: '#modal-digital-package'
                }
            });
            $('body').on('click', '.js_mfpopup-popup-close', function (e) {
                e.preventDefault();
                $.magnificPopup.close();
            })
        }
        let $updateInformer = $('.js-get-info-zip');
        if($updateInformer.length) {
            let action = $updateInformer.attr('data-action');
            let method = $updateInformer.attr('data-method');
            let data = {_token: getToken()};
            let intervalSendAjax =  setInterval(function () {
                $.ajax({
                    url: action,
                    method: method,
                    data: data,
                    success: (data) => {
                        if (data.redirect !== undefined) {
                            window.location.replace(data.redirect);
                            return;
                        }
                        if(data.link && data.link !== ''){
                            let $cntUser = $('.js-get-info-zip-cnt');
                            $cntUser.fadeIn(200);
                            $updateInformer.remove();
                            $cntUser.each((el)=>{
                                el.find('.js_zip-download').attr('href', data.link);
                            });
                            clearInterval(intervalSendAjax);
                        }
                    },
                    error: (data) => {
                    }
                });
            }, 3000);
        }
        /**
         * Send form enter code
         * @type {Form}
         */
        let formCodeLogin = new Sendform('.js_form-code', {
            success: function(){},
            error: function (request) {
                $('.js_form-code .form-status').text(JSON.parse(request.response).message).addClass('with_error');
            }
        });
        /**
         * Send form on order payment page
         * @type {Form}
         */
        let formOrderPay = new Sendform('.js_form-pay', {
            success: function(data){
                let url = '/order/get/' + JSON.parse(data.response).order_id;
                window.location.href=url;
            },
            error: function (request) {
                $('.js_form-pay .form-status').html(JSON.parse(request.response).message).addClass('with_error');
            }
        });
        /**
         * Send form on order status page
         * @type {Form}
         */
        let formWithReview = new Sendform('.js_submit-review', {
            success: function(){},
            error: function (request) {
                $('.js_submit-review .form-status').text(JSON.parse(request.response).message).addClass('with_error');
            }
        });
        $('body').on('click', '.js_review-add', function () {
            let link = $(this).attr('data-href');
            window.open(link, '_blank');
        });

        /**
         * Send form on order status page
         * @type {Form}
         */
        let formWithReviewEmail = new Sendform('.js_submit-review-email', {
            success: function(){}
        });

        if($('.js_review-add-email').length){
            let links = $('.js_review-add-email').attr('data-href');
            localStorage.setItem('link', links);
            let linkGgl = localStorage.getItem('link')
            window.open(linkGgl, '_blank');
        }

        if($('.js_submit-review-email').length) {
            $('.js_submit-review-email').trigger('submit');
        }

        /**
         * accordion for cart product's on order status page
         */
        let accordion = function(accordionTtl, accordionCnt, activeClassTtl = "__active", activeClassCnt = '__show') {
            $('body').on('click', accordionTtl, function (event) {
                event.preventDefault();
                if ($(this).hasClass(activeClassTtl) && !$(this).hasClass(activeClassTtl)) {
                    $(this).removeClass(activeClassTtl);
                    $(this).closest('.order-product').find(accordionCnt).slideUp(400).removeClass(activeClassCnt);
                }
                $(this).toggleClass(activeClassTtl);
                $(this).closest('.order-product').find(accordionCnt).slideToggle(400).toggleClass(activeClassCnt);

            });
        };
        accordion('.js_ui-accordion-ttl', '.js_ui-accordion-cnt');

        /**
         * Mask for order credit card
         */
        if ($('.js_date').length){
            let cleaveDate = new Cleave('.js_date', {
                date: true,
                datePattern: ['m', 'y']
            });

            let cleaveNumber = new Cleave('.js_number', {
                creditCard: true,
                onCreditCardTypeChanged: function (type) {
                }
            });

            let cleaveCvv = new Cleave('.js_cvv', {
                blocks: [3, 3, 3],
                numeral: true,
                delimiter: '',
                delimiterLazyShow: true
            });
        }
        function detectMob() {
            if( navigator.userAgent.match(/Android/i)
                || navigator.userAgent.match(/webOS/i)
                || navigator.userAgent.match(/iPhone/i)
                || navigator.userAgent.match(/iPad/i)
                || navigator.userAgent.match(/iPod/i)
                || navigator.userAgent.match(/BlackBerry/i)
                || navigator.userAgent.match(/Windows Phone/i)
            ){
                return true;
            }
            else {
                return false;
            }
        }

        if(detectMob()){
            // $('.js_mob-preview-images').fadeIn(150);
            $('.js_zip-download').hide();
        }
    }

};
