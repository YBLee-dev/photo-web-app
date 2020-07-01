
<form method="POST" action="{{route('dashboard::gallery.subgallery.update-cropped-info', $subGallery->id)}}" enctype="multipart/form-data"
      class="js_form-crop"
      style="min-height: 500px;">
    <div class="box box-widget">
        <div class="box-header">
            <div class="btn btn-flat btn-default  btn-file">
                <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                Upload new photo
                <input type="file" class="js_crop-file-inp">
            </div>
            <button class="btn btn-flat btn-default js_show-carousel">  <i class="fa fa-cloud-upload" aria-hidden="true"></i> Upload from this Sub-gallery</button>
            <button class="btn btn-flat btn-success  pull-right js_crop-btn"> <i class="fa fa-crop" aria-hidden="true"></i> Crop</button>
        </div>
        <div class="box-body" >
            <div class="wrap-crop-img">
                <img src="{{$originalImageUrl}}" alt=""
                     class="img-responsive  js_crop-image"
                     data-width="{{$croppedFaceWidth}}"
                     data-height="{{$croppedFaceHeight}}"
                     data-top-indent="{{$croppingTopIndent}}"
                     data-bottom-indent="{{$croppingBottomIndent}}"
                     data-crop-x="{{$cropX}}"
                     data-crop-y="{{$cropY}}"
                     data-crop-original-width="{{$cropWidth}}"
                     data-crop-original-height="{{$cropHeight}}"
                >
            </div>

        </div>
    </div>
</form>

<div class="js_subgallery-carousel" style="display: none;">
    <div id="carousel-subgalledy-photo" class="carousel slide" data-ride="carousel" data-interval="false">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            @foreach ($subGallery->photos as $photo)
                <li data-target="#carousel-subgalledy-photo" data-slide-to="{{$loop->index}}" @if($loop->first) class="active" @endif></li>
            @endforeach
        </ol>

        <!-- Wrapper for slides -->
        <div class="carousel-inner">
            @foreach ($subGallery->photos as $photo)
                <div class="item @if($loop->first) active @endif">
                    <img data-src="{{$photo->present()->previewUrl()}}" alt="" class="js_subgallery-photo carousel-item-img" >
                    <div class="carousel-caption">
                        <a href="#" class=" btn btn-success margin btn-flat js_new-crop-photo"
                           data-url="{{$photo->present()->originalUrl()}}"
                           data-id="{{$photo->id}}">
                            <i class="fa fa-cloud-upload" aria-hidden="true"></i> Choose
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Controls -->
        <a class="left carousel-control" href="#carousel-subgalledy-photo" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left"></span>
        </a>
        <a class="right carousel-control" href="#carousel-subgalledy-photo" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right"></span>
        </a>
    </div>

</div>


<link rel="stylesheet" type="text/css" href="{{asset('css/cropper.css')}}">
<script src="{{asset('js/cropper.js')}}"></script>

<style>
    .carousel-inner>.item>.carousel-item-img{
        max-width: 400px;
        margin: auto;
    }
    .js_base_modal_empty .loader{
        border-width: .5em;
        margin: -2em;
        position: absolute;
        top: 50%;
        left: 50%;
        border-left-width: .5em;
    }
    .js_base_modal_empty .loader, .js_base_modal_empty .loader:after {
        border-radius: 50%;
        width: 4em;
        height: 4em;
    }
    .js_base_modal_empty #formsendHover{
        background-color: #fff;
    }
    .js_base_modal_empty .wrap-crop-img{
        max-width: 400px;
        margin: 0 auto;
    }
    .cropper-crop-box:before,
    .cropper-crop-box:after{
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        border-top: 2px dashed red;
        z-index: 15;
    }
