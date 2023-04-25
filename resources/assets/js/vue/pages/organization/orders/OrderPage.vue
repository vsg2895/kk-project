<template>
    <div v-if="order.user">
        <header class="section-header">
            <h1 class="page-title">Beställning #{{ order.id }} av
                <span v-if="order.user.name">{{ order.user.name }}</span>
                <span v-else>{{ order.user.email }}</span>
            </h1>
            <h2 class="text-danger" v-if="order.cancelled">Beställningen är avbokad</h2>
        </header>

        <div v-if="status" class="alert" role="alert"
             :class="{'alert-success': status === 'UPDATED', 'alert-info': status === 'CANCELLED'}">
            <button type="button" class="close" data-dismiss="alert" aria-label="Stäng">
                <span aria-hidden="true">&times;</span>
            </button>

            <template v-if="status === 'UPDATED'">
                <strong>Beställning uppdaterad</strong>
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
                    <h3>{{ item.name }}
                        <small v-if="item.course">{{ item.course.start_time }}</small>
                    </h3>
                    <p v-if="item.participant">
                        Namn: <strong>{{ item.participant.name }}</strong><br>
                        Personnummer: <strong>{{ item.participant.social_security_number }}</strong><br>
                        Typ:
                        <strong v-if="item.participant.type == 'TUTOR'">Elev / Handledare </strong>
                        <strong v-else>Elev</strong>
                        <br>
                        Antal: {{ item.quantity }}
                    </p>

                    <p>{{ item.statusText }}</p>

                    <template v-if="!order.cancelled">
                        <label class="custom-control custom-checkbox custom-block">
                            <input :disabled="!!item.external_invoice_id || !!item.delivered"
                                   class="custom-control-input" type="checkbox" v-model="item.delivered"/>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Kursen genomförd av kunden</span>
                        </label>
                    </template>
                </div>
            </div>
        </div>
        <button v-if="!order.cancelled" class="btn btn-primary" @click="updateOrder">Spara</button>
        <button v-if="canBeCancelled" class="btn btn-danger" @click="cancelOrder">Avboka</button>
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
                status: null,
                routes: routes
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

                return canBe;

            }
        },
        methods: {
            async findOrder() {
                this.order = await Api.findOrder(this.orderId);
                this.setItemStatus();

            },
            async updateOrder() {
                this.order = await Api.updateOrder(this.order);
                this.setItemStatus();
                this.status = 'UPDATED';
            },
            async cancelOrder() {
                this.order = await Api.cancelOrder(this.order);
                this.status = 'CANCELLED';
                this.setItemStatus();
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
