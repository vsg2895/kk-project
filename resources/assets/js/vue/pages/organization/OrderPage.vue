<template>
    <div v-if="order.user">

        <BlockUI v-if="loader">
            Vänta...
        </BlockUI>

        <header class="section-header">
            <h1 class="page-title">Beställning #{{ order.id }} av
                <span v-if="order.user.name">{{ order.user.name }}</span>
                <span v-else>{{ order.user.email }}</span>
            </h1>
            <h2 class="text-danger" v-if="order.cancelled">Beställningen är avbokad</h2>
        </header>

        <div v-if="status" class="alert" role="alert"
             :class="{'alert-success': status === 'UPDATED', 'alert-info': status === 'CANCELLED', 'alert-danger' : status === 'FAILED'}">
            <button type="button" class="close" data-dismiss="alert" aria-label="Stäng">
                <span aria-hidden="true">&times;</span>
            </button>

            <template v-if="status === 'UPDATED'">
                <strong>Beställning uppdaterad</strong>
            </template>
            <template v-if="status === 'FAILED'">
                <strong v-html="statusText"></strong>
            </template>
            <template v-if="status === 'CANCELLED'">
                <strong>Beställning avbokad</strong>
            </template>
        </div>

        <div class="card card-block mx-0">
            <div class="list">
                <div class="list-item">
                    <h3>Bokningsinformation</h3>
                    <p>Namn: {{ order.user.name }}</p>
                    <p>Email: {{ order.user.email }}</p>
                    <p>Telefonnummer: {{ order.user.phone_number }}</p>
                </div>
                <div class="list-item" v-for="item in order.items">
                    <div v-if="!item.gift_card">
                        <h3>{{ item.name }}
                            <small v-if="item.course">{{ item.course.start_time }}</small>
                        </h3>
                        <span v-if="item.participant">
                            Namn: <strong>{{ item.participant.name }}</strong><br>
                            Personnummer: <strong>{{ item.participant.social_security_number }}</strong><br>
                            Typ:
                            <strong v-if="item.participant.type == 'TUTOR'">Elev / Handledare </strong>
                            <strong v-else>Elev</strong>
                        </span>
                        <p> Antal: {{ item.quantity }}</p>
                        <p>{{ item.statusText }}</p>

                        <p v-if="item.course && item.participant && item.participant.transmission">
                            Transmission: <strong>{{ item.participant.transmission }}</strong><br>
                        </p>
                    </div>
                    <div v-else>
                        <h3>Presentkort
                            <small>{{ item.amount }}</small>
                        </h3>
                        <span>Betalas ut av Körkortsjakten</span>
                    </div>
                </div>
            </div>
        </div>
        <button v-if="!order.cancelled" class="btn btn-primary" @click="updateOrder">Spara</button>
    </div>
</template>

<script type="text/babel">
    import _ from 'lodash';
    import Api from 'vue-helpers/Api';
    import routes from 'build/routes.js';
    import moment from "moment";

    export default {
        props: ['orderId'],
        data() {
            return {
                order: {},
                loader: false,
                status: null,
                routes: routes,
                statusText: ''
            };
        },
        watch: {
            prop(oldVal, newVal) {
            }
        },
        computed: {
            canBeCancelled() {
                if (!this.order) {
                    return false;
                }

                if (this.order.cancelled) {
                    return false;
                }

                let canBe = true;

                if (this.order.hasOwnProperty('items') && this.order.items.length > 0) {
                  _.each(this.order.items, (item) => {
                    if (item.course) {
                      if (moment(item.course.start_time).diff(moment(), 'days') <= 2) {
                        canBe = false;
                      }
                    } else {
                      if (moment().diff(moment(item.created_at), 'days') >= 14) {
                        canBe = false;
                      }
                    }
                  });
                }

                return canBe;
            }
        },
        methods: {
            switchLoader(state = false) {
                this.loader = state;
            },
            async findOrder() {
                this.order = await Api.findOrder(this.orderId);
                this.setItemStatus();

            },
            async updateOrder() {

                this.switchLoader(true);

                let activate = this.order.items
                        .filter(item => item['delivered'] === true && !item['external_invoice_id'])
                        .map(item => item.id),
                    cancel = this.order.items
                        .filter(item => item['external_invoice_id'] && item['credited'])
                        .map(item => item.id);

                let response = await Api.activateOrCancelOrder(this.order.id, {activate, cancel});
                this.statusText = '';

                this.setItemStatus();

                if (!response.hasOwnProperty('status')) {
                    this.order = response.order;
                    this.status = 'UPDATED';

                    this.$swal({
                        type: 'success',
                        title: 'Framgång!',
                        text: 'Beställningen har uppdaterats!',
                        showConfirmButton: false,
                        showCancelButton: false,
                    });
                } else {
                    this.status = 'FAILED';
                    this.statusText = response.message;

                    this.$swal({
                        type: 'error',
                        title: 'Fel!',
                        text: 'Det gick inte att uppdatera en beställning!',
                        showConfirmButton: false,
                        showCancelButton: false,
                    });
                }

                this.switchLoader();
            },
            async cancelOrder() {
                this.switchLoader(true);

                let response = await Api.cancelOrder(this.order);
                /** Uncomment only for debug **/
                console.log('Log Response', response);

                if (!response.hasOwnProperty('status')) {
                    this.order = response.order;
                    this.status = 'UPDATED';

                    this.$swal({
                        type: 'success',
                        title: 'Framgång!',
                        text: 'Beställningen avbruten!',
                        showConfirmButton: false,
                        showCancelButton: false
                    });
                } else {
                    response = response.body;

                    this.status = 'FAILED';
                    this.statusText = response.message;
                    this.order = response.order;

                    this.$swal({
                        type: 'error',
                        title: 'Fel!',
                        html: response.message,
                        showConfirmButton: false,
                        showCancelButton: false
                    });
                }

                this.status = 'CANCELLED';
                this.setItemStatus();

                this.switchLoader();
            },
            setItemStatus() {
                _.each(this.order.items, (item) => {
                    item.originalCredited = Boolean(item.credited);

                    if (item.credited) {
                        item.statusText = 'Artikeln har krediterats';
                    } else if (item.cancelled) {
                        item.statusText = 'Artikeln har avbokats';
                    } else if (item.delivered) {
                        item.statusText = 'Artikeln har levererats';
                    } else {
                        item.statusText = 'Artikeln har inte hanterats än';
                    }
                });
            }
        },
        mounted() {
            this.findOrder();
        },
        destroyed() {
        }
    }
</script>

<style lang="scss" type="text/scss">

</style>
