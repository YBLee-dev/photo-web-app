<form class=" js-submit " action="{{route('dashboard::gallery.subgallery.client.update', $client)}}" method="POST" data-send-all-checkbox="false">
    <input type="hidden" name="_token" value="{{csrf_token()}}">
    <input type="hidden" name="_method" value="PUT">

    <div class="form-group ">
        <label for="first_name" >First name</label>
        <input id="first_name" class="form-control form-required" type="text" placeholder="" value="{{$client->first_name}}" name="first_name">
    </div>
    <div class="form-group ">
        <label for="second_name" >Second name</label>
        <input id="second_name" class="form-control" type="text" placeholder="" value="{{$client->last_name}}" name="last_name">
    </div>

    <div class="form-group ">
        <label for="classroom">Classroom</label>

        <select id="classroom" name="classroom" class="form-control js-select2-dynamic-options" style="width: 100%;">
            @foreach($classrooms as $key => $classroom)
                <option value="{{$classroom}}" @if($client->classroom == $classroom) selected="true" @endif>{{$classroom}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group ">
        <label for="classrooms">Additional Classrooms</label>

        <select id="classrooms" name="add_classrooms[]" class="form-control js-select2" style="width: 100%" multiple>
            @foreach($classrooms as $key => $classroom)
                @if($client->classroom !== $classroom)
                    <option value="{{$classroom}}" @if($client->additionalClassrooms->contains('name', $classroom)) selected="true" @endif>{{$classroom}}</option>
                @endif
            @endforeach
        </select>

    </div>

    <div class="form-group " >
        <label for="school_name">School</label>
        <input id="school_name" class="form-control" type="text" placeholder="" value="{{$client->school_name}}" name="school_name" >
    </div>
    {{--<div class="form-group ">--}}
        {{--<label for="graduate">Graduate</label>--}}
        {{--<input id="graduate" type="checkbox" name="graduate" value="true">--}}
    {{--</div>--}}

    <div class="form-group ">
        <input id="teacher" type="checkbox" class="checkbox" name="teacher"  @if($client->teacher) checked @endif >
        <label for="teacher" class="checkbox-lbl">Staff</label>
    </div>

    <div class="form-group " >
        <label for="title">Title (for staff only)</label>
        <input id="title" class="form-control" type="text" placeholder="" value="{{$client->title}}" name="title">
    </div>

    <div class="form-group ">
        <button type="submit" class="btn btn-flat bg-light-blue margin ">Update</button>
    </div>

</form>
<script>
    let $selectWithDynamicOption = $('.js-select2-dynamic-options');
    $selectWithDynamicOption.select2({
        tags: true,
        dropdownParent: $selectWithDynamicOption.closest('form')
    });
</script>