</style>
<script>
        let imageCrop = document.querySelector('.js_crop-image');

        let $modalCropCnt = $('.js_base_modal_empty .box');
        let $body = $('body');
        let modal = $('.js_base_modal_empty');
        $modalCropCnt.spinnerAdd('formsendHover');

        let initCrop = function(){
            return new Cropper(imageCrop, {
                aspectRatio: imageCrop.dataset.width / imageCrop.dataset.height,
                dragMode:'move',
                data: {
                    "x": Number(imageCrop.dataset.cropX),
                    "y": Number(imageCrop.dataset.cropY),
                    "width": Number(imageCrop.dataset.cropOriginalWidth) ,
                    "height": Number(imageCrop.dataset.cropOriginalHeight)
                },
                ready: function (event) {
                    $.spinnerRemove('formsendHover', 'form-loading');

                    $('body').append('<style>.cropper-crop-box:before{top: ' + imageCrop.dataset.topIndent/imageCrop.dataset.width*100 + '%;}' +
                        '.cropper-crop-box:after{bottom: ' + imageCrop.dataset.bottomIndent/imageCrop.dataset.height*100 + '%;}</style>');
                },
                crop: function () {
                    var cropData = cropper.getData();
                    if(cropData.x< 0){
                        cropper.setData({x:0})
                    }
                    if(cropData.y < 0){
                        cropper.setData({y:0})
                    }
                    if(cropData.width > cropper.getImageData().naturalWidth){
                        cropper.setData({width:cropper.getImageData().naturalWidth})
                    }
                },
                zoom: function (e) {
                    if (e.detail.ratio > 1 || e.detail.ratio < 0.1) {
                        event.preventDefault(); // Prevent zoom in
                    }
                }
            });
        };

        let cropper = initCrop();

        modal.on('hidden.bs.modal', function (e) {
            cropper.destroy();
            modal.off('click', '.js_crop-btn')
            modal.off('change', '.js_crop-file-inp')
            modal.find('.modal-body').html('');
        });

        modal.on('click', '.js_crop-btn', function (e) {

            e.preventDefault();
            $modalCropCnt.spinnerAdd('formsendHover');

            let thisForm = $(this).closest('form');
            let formData = new FormData();
            formData.append('_token', $('#_token-csrf').html());
            var cropperBoxData = cropper.getData();

            formData.append('crop_x', Math.round(cropperBoxData.x));
            formData.append('crop_y', Math.round(cropperBoxData.y));
            formData.append('crop_original_width', Math.round(cropperBoxData.width));
            formData.append('crop_original_height', Math.round(cropperBoxData.height));

            cropper.getCroppedCanvas({
                width: imageCrop.dataset.width,
                height: imageCrop.dataset.height,
            }).toBlob((blob) => {


                formData.append('cropped_image', blob);

                $.ajax(thisForm.attr('action'), {
                    method: thisForm.attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (dataUrl) {
                        let thisImg = $(modal.attr('data-replace-blk')).closest('.box').find('.has-img img');
                        thisImg.attr('src', '');
                        thisImg.attr('src', dataUrl);
                        $.spinnerRemove('formsendHover', 'form-loading');
                        modal.modal('hide');
                    },
                    error: function () {
                        console.log('Upload error');
                        $.spinnerRemove('formsendHover', 'form-loading');
                    }
                });
            });
        });
        let newImageForCrop = function (url) {
            // input.value = '';
            imageCrop.src = url;
            $modalCropCnt.spinnerAdd('formsendHover')
            cropper.destroy();
            cropper = initCrop();
        };
        modal.on('change', '.js_crop-file-inp', function (e){
            let files = e.target.files;
            let reader;
            let file;

            if (files && files.length > 0) {
                file = files[0];
                if (URL) {
                    newImageForCrop(URL.createObjectURL(file));
                } else if (FileReader) {
                    reader = new FileReader();
                    reader.onload = function (e) {
                        newImageForCrop(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
        modal.on('click', '.js_new-crop-photo', function (e){
            e.preventDefault();
            let urlNew = $(this).attr('data-url');
            $modalCropCnt.spinnerAdd('formsendHover');
            newImageForCrop(urlNew);
            $formCropping.show();
            $carouselBlk.hide();
        });

        let $formCropping = $('.js_form-crop');
        let $carouselBlk = $('.js_subgallery-carousel');
        let $imagesSubgallery = $('.js_subgallery-photo');
        modal.on('click', '.js_show-carousel', function (e) {
            e.preventDefault();
            $formCropping.hide();
            $carouselBlk.show();
            $imagesSubgallery.each(function () {
                $(this).attr('src', $(this).attr('data-src'));
            })
        })
</script>
