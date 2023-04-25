<template>
    <div id="invoice-create">
        <div v-if="!order">
            <select v-model="schoolId">
                <option v-for="school in schools" :value="school.id">{{ school.name }}</option>
            </select>
        </div>
        <div class="row">
            <div v-for="(row, index) in rows" class="col-md-12">
                <label>Namn</label>
                <input type="text" v-model="row.name">
                <label>Antal</label>
                <input type="text" v-model="row.quantity">
                <label>Pris/st</label>
                <input type="text" v-model="row.amount">
                <button @click="removeRow(index)">Ta bort rad</button>
            </div>
            <button class="btn btn-primary" @click="addRow">Lägg till rad</button>
            <button class="btn btn-success" @click="createInvoice">Skapa faktura</button>
        </div>
    </div>
</template>

<script type="text/babel">
    import _ from 'lodash';
    export default {
        props: ['order'],
        data() {
            return {
                schools: [],
                schoolId: this.order ? this.order.school_id : null,
                rows: this.order ? _.map(_.filter(this.order.items, (item) => {
                    return item.delivered;
                }), (item) => {
                    return {
                        amount: (item.amount * item.provision) / 100,
                        quantity: item.quantity,
                        name: item.type
                    }
                }) : []
            };
        },
        watch: {
            prop (oldVal, newVal) {
            }
        },
        methods: {
            addRow() {
                this.rows.push({
                    amount: 0,
                    quantity: 1,
                    name: ''
                })
            },
            createInvoice() {
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
            removeRow(index) {
                let rows = this.rows;
                rows.splice(index, 1)
                this.rows = rows;
            },
            getSchools() {
                var vm = this;
                this.$http.get(routes.route('api::schools.all')).then(function (response) {
                    vm.schools = response.body;
                }, function (error) {
                    // handle error
                });
            },
        },
        mounted () {
            if (!this.order) {
                this.addRow();
                this.getSchools();
            }
        },
        destroyed () {
        }
    }
</script>

<style lang="scss" type="text/scss">

</style>