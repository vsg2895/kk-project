@extends('admin::layouts.default')
@section('content')
    <div id="invoice-index" class="row">
        <div v-for="invoice in invoices" class="col-md-12">
            <a v-bind:href="routes.route('admin::invoices.show', {id: invoice.id})">@{{ invoice.id }}</a>
        </div>
        <a class="btn btn-primary" href="{{ route('admin::invoices.create') }}">Skapa</a>
    </div>
@endsection
@section('scripts')
    @parent
    <script type="text/javascript" src="{{ elixir('js/components/InvoiceIndex.js') }}"></script>
@endsection
