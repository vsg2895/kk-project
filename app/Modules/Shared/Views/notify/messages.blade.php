@extends(auth()->user()->getRoleName().'::layouts.default')
@section('content')
    @include('shared::components.message')
    <header class="section-header">
        <h1 class="page-title">{{ __('notify.messages') }}</h1>
    </header>
    <div class="card card-block">
        @if($user->unreadNotifications->count())
            <div class="table">
                <div class="table-head table-row hidden-sm-down">
                    <div class="table-cell col-md-6">{{ __('notify.notis') }}</div>
                    <div class="table-cell col-md-2 text-md-center">{{ __('notify.action') }}</div>
                </div>
                @foreach($user->unreadNotifications as $notify)
                    <div class="table-row">
                        <div class="table-cell col-md-6">
                            {{ $notify->data['message'] }}
                        </div>
                        <div class="table-cell col-md-2 text-md-center">
                            <form method="POST">
                                {{ csrf_field() }}
                                <input type="hidden" name="notify_id" value="{{ $notify->id }}">
                                <a href="javascript:void(0)" onclick="parentNode.submit();">{{ __('notify.mark') }}</a>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <no-results title="Inga sidor hittades"></no-results>
        @endif
    </div>
@endsection