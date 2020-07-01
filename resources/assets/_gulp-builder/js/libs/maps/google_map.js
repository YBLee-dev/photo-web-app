/**
 * Config google map.
 */

import { Map } from './map';

export class GoogleMap extends Map{
    /**
     * @param data
     */
    constructor(data){
        super(data, 'google');
    }


    initMap() {
        //set map and center
        let map = new this.map.maps.Map(this.element[0], {
            center: {lat: this.center[0], lng: this.center[1]},
            zoom: this.zoom
        });

        //if dont have mark set it in center.
        if(this.placemarks[0].coordinate == null){
            new this.map.maps.Marker({
                position: {lat: this.center[0], lng: this.center[1]},
                map: map
            });
            console.warn('Warning in Map: coordinate has not declare');
            return ;
        }

        let geos = [];
        // add marks
        this.placemarks.forEach((placemark)=>{
            let geo = new this.map.maps.Marker({
                position: {lat: placemark.coordinate[0], lng: placemark.coordinate[1]},
                map: map,
                title: placemark.hint
            });
            geos.push(geo);
        });
    }

}