/**
 * Configure default class of map.
 */

import getByUrl from './get_map';
import { Component } from '../../components/component';
const defaultConfig = {
    identifier: '#map-google',
    key: 'AIzabyC2Yoh_d1l9AZ7GUp5rP_avurGrYuHb3YM',
    dataFromView: false,
    center: [46.479211, 30.723335],
    zoom: 16,
    placemarks: [
        {
            coordinate: null,
        },
    ]
};

export class Map extends Component{
    /**
     * @param data{Object}
     * @param map{string} - ymaps or google
     */
    constructor(data, map){
        super(data.identifier);
        //if fet from view and try to get data
        if(data.dataFromView){
            try {
                data = this.getAttrFromView();
            }
            catch(err){
                console.warn(err);
                return;
            }
        }
        //merge objects
        data = Object.assign(defaultConfig, data);
        //check on errors from parent class
        if(this.errors){
            return;
        }
        //define map
        this.needleMap = map;
        //options of map link
        this.maps = {
            ymaps:  '//api-maps.yandex.ru/2.1/?lang=ru_RU',
            google: '//maps.googleapis.com/maps/api/js?v=3' + '&key=' + this.key
        };
        this.loadMap(data);
    }

    /**
     * Get map and set it up.
     * @param data{Object}
     */
    loadMap(data) {
        getByUrl(this.maps[this.needleMap]).then(() => {
            this.setAttr(data);
            this.map = global[this.needleMap];
            this.initMap();
        }, (reason) => {
            this.errorInit(reason);
        });
    }

    /**
     * Set attribute after getting data.
     * @param data{Object}
     */
    setAttr(data){
        this.center = data.center;
        this.key = data.key;
        this.zoom = data.zoom;
        this.placemarks = data.placemarks;
    }

    /**
     * Getting data from view.
     */
    getAttrFromView(){
        let jsonData = $('#map_json').html();
        return JSON.parse(jsonData);
    }

    /**
     * Console error.
     * @param reason
     */
    errorInit(reason){
        console.error(reason);
    }

    /**
     * Init map should be redeclare in child class.
     */
    initMap(){
        console.warn('Warning in Map: you should redeclare your own initMap function')
    };
}