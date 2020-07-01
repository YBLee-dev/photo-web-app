/**
 * Owl carousel slider class.
 */
import '../../node_modules/owl.carousel/dist/owl.carousel.min';

import { Component } from '../components/component';

const defaultSetting = {
    loop: true,
    margin: 0,
    items: 1,
    nav: true,
    autoPlay: true,
    stopOnHover: true,
    navText: ['', ''],
};
/**
 * Class for init slider.
 *
 */
export class Slider extends Component{
    /**
     * @param el{string} - slider class.
     * @param settings{Object} - settings.
     * @param plugin{string} - plugin define.
     */
    constructor(el = '.js_owl-carousel', settings = defaultSetting, plugin = 'owlCarousel'){
        super(el);
        this.settings = Object.assign({}, defaultSetting, settings);
        this.plugin = plugin;
        if(!this.errors) {
            this.inetedPlugin = this.initSlider();
        }
    }

    /**
     * Set new plugin.
     *
     * @param plugin{string} - name of function which init plugin.
     */
    set plugin(plugin){
        plugin = jQuery()[plugin];
        if(plugin == undefined){
            console.error('Error in Slider: plugin of slider is not defined.');
            return;
        }
        this.sliderPlugin = plugin;
    }

    /**
     * Initialize slider with owlCarousel.
     */
    initSlider(){
        return this.sliderPlugin.call(this.element, this.settings);
    }
}