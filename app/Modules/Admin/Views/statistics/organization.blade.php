@extends('admin::layouts.default') @section('content')
<div id="statistics-page">
    <div class="row">
        <div class="col-xs-12 col-xxl-3">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-xxl-12">
                    <div class="card card-block">
                        <div class="grey">Antal upplagda kurser</div>
                        <div class="total">{{ $totalCourses }} st</div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-xxl-12">
                    <div class="card card-block">
                        <div class="grey">Antal förmedlade kontakter via Körkortsjakten</div>
                        <div class="total">{{ $totalContacts }} st</div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-xxl-12">
                    <div class="card card-block">
                        <div class="grey" style="text-decoration: underline!important; font-weight: 600; ">Area Average:</div>
                        @foreach($avgCourses as $course)
                            <div class="grey">
                                <span style="float: left">{{ trans($course->name) }}</span>
                                <span style="float: right">{{ number_format((float)$course->avg, 2, ',', '') }} </span>
                            </div>
                            <br/>
                        @endforeach
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-xxl-12">
                    <div class="card card-block">
                        <div class="grey" style="text-decoration: underline; font-weight: 600; ">School Average:</div>
                        @foreach($avgCoursesOrg as $courseOrg)
                            <div class="grey">
                                <span style="float: left">{{ trans($courseOrg->name) }}</span>
                                <span style="float: right">{{ number_format((float)$courseOrg->avg, 2, ',', '') }} </span>
                            </div>
                            <br/>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-xs-12">
                <div class="card card-block">
                    <user-types-chart :data="{{ $bookingInfo }}" title="Bokningsinformation"></user-types-chart>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-xxl-9">
            <div class="card card-block">
                <summary-chart :organization="{{ $organization }}"></summary-chart>
            </div>
        </div>
    </div>
</div>
@endsection
