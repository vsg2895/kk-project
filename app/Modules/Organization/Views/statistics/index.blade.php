@extends('organization::layouts.default') @section('content')
<div id="statistics-page">
    @php
        $neededCoursesTypes = [\Jakten\Models\VehicleSegment::DIGITAL_INTRODUKTIONSKURS_ENGLISH, \Jakten\Models\VehicleSegment::DIGITAL_INTRODUKTIONSKURS_ARABISKA];
    @endphp
    <div class="row">
        <div class="col-xs-12 col-xxl-3">
            <left-side-filter
                :schools="{{ $schools }}"
                :total-courses="{{ $totalCourses }}"
                :total-contacts="{{ $totalContacts }}"
                :avg-courses="{{ $avgCourses }}"
                :avg-courses-org="{{ $avgCoursesOrg }}"
                :booking-info="{{ $bookingInfo }}"
                :needed-courses-types="{{ json_encode($neededCoursesTypes) }}">

            </left-side-filter>
        </div>
        <div class="col-xs-12 col-xxl-9">
            <div class="card card-block">
                <summary-chart :more-info="true" :organization="{{ $organization }}" :schools="{{ $schools }}" :loyalty-levels="{{ $loyaltyLevels }}"></summary-chart>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="card card-block">
                <loyalty-progress :schools="{{ $schools }}" :loyalty-levels="{{ $loyaltyLevels }}" :show-more="true" />
            </div>
        </div>
    </div>
</div>
@endsection
