const fs = require('fs');

module.exports = class changeBaseFile {
    constructor(action) {

        this.actions = typeof action === 'object' ? action : [action];

        this.fileName = '../../views/core/base.blade.php';
        this.fileStyleName = '../../views/core/base_styles.blade.php';

        this.baseStylesPath = '../../../public_html/css/base-style.css';


        console.log(`start to replace ${action}`);
        const baseStyles =
            `{{--###BaseStyles###--}}
                <style>
                    ${this.getCssForBase()}
                </style>
            {{--###BaseStyles###--}}`

        const scripts =
            `{{--###Scripts###--}}
<script src="{{asset('js/libs.js?version=${this.generateHash()}')}}"></script>
<script src="{{asset('js/script.js?version=${this.generateHash()}')}}"></script>
{{--###Scripts###--}}`

        const styles =
            `{{--###Styles###--}}
<link rel="stylesheet" type="text/css" href="{{asset('css/style.css?version=${this.generateHash()}')}}">
<link rel="stylesheet" type="text/css" href="{{asset('css/custom.css?version=${this.generateHash()}')}}">
{{--###Styles###--}}`

        this.state = {
            baseStyles: {
                pasteData: baseStyles,
                mark: '{{--###BaseStyles###--}}'
            },
            scripts: {
                pasteData: scripts,
                mark: '{{--###Scripts###--}}'
            },
            styles: {
                pasteData: styles,
                mark: '{{--###Styles###--}}'
            }
        };
    }

    init() {
        this.readFile(this.fileName);
        this.readFile(this.fileStyleName);
    }

    readFile(fileName) {
        let _this = this;

        let data = fs.readFileSync(fileName);

        let file = data.toString();

        this.actions.forEach((type)=>{
            let active = this.state[type];

            if(file.indexOf(active['mark']) == -1) return;

            file = file.split(active['mark']);

            file[1] = active['pasteData'];

            file = file.join('');

        });

        this.changeFile(fileName, file);


        // fs.readFile(this.fileName, function (err, data) {
        //     if (err) throw err;
        //
        //     let file = data.toString().split(_this.active['mark']);
        //
        //
        //     file[1] = _this.active['pasteData'];
        //
        //     _this.changeFile(file.join(''));
        // });
    }

    changeFile(fileName, buffer) {
        fs.writeFileSync(fileName, buffer);
        console.log('replaced success!');
    }

    generateHash() {
        return new Date().toISOString();
    }

    getCssForBase(){
        let buf = fs.readFileSync(this.baseStylesPath);
        return buf.toString();
    }

};