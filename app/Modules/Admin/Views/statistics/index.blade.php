@extends('admin::layouts.default') @section('content')
    <ul class="nav nav-tabs" role="tablist" style="margin-left: 15px">
        <li class="nav-item">
            <a class="nav-link active mx-0" data-toggle="tab" href="#statistics-page" role="tab">Statistics</a>
        </li>
        <li class="nav-item">
            <a class="nav-link mx-0" data-toggle="tab" href="#reports-page" role="tab">Reports</a>
        </li>
        <li class="nav-item">
            <a class="nav-link mx-0" data-toggle="tab" href="#monthly-reports-page" role="tab">Monthly Report</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="statistics-page" role="tabpanel">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-xl-4">
                    <div class="row">
                        <div class="col-xs-12 col-md-6 col-xl-12">
                            <div class="card card-block">
                                <div class="grey">Totalt antal upplagda kurser</div>
                                <div class="total">{{ $totalCourses }} st</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6 col-xl-12">
                            <div class="card card-block">
                                <div class="grey">Antal anslutna trafikskolor</div>
                                <div class="total">{{ $totalSchools }} st</div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="card card-block">
                                <user-types-chart :data="{{ $users }}"></user-types-chart>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-xl-8">
                    <div class="card card-block">
                        <course-statistics-table></course-statistics-table>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="card card-block">
                        <summary-chart :more-info="true"></summary-chart>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="reports-page" role="tabpanel">
            <form id="export-excel" method="GET"
                  action="{{ route('admin::statistics.report') }}">
                <div class="card card-block">
                    <div class="d-flex" style="flex-wrap: wrap;">
                        <div style="margin-right: 15px;">
                            <reports-dates></reports-dates>
                        </div>
                        <div style="flex-grow: 1;">
                            <div class="mt-2">
                                <input id="include_participants" type="checkbox" name="include_participants"/>
                                <label for="include_participants">Include Participants</label>
                            </div>
                            <div style="margin-bottom: 16px;">
                                <label for="segments">Course</label>
                                <select name="segment_id" class="form-control" id="segments">
                                    <option value="all">All</option>
                                    @foreach($segments as $segment)
                                        <option
                                            value="{{$segment->id}}">{{__('vehicle_segments.' . strtolower($segment->name))}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
            </form>
        </div>
        <div class="tab-pane" id="monthly-reports-page" role="tabpanel">
            <form id="export-monthly-excel" method="GET"
                  action="{{ route('admin::statistics.monthly-report') }}">
                <div class="card card-block">
                    <div class="d-flex" style="flex-wrap: wrap;">
                        <div style="margin-right: 15px;">
                            <reports-dates></reports-dates>
                        </div>
                        <div style="flex-grow: 1;">
                            <div class="mt-2">
                                <input id="is_daily" type="checkbox" name="is_daily"/>
                                <label for="is_daily">Daily</label>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection


