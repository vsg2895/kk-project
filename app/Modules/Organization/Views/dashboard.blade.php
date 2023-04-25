@extends('organization::layouts.default')
@section('body-class') page-dashboard @parent @stop

@section('content')
    @if($claims->count())
        <div class="card card-block mx-0">
            <h2>Du har gjort anspråk på</h2>
            @foreach($claims as $claim)
                <h3>- {{ $claim->school->name }}</h3>
            @endforeach
            <p>Vi har tagit emot din förfrågan och återkommer med besked.</p>
        </div>
    @endif

    <div class="row">
        @if($user->organization->sign_up_status != 'NOT_INITIATED')
            <div class="card">
                <div class="d-flex flex-column flex-md-row justify-content-center flex-column align-items-center p-1">
                    <h2 class="mb-0 px-1">Behöver du hjälp?</h2>
                    <p>Ring
                        <a href="tel:{{ config('services.phones.customers.regular') }}">{{ config('services.phones.customers.text') }}</a>
                    </p>
                </div>
            </div>
        @endif

        <partners :partners="{{ json_encode($partners) }}"></partners>

        @if(!$claims->count())
            <organization-dashboard :initial-organization="{{ json_encode($user->organization) }}"
                                    :onboarding="{{ json_encode($onboarding) }}"></organization-dashboard>
        @endif

        @if($user->organization->schools->count())
            <div class="col-md-6">
                <div class="bookings-card card card-block mx-0">
                    <h3 class="card-title">Senaste bokningar</h3>

                    @if($bookings->count())
                        <div class="list {{ $bookings->count() > 3 ? 'faded' : '' }}">
                            @foreach($bookings as $booking)
                                <div class="list-item">
                                    <h4>{{ $booking->participant->given_name }} <span
                                                class="text-muted">har bokat</span></h4>
                                    <a class="btn btn-sm btn-outline-primary float-sm-right"
                                       href="{{ route('organization::orders.show', ['id' => $booking->order_id]) }}">Visa</a>
                                    <div>{{ $booking->course->name }} <span class="text-numerical text-muted">{{ $booking->course->start_hour }} - {{ $booking->course->end_hour }}</span>
                                    </div>
                                    <div class="small">{{ $booking->course->start_time->formatLocalized('%A %d:e %B, %Y') }}</div>
                                </div>
                            @endforeach
                        </div>
                        <a class="btn btn-sm btn-outline-primary float-xs-right"
                           href="{{ route('organization::orders.index') }}">Visa alla bokningar</a>
                    @else
                        <no-results
                                title="Ni har inga bokningar ännu"
                        ></no-results>
                    @endif
                </div>
                @if($bookingCount)
                    <div class="card card-block mx-0">
                        <h2 class="mb-0">Antal bokningar:</h2>
                        <div class="display-4 text-success">{{ $bookingCount }} st</div>
                        <h3 class="text-muted">Värde totalt: {{ $orderValue }} SEK</h3>
                    </div>
                @endif
            </div>
            <div class="col-md-6">
                <div class="courses-card card card-block mx-0">
                    <h3 class="card-title">Kommande kurser</h3>

                    @if($courses->count())
                        <div class="list {{ $courses->count() > 4 ? 'faded' : '' }}">
                            @foreach($courses->slice(0, 4) as $course)
                                <div class="list-item">
                                    <a class="btn btn-sm btn-outline-primary float-sm-right"
                                       href="{{ route('organization::courses.show', ['id' => $course->id]) }}">Visa/redigera</a>
                                    <div class="small">{{ $course->start_time->formatLocalized('%A %d:e %B, %Y') }}</div>
                                    <div>{{ $course->name }} <span class="text-numerical text-muted">{{ $course->start_hour }} - {{ $course->end_hour }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <a class="btn btn-sm btn-outline-primary float-xs-right"
                           href="{{ route('organization::courses.index') }}">Visa alla kurser</a>
                    @else
                        <no-results>
                            <a slot="description" class="btn btn-outline-primary"
                               href="{{ route('organization::courses.create') }}">Skapa en kurs</a>
                        </no-results>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
@section('scripts')
    @parent
    <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_API_KEY') }}&libraries=places&language=sv&region=se"></script>
@endsection
