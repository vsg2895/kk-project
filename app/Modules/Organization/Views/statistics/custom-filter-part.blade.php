<div class="col-xs-12 col-xxl-3">
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-xxl-12 pb-1">
            <select class="custom-school-filter-button">
                @foreach($schools as $index => $school)
                    <option
                        value="{{$school->id}}" {{ $index === 0 ? 'selected' : '' }}>{{$school->name . '(' . $school->postal_city . ')'}}
                    </option>
                @endforeach
            </select>
        </div>
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
                @foreach($avgCourses as $KEY => $course)
                    @if(!in_array($course->id, [\Jakten\Models\VehicleSegment::DIGITAL_INTRODUKTIONSKURS_ENGLISH, \Jakten\Models\VehicleSegment::DIGITAL_INTRODUKTIONSKURS_ARABISKA]))
                        <div class="grey">
                            <span style="float: left">{{ trans($course->name) }}</span>
                            <span style="float: right">{{ number_format((float)$course->avg, 2, ',', '') }} </span>
                        </div>
                        <br/>
                    @endif
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

    <div class="col-xs-12 bookingArea">
        <div class="card card-block mx-0">
            <user-types-chart :data="{{ $bookingInfo }}" title="Bokningsinformation"></user-types-chart>
        </div>
    </div>
</div>
