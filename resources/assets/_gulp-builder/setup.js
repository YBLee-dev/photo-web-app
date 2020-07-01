"use strict";
const fs = require('fs');
const inquirer = require('inquirer');
console.log('setup');

// setup
inquirer.prompt([
    {
        name: 'setup',
        message : 'Do you need set up project? Will be modify "main.js"',
        validate: value => { return validate(value )}
    }
]).then((answer)=> {
    if(answer.setup === 'yes' || answer.setup === 'y') setup();
});

function setup() {
    inquirer.prompt([
        {
            name: 'slider',
            message: 'Do you need slider?',
            validate: value => {
                return validate(value)
            }
        },
        {
            name: 'mfPopup',
            message: 'do you need static Magnific Popup?',
            validate: value => {
                return validate(value)
            }
        },
        {
            name: 'ajaxPopup',
            message: 'do you need Ajax Popup?',
            validate: value => {
                return validate(value)
            }
        },
        {
            name: 'magicPopup',
            message: 'do you need static Magic Popup?',
            validate: value => {
                return validate(value)
            }
        },
        {
            name: 'sendform',
            message: 'do you need Sendform?',
            validate: value => {
                return validate(value)
            }
        },
        {
            name: 'filter',
            message: 'do you need Filter?',
            validate: value => {
                return validate(value)
            }
        },
        {
            type: 'rawlist',
            name: 'map',
            message: 'Do you need Map?',
            choices: ["Yandex", 'Google', "No, i don't need"]
        }
    ]).then((answers) => {
        console.log(answers);
        prepareFile(answers);
        return;
    });

}
/**
 * Validate answers
 * @param value
 * @returns {*}
 */
function validate(value){
    if(value != 'y' && value != 'yes' && value != 'n' && value != 'no'){
         return 'Please use n/no or y/yes';
    }
    return true;
}

/**
 * Prepare file main.js with importing and initialize
 * @param answers
 */
function prepareFile(answers) {
     // will contain importing
     let importsFile = '';
     // will contain initialize
     let initPlugins = '';

     // slider
     if(checkOnNeedle(answers.slider)) {
         importsFile  += "\n import { Slider } from '../libs/slider';";
         initPlugins += "\n //new Slider('.owl-carousel');"
     }
     // sendform
    if(checkOnNeedle(answers.sendform)) {
        importsFile  += "\n import {Sendform} from '../libs/sendform/sendform2';";
        initPlugins += "\n // new Sendform(/*selector*/)"
    }
     // magnific popup
    if(checkOnNeedle(answers.mfPopup)) {
        importsFile  += "\n import { MfPopup } from '../libs/popup/mfpopup';";
        initPlugins += "\n // new MfPopup(/*selector*/);"
    }
     // ajax Popup
    if(checkOnNeedle(answers.ajaxPopup)) {
        importsFile  += "\n import { AjaxPopup } from '../libs/popup/ajaxPopup';";
        initPlugins += "\n // new AjaxPopup(/*selector*/);"
    }
    // magic Popup
    if(checkOnNeedle(answers.magicPopup)) {
        importsFile  += "\n import { MagicPopup } from '../libs/popup/magicPopup';";
        initPlugins += "\n // new MagicPopup(/*selector*/);"
    }
    // filter
    if(checkOnNeedle(answers.filter)) {
        importsFile  += "\n import {Filter} from '../libs/filter';";
        initPlugins += "\n // new Filter({});"
    }

    // magic Popup
    if(answers.map !== "No, i don't need") {
          if(answers.map === 'Yandex'){
              importsFile  += "\n import { YandexMap } from '../libs/maps/yandex_map';";
              initPlugins += ` 
          new YandexMap({
             identifier:'#map-google',
             dataFromView: false,
             center:[55.618583, 37.394107],
             zoom: 16,
             placemarks:[
                 {
                     coordinate: [55.618583, 37.394107],
                     hint:' Москва, Киевское шоссе, 5 км от МКАД',
                     iconColor: 'red',
                     baloonCnt: 'safsafa'
                 },
                 {
                     coordinate: [55.6182383, 37.395507],
                     hint:' Rsfa, Киевское шоссе, 5 км от МКАД',
                 }
             ]
          });
              `
          }
          if(answers.map === 'Google'){
              importsFile  += "\n import { GoogleMap } from '../libs/maps/google_map';";
              initPlugins += ` 
          new GoogleMap({
             identifier:'#map-google',
             dataFromView: false,
             center:[55.618583, 37.394107],
             zoom: 16,
             placemarks:[
                 {
                     coordinate: [55.618583, 37.394107],
                     hint:' Москва, Киевское шоссе, 5 км от МКАД',
                     iconColor: 'red',
                     baloonCnt: 'safsafa'
                 },
                 {
                     coordinate: [55.6182383, 37.395507],
                     hint:' Rsfa, Киевское шоссе, 5 км от МКАД',
                 }
             ]
          });
              `
          }
    }

     // Finish set up file.
     let file = `
     ${importsFile}
$( document ).ready(function() {
    if(NODE_ENV === 'dev'){
        console.log('its dev mode use prod mode for production');
    }
    let projectApp = new App();
    global.app = projectApp;
    projectApp.init();
});


class App{
    // Define global visible variable inside app 
    constructor(){}
    init(){
     ${initPlugins}
    }
};`;

    writeFile(file);
}
/**
 * Check if answer yes or no,
 * @param value
 * @returns {boolean}
 */
function checkOnNeedle(value) {
    if(value === 'yes' || value === 'y') return true;
    return false;
}
/**
 * Open and write in file
 * @param file
 */
function writeFile(file) {
    let logStream = fs.createWriteStream('./js/script/main.js', {'flags': 'a'});
    logStream.write(file);
}
