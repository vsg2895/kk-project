@extends('admin::layouts.default')
<style>

</style>
@section('content')
    @include('shared::components.message')
    @include('shared::components.errors')
    <header class="section-header">
        <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('admin::orders.index') !!}">@icon('arrow-left')
            Tillbaka</a>
        <h1 class="page-title">Edit Order</h1>
    </header>
    <div class="card card-block" id="order-item-content">
        <div class="row">
            <div class="col-md-12">
                <p>
                    Namn: {{ $order->user->name }}<br>
                    Telefonnummer: {{ $order->user->phone_number ?: 'Saknas' }}<br>
                    Email: {{ $order->user->email }}<br>
                    Betalningssätt: {{ trans('payment.types.' . $order->payment_method) }} @if($order->includesGiftCard() && !$order->isGiftCardOrder())
                        & Körkortsjakten @endif<br>
                    @if($order->items->count())
                        @if(!$order->getFirstOrderItem()->isGiftCard())
                            Status: {{ $order->handled ? $order->cancelled ? 'Beställningen har avbokas' : 'Trafikskola har hanterat order' : 'Trafikskolan har inte hanterat ordern än' }}
                        @endif
                    @else
                        Status: <span class="text-danger">Fel (beställningsobjekt hittades inte).</span>
                    @endif
                </p>
                <form method="post" action="{{ route('admin::orders.update', ['id' => $order->id]) }}">
                    {{ csrf_field() }}
                    @php($courseIds = array_values(array_filter($order->items->pluck('course_id')->toArray())))
                    @if($order->school)
                        <div>School Id: <input type="text" name="school_id" value="{{$order->school_id}}"></div>
                    @endif
                    @if(count($courseIds))
                        <div>Course Id: <input type="text" name="course_id" class="mt-1"
                                               value="{{count($courseIds) ? $courseIds[0] : null}}"></div>
                    @endif
                    <div class="list">
                        @foreach($order->items as $index => $item)
                            <div class="list-item">
                                {{--                                {{ dump($item) }}--}}
                                <div class="d-flex justify-content-between">
                                    <h3>{{ $item->name }} @if($item->isCourse()) @if($item->course->part) {{ $item->course->part }} @endif
                                        <small>{{ $item->course->start_time->formatLocalized('%A %d:e %B, %Y (%H:%M)') }}</small>@endif
                                    </h3>
                                    <button type="button" class="btn btn-danger delete-order-items" data-id="{{ $item->id }}">Delete</button>
                                </div>
                                <div><strong>{{ $item->amount }} kr</strong></div>
                                <div>Antal: <strong>{{ $item->quantity }}</strong></div>

                                @if(!$item->isCourse())
                                    @if($item->packageParticipant)
                                        <input type="hidden" name="addons[{{$index}}][id]"
                                               value="{{$item->packageParticipant->id}}">
                                        <p>Deltagare: <input type="text" name="addons[{{$index}}][name]"
                                                             value="{{ $item->packageParticipant->name }}"
                                                             placeholder="Name">
                                            - <input type="text" name="addons[{{$index}}][ssn]"
                                                     value="{{ $item->packageParticipant->social_security_number }}"
                                                     placeholder="SSN">
                                            - <input type="text" name="addons[{{$index}}][email]"
                                                     value="{{ $item->packageParticipant->email }}" placeholder="Email">
                                        </p>
                                    @endif
                                @endif
                                @if($item->isCourse())
                                    @if($item->participant)
                                        <input type="hidden" name="partisipants[{{$index}}][id]"
                                               value="{{$item->participant->id}}">
                                        <p>Deltagare: <input type="text" name="partisipants[{{$index}}][name]"
                                                             value="{{ $item->participant->name }}" placeholder="Name">
                                            - <input type="text" name="partisipants[{{$index}}][ssn]"
                                                     value="{{ $item->participant->social_security_number }}"
                                                     placeholder="SSN">
                                            - <input type="text" name="partisipants[{{$index}}][email]"
                                                     value="{{ $item->participant->email }}" placeholder="Email">
                                        </p>
                                    @endif
                                @endif
                                @if(!$item->isGiftCard())
                                    <p>Status:
                                        @if ($item->delivered && !$item->credited)
                                            <strong class="text-success">Att
                                                fakturera: {{ (($item->amount * $item->quantity) * $item->provision) / 100 }}</strong>
                                        @elseif($item->credited)
                                            <strong class="text-danger">Har krediterats</strong>
                                        @endif
                                        @if($order->cancelled)
                                            <strong class="text-danger">Beställningen har avbokats</strong>
                                        @endif
                                        @if($order->rebooked)
                                            <strong class="text-warning">Ombokad</strong>
                                        @endif
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex flex-column">
                        <a href="{{ route('admin::orders.show', ['id' => $order->id]) }}"
                           class="btn btn-outline-warning">Avbryt</a>
                        <button type="submit" class="btn btn-outline-success mt-1">Spara</button>
                    </div>
                </form>
                <form method="post" id="delete-order-item" action="{{ route('admin::orders.order_details.delete') }}">
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('.delete-order-items').on('click', function () {
            confirm('This action will not be possible to revert. Do you want to continue?');
            let id = $(this).attr('data-id');
            $('<input>').attr({
                type: 'hidden',
                name:'item_id',
                value:id,
            }).appendTo('#delete-order-item');

            $('#delete-order-item').submit();
        })

    </script>
@endsection
