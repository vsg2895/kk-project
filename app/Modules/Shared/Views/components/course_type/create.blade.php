{{ csrf_field() }}

<div class="form-group @if($errors->has('title')) has-error @endif">
    <label for="title">Title</label>
    <input class="form-control" type="text" name="title" id="title" value="{{ old('title') }}"/>
</div>

<div class="form-group @if($errors->has('name')) has-error @endif">
    <label for="name">Name</label>
    <input class="form-control" placeholder="COURSE_CAR" type="text" name="name" id="name" value="{{ old('name') }}"/>
</div>

<div class="form-group">
    <label for="vehicle_id">Vehicles</label>
    <select id="vehicle_id" name="vehicle_id">
        @foreach($vehicles as $vehicle)
            <option value="{{ $vehicle->id }}" @if(old('vehicle_id') == $vehicle->id) selected @endif >{{ $vehicle->name }}</option>
        @endforeach
    </select>
</div>

<div class="form-group @if($errors->has('slug')) has-error @endif">
    <label for="title">Slug</label>
    <div class="input-group">
        <span class="input-group-addon">{{ url('/') }}/kurser/</span>
        <input class="form-control"  name="slug" id="slug" aria-describedby="uriHelp" placeholder="Skriv uri länk"
               value="{{ old('slug') }}">
    </div>
</div>

<div class="form-group @if($errors->has('description')) has-error @endif">
    <label for="description">Description</label>
    <input class="form-control" type="text" name="description" id="description" value="{{ old('description') }}"/>
</div>

<div class="form-group @if($errors->has('sub_description')) has-error @endif">
    <label for="sub_description">Sub Description</label>
    <input class="form-control" type="text" name="sub_description" id="sub_description" value="{{ old('sub_description') }}"/>
</div>

<div class="form-group @if($errors->has('sub_explanation')) has-error @endif">
    <label for="partner">Sub Explanation</label>
    <textarea class="form-control" type="text" name="sub_explanation" id="sub_explanation" value="{{ $vehicleSegment->sub_explanation }}">
        {{ $vehicleSegment->sub_explanation }}
    </textarea>
</div>

<div class="form-group @if($errors->has('explanation')) has-error @endif">
    <label for="explanation">Explanation</label>
    <input class="form-control" type="text" name="explanation" id="explanation" value="{{ old('explanation') }}"/>
</div>

<div class="form-group @if($errors->has('sub_explanation')) has-error @endif">
    <label for="sub_explanation">Sub Explanation</label>
    <textarea class="form-control" type="text" name="sub_explanation" id="sub_explanation" value="{{ old('sub_explanation') }}">
        {{ old('sub_explanation') }}
    </textarea>

</div>

<div class="form-group @if($errors->has('default_price')) has-error @endif">
    <label for="default_price">Default Price</label>
    <input type="number" id="default_price" name="default_price" value="{{ old('default_price') }}" />
</div>

<div class="form-group @if($errors->has('editable')) has-error @endif">
    <input type="checkbox" name="editable" id="editable" checked="{{ old('editable') }}">
    <label for="active">Editable</label>
</div>

<div class="form-group @if($errors->has('bookable')) has-error @endif">
    <input type="checkbox" name="bookable" id="bookable" checked="{{ old('bookable') }}">
    <label for="active">Bookable</label>
</div>

<div class="form-group @if($errors->has('comparable')) has-error @endif">
    <input type="checkbox" name="comparable" id="comparable" checked="{{ old('comparable') }}">
    <label for="active">Comparable</label>
</div>
