/**
 * Config yandex map.
 */
import { Map } from './map';

export class YandexMap extends Map{
    /**
     * @param data{Object}
     */
    constructor(data){
        super(data, 'ymaps');

        this.identifier = data.identifier;
    }


    initMap() {
        this.map.ready(()=> {
            // slice # or .
            let el = this.identifier.slice(1);
            // set center
            let map = new this.map.Map(el, {
                center: this.center,
                zoom: this.zoom
            });

            // If don't have placemark set placemark in center.
            if(this.placemarks[0].coordinate == null){
                map.geoObjects.add(
                    new this.map.Placemark({
                        type: "Point",
                        coordinates: this.center,
                    })
                );
                console.warn('Warning in Map: coordinate has not declare');
                return ;
            }
            let geos = [];
            // add palcemarks
            this.placemarks.forEach((placemark)=>{
                 let geo = new this.map.Placemark({
                        type: "Point",
                        coordinates: placemark.coordinate
                    },
                    {
                        hintContent : placemark.hint,
                        balloonContent: placemark.baloonCnt

                    },
                    {
                        iconColor: placemark.iconColor || 'blue'
                    }
                );
                geos.push(geo);
                map.geoObjects.add(geo);
            });
        });
    }

}