@if( strpos( $dynamic_fields, 'js_delete' ) !== false )
    <div class="col-md-2" id="{{$id}}">
@else
    <form action="{{$action}}" method="{{$method}}" id="{{$id}}" data-replace-blk="#myformID" {!! $dynamic_fields !!}>
@endif
            <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">{{$title}}</h3>
            <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <img class="img-responsive pad " src="{{$img_url}}@if($no_caching)?no-cache={{uniqid()}}@endif" alt="Photo" @if(!$img_url) style="display: none;" @endif>
            <div class="mailbox-attachment-icon" @if($img_url) style="display: none;" @endif>
                <i class="fa fa-image"></i>
            </div>
            <div class="text-center">
                @if($file_name)<div class="image-name" title="{{$file_name}}" data-placement="bottom" >{{$file_name}}</div>@endif
                @if($width && $height)<div class="image-size">{{$width}} x {{$height}} px</div>@endif
                @if($size)<div class="image-size">{{$size}}</div>@endif
            </div>
        </div>
        <div class="box-footer text-center">
            <div class="btn-group">
                @if($download_url)<a href="{{$download_url}}" type="button" class="btn btn-flat btn-info" download ><i class="fa  fa-download"></i></a> @endif($download_url)
                @if( strpos( $dynamic_fields, 'edit-btn' ) !== false )
                    <button type="button" class="btn  btn-flat btn-success btn-file">
                        <i class="fa fa-edit"></i>
                        <input type="file" name="{{$name}}" class="js_submit-form-by-change-el" accept="image/*" data-form="#myformID">
                    </button>
                @endif
                    @if( strpos( $dynamic_fields, 'delete-btn-disable' ) !== false )
                        <button type="button" class="btn  btn-flat btn-danger disabled"
                                data-title="This photo has connected orders or carts"
                                data-action="{{$delete_action}}"
                                data-replace-blk="#{{$id}}"
                                data-method="{{$delete_method}}"><i class="fa fa-trash-o"></i></button>
                    @elseif( strpos( $dynamic_fields, 'js_delete' ) !== false )
                        <button type="button" class="btn  btn-flat btn-danger js_delete"
                                data-request="{{$delete_action}}"
                                data-item="#{{$id}}"
                                data-method="{{$delete_method}}"><i class="fa fa-trash-o"></i></button>
                    @else
                        <button type="button" class="btn  btn-flat btn-danger js_ajax-by-click-btn"
                                data-action="{{$delete_action}}"
                                data-replace-blk="#{{$id}}"
                                data-method="{{$delete_method}}"><i class="fa fa-trash-o"></i></button>
                    @endif
            </div>
        </div>
        <!-- /.box-body -->
    </div>
@if( strpos( $dynamic_fields, 'js_delete' ) !== false )
    </div>
@else
    </form>
@endif
