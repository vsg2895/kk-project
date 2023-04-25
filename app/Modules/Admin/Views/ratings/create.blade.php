@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <h1 class="page-title">Skapa omdöme</h1>
    </header>
    <div class="card card-block col-lg-6 mx-0">
        {!! Form::open(['route' => 'admin::ratings.store', 'method' => 'POST']) !!}
            <div class="form-group row">
                <div class="col-md-6">
                    <label class="form-control-label">Betyg</label>
                    <input class="form-control " type="number" min="1" max="5" name="rating" value="5">
                </div>
            </div>
            <div class="form-group">
                <label class="form-control-label">Rubrik</label>
                <input type="text" name="title" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-control-label">Kommentar</label>
                <textarea name="content" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label>Skola</label>

{{--                    <semantic-dropdown :on-item-selected="schoolChanged" form-name="school_id" :search="true"--}}
{{--                                       :readonly="true" placeholder="Skola"--}}
{{--                                       :data="{{ $schools }}">--}}
{{--                        <template slot="dropdown-item" slot-scope="props">--}}
{{--                            <div class="item" :data-value="props.item.id">--}}
{{--                                <div class="item-text">{{ props.item.name }} ({{ props.item.postal_city }})</div>--}}
{{--                            </div>--}}
{{--                        </template>--}}
{{--                    </semantic-dropdown>--}}
                <select name="school_id" class="form-control">
                    @foreach($schools as $school)
                        <option value="{{$school->id}}">{{$school->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Användare</label>
                <select name="user_id" class="form-control">
                    @foreach($users as $user)
                        <option value="{{$user->id}}">{{ $user->email }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <button class="btn btn-success" type="submit">Skapa</button>
            </div>
        {!! Form::close() !!}
    </div>
@endsection
