@extends('admin::layouts.default')
@section('content')
    <div id="invoice-edit">
        <input id="invoice" type="hidden" value="{{ json_encode($invoice) }}" />
        <div class="row">
            <span v-if="invoice.sent">SKICKAD</span>
            <span v-if="invoice.paid">BETALAD</span>
            <div v-for="row in invoice.rows" class="col-md-12">
                <p>Namn: @{{ row.name }}</p>
                <p>Antal: @{{ row.quantity }}</p>
                <p>Pris: @{{ row.amount }}</p>
            </div>
            <button v-if="!invoice.paid" class="btn btn-primary" v-on:click="setPaid">Betalad</button>
            <button v-if="!invoice.sent" class="btn btn-success" v-on:click="setSent">Skickad</button>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script type="text/javascript" src="{{ elixir('js/components/InvoiceEdit.js') }}"></script>
@endsection
