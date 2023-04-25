@extends(auth()->user()->getRoleName().'::layouts.default')
@section('content')
    @include('shared::components.message')
    <header class="section-header">
        <h1 class="page-title">{{ __('notify.notiser') }}</h1>
    </header>
    <div class="card card-block mx-0">
        @if($notifyEvents->count())
            <div class="table">
                <div class="table-head table-row hidden-sm-down">
                    <div class="table-cell col-md-6">{{ __('notify.notis') }}</div>
                    @foreach(NotifyChannels::$types as $value)
                        <div class="table-cell col-md-2 text-md-left">{{ __('notify.'. $value) }}</div>
                    @endforeach
                </div>
                <form method="POST" action="{{ route(auth()->user()->getRoleName().'::notify.update') }}">
                    {{ csrf_field() }}
                    @foreach($notifyEvents as $event)
                        <div class="table-row">
                            <div class="table-cell col-md-6">
                                {{ __('notify.'. $event->label) }}
                            </div>
                            @foreach(NotifyChannels::$types as $value)
                                <div class="table-cell col-md-2">
                                    <div class="form-group">
                                        <input type="checkbox" name="events[{{ $event->id }}][{{ $value }}]"
                                               @if (in_array($value, $disabledTypes))
                                                    disabled
                                               @elseif(strpos($event->channels, $value))
                                                    checked
                                               @endif>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                    <div class="is-justify-space-between">&nbsp;</div>
                    <button class="btn btn-success" type="submit">{{ __('notify.spara') }}</button>
                </form>
            </div>
        @else
            <no-results title="Inga sidor hittades"></no-results>
        @endif
    </div>
@endsection