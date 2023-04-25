@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <h1 class="page-title">Sökalgoritm</h1>
    </header>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        var sortableData = [];

        function saveOrder() {

            $.post("{{ route('admin::search_algorithm.update') }}", { order: sortableData, "_token": "{{ csrf_token() }}"})
                .success(function(data) {
                    document.location.reload();
                })
                .error(function(data) {
                    document.location.reload();
                });
        }

        $( function() {
            $( ".sortable" ).sortable({
                axis: 'y',
                update: function (event, ui) {
                    sortableData = $(this).sortable('serialize');
                }
            });
            $( ".sortable" ).disableSelection();
        });
    </script>

    <div class="card card-block">

        @if(Session::has('errors'))
            <div class="alert alert-danger">
                <strong>Update fail</strong><br>
                {!! Session::get('errors') !!}
            </div>
        @endif

        <div class="col-md-6">
            <form action="{{ route('admin::search_algorithm.update') }}" method="POST">
                {{ csrf_field() }}
                <div class="mb-1 sortable">
                        @foreach($data as $key => $val)
                            @if(in_array($key, ['user_id', 'created_at']))
                                @continue
                            @endif
                            <div class="card-header collapsed" style="margin: 1px">
                                {{ $key }}
                                <input id="increased-value"
                                  type="number" min="1" step="1"
                                  name="{{ $key }}" class="form-control"
                                  placeholder="{{ $key }}" value="{{ $val }}">
                            </div>
                        @endforeach
                </div>
                <span class="input-group-btn">
                    <button onclick="saveOrder" class="btn btn-primary" type="submit">Uppdatera</button>
                </span>
            </form>

        </div>
    </div>
@endsection
