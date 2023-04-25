@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <h1 class="page-title">Update review</h1>
    </header>
    <div class="card card-block">
        {!! Form::model($rating, ['route' => ['admin::ratings.update', $rating->id], 'method' => 'POST']) !!}
        <div class="form-group row">
            <div class="col-md-6">
                <label class="form-control-label">Rate</label>
                {!! Form::number('rating', $rating->rating, ['class' => 'form-control', 'min' => 1, 'max' => 5]) !!}
            </div>
        </div>
        <div class="form-group">
            <label class="form-control-label">Title</label>
            {!! Form::text('title', $rating->title, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            <label class="form-control-label">Content</label>
            {!! Form::textarea('content', $rating->content, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            <label>School</label>
            {!! Form::select('school_id', \Jakten\Models\School::all()->pluck('name', 'id'), $rating->school_id, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            <label>User</label>
            {!! Form::select('user_id', \Jakten\Models\User::where('role_id',1)->get()->pluck('name', 'id'), $rating->user_id, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            <label>Verified</label>
            {!! Form::select('verified', ['No', 'Yes'], $rating->verified, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            <button class="btn btn-success" type="submit">Skapa</button>
        </div>
        {!! Form::close() !!}
    </div>
@endsection
