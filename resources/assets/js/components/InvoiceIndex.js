var InvoiceIndex = new Vue({
    el: '#invoice-index',
    data: {
        invoices: [],
        routes: routes
    },
    methods: {
        getInvoices: function () {
            var vm = this;
            this.$http.get(routes.route('api::invoices.all')).then(function (response) {
                vm.invoices = response.body;
            }, function (error) {
                // handle error
            });
        },
        mounted: function () {
            this.getInvoices();
        }
    }
});

InvoiceIndex.mounted();