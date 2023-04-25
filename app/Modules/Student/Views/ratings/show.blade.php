@extends('student::layouts.default')
@section('content')
    <header class="section-header student-section">
        <h1 class="page-title">
            <a href="{{ route('shared::schools.show', ['citySlug' => $rating->school->city->slug, 'slug' => $rating->school->slug]) }}">{{ $rating->school->name }}</a>
        </h1>
    </header>

    @include('shared::components.message')
    @include('shared::components.errors')
    
    <div class="card card-block mx-0">
        <form class="form-block" method="POST" action="{{ route('student::ratings.update', ['id' => $rating->id]) }}">
            {{ csrf_field() }}


            

            <div class="row">
                <div class="col-lg-8 col-xl-6">
                    <div class="form-group">
                        <label class="form-control-label" for="value">Omdöme</label>
                        <span class="far  fa-star form-control-icon"></span>
                        <input class="form-control form-control-lg" name="rating" type="number" min="1" max="5" value="{{ old('rating', $rating->rating) }}">
                    </div>
                    <button type="submit" class="btn btn-success">Spara</button>
                </div>
            </div>
        </form>
        <a class="btn btn-sm btn-outline-danger" href="{{ route('student::ratings.delete', ['id' => $rating->id]) }}" data-method="delete" data-token="{{ csrf_token() }}" data-confirm="@lang('form.confirm_action')">Ta bort</a>
    </div>
@endsection
