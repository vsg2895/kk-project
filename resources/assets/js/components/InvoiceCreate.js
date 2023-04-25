var order = JSON.parse($('#order').val());

var InvoiceCreate = new Vue({
    el: '#invoice-create',
    data: {
        schools: [],
        schoolId: order ? order.school_id : null,
        order: order,
        rows: order ? _.map(order.items, function(item) {
            return {
                amount: (item.amount * item.provision) / 100,
                quantity: item.quantity,
                name: item.type
            }
        }) : []
    },
    methods: {
        addRow: function () {
            this.rows.push({
                amount: 0,
                quantity: 1,
                name: ''
            })
        },
        createInvoice: function () {
            var data = {
                order_id: this.order ? this.order.id : null,
                school_id: this.schoolId,
                rows: this.rows
            };

            this.$http.post(routes.route('api::invoices.store'), data).then(function (response) {
                // handle response.body
            }, function (error) {
                // handle error
            });
        },
        removeRow: function (index) {
            this.rows.splice(index, 1)
        },
        getSchools: function () {
            var vm = this;
            this.$http.get(routes.route('api::schools.all')).then(function (response) {
                vm.schools = response.body;
            }, function (error) {
                // handle error
            });
        },
        mounted: function () {
            if (!this.order) {
                this.addRow();
                this.getSchools();
            }
        }
    }
});

InvoiceCreate.mounted();