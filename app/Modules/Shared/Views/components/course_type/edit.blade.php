{{ csrf_field() }}

<div class="form-group @if($errors->has('title')) has-error @endif">
    <label for="partner">Title</label>
    <input class="form-control" type="text" name="title" id="title" value="{{ $vehicleSegment->title }}"/>
</div>

<div class="form-group">
    <label for="vehicle_id">Vehicles</label>
    <select id="vehicle_id" name="vehicle_id">
        @foreach($vehicles as $vehicle)
            <option value="{{ $vehicle->id }}" @if($vehicleSegment->vehicle_id == $vehicle->id) selected @endif >{{ $vehicle->name }}</option>
        @endforeach
    </select>
</div>

<div class="form-group @if($errors->has('slug')) has-error @endif">
    <label for="title">Slug</label>
    <div class="input-group">
        <span class="input-group-addon">{{ url('/') }}/kurser/</span>
        <input class="form-control" type="text" name="slug" id="slug" aria-describedby="uriHelp" placeholder="Skriv uri länk" value="{{ $vehicleSegment->slug }}"/>
    </div>
</div>

<div class="form-group @if($errors->has('description')) has-error @endif">
    <label for="partner">Description</label>
    <input class="form-control" type="text" name="description" id="description" value="{{ $vehicleSegment->description }}"/>
</div>

<div class="form-group @if($errors->has('sub_description')) has-error @endif">
    <label for="partner">Sub Description</label>
    <input class="form-control" type="text" name="sub_description" id="sub_description" value="{{ $vehicleSegment->sub_description }}"/>
</div>

<div class="form-group @if($errors->has('calendar_description')) has-error @endif">
    <label for="partner">Calendar Description</label>
    <input class="form-control" type="text" name="calendar_description" id="calendar_description" value="{{ $vehicleSegment->calendar_description }}"/>
</div>

<div class="form-group @if($errors->has('explanation')) has-error @endif">
    <label for="partner">Explanation</label>
    <input class="form-control" type="text" name="explanation" id="explanation" value="{{ $vehicleSegment->explanation }}"/>
</div>

<div class="form-group @if($errors->has('sub_explanation')) has-error @endif">
    <label for="partner">Sub Explanation</label>
    <textarea class="form-control" type="text" name="sub_explanation" id="sub_explanation" value="{{ $vehicleSegment->sub_explanation }}">
        {{ $vehicleSegment->sub_explanation }}
    </textarea>
</div>

<div class="form-group @if($errors->has('default_price')) has-error @endif">
    <label for="default_price">Default Price</label>
    <input type="number" id="default_price" name="default_price" value="{{ $vehicleSegment->default_price }}" />
</div>

<div class="form-group @if($errors->has('editable')) has-error @endif">
    <input type="checkbox" name="editable" id="editable" @if($vehicleSegment->editable) checked="checked" @endif>
    <label for="active">Editable</label>
</div>

<div class="form-group @if($errors->has('bookable')) has-error @endif">
    <input type="checkbox" name="bookable" id="bookable" @if($vehicleSegment->bookable) checked="checked" @endif>
    <label for="active">Bookable</label>
</div>

<div class="form-group @if($errors->has('comparable')) has-error @endif">
    <input type="checkbox" name="comparable" id="comparable" @if($vehicleSegment->comparable) checked="checked" @endif>
    <label for="active">Comparable</label>
</div>
