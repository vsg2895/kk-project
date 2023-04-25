@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <h1 class="page-title">Reports</h1>
    </header>
    @include('shared::components.message')

    <div class="card card-block">
        <div class="table">
            <div class="table-head table-row hidden-sm-down">
                <div class="table-cell col-md-3">Report Name</div>
            </div>

            <div class="table-row">
                <div class="table-cell col-md-12">
                    <a href="{{ route('admin::reports.orders') }}">Orders by date and school</a>
                </div>
                <div class="table-cell col-md-12">
                    <a href="{{ route('admin::reports.students') }}">Students</a>
                </div>
            </div>

        </div>
    </div>
@endsection
