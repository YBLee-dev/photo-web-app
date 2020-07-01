<div {!! $dynamic_fields !!}>
<div class="box box-default js_ui-file-preview">
    <div class="box-header with-border">
        <h3 class="box-title">{{$title}}</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <img class="img-responsive pad js_ui-input-file-preview" src="{{$img_url}}" alt="Photo" @if(!$img_url)style="display: none;"@endif>
        <div class="mailbox-attachment-icon js_ui-input-file-default-img" @if($img_url)style="display: none;"@endif>
            <i class="fa fa-image"></i>
        </div>
        <div class="text-center">
            @if($file_name)<div class="image-name js_ui-input-file-name" title="{{$file_name}}" data-placement="bottom" >{{$file_name}}</div>@endif
            @if($width && $height)<div class="image-size js_ui-input-file-size-with-height">{{$width}} x {{$height}} px</div>@endif
            @if($size)<div class="image-size js_ui-input-file-size">{{$size}}</div>@endif
        </div>
    </div>
    <div class="box-footer text-center">
        <div class="btn-group">
            <button type="button" class="btn btn-flat btn-success btn-file">
                <i class="fa fa-edit"></i>
                <input type="file" name="{{$name}}" class="js_ui-input-file" accept="image/*">
            </button>
        </div>
    </div>
    <!-- /.box-body -->
</div>
</div>
