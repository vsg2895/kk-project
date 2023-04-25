var invoice = JSON.parse($('#invoice').val());

var InvoiceEdit = new Vue({
    el: '#invoice-edit',
    data: {
        invoice: invoice,
    },
    methods: {
        setPaid: function () {
            var vm = this;
            this.$http.post(routes.route('api::invoices.paid', {invoiceId: this.invoice.id})).then(function (response) {
                vm.invoice = response.body;
            }, function (error) {
                // handle error
            });
        },
        setSent: function () {
            var vm = this;
            this.$http.post(routes.route('api::invoices.sent', {invoiceId: this.invoice.id})).then(function (response) {
                vm.invoice = response.body;
            }, function (error) {
                // handle error
            });
        },
        mounted: function () {
        }
    }
});

InvoiceEdit.mounted();