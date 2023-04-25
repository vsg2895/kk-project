<template>
    <div id="course-page">
        <template v-if="school && order.courses">
            <div class="container" v-if="user.role_id == 1">
                <div>
                    <a class="btn btn-sm btn-secondary mt-1" href="javascript:history.back()">Tillbaka</a>
                </div>

                <template v-if="!klarnaAvailable">
                    <div class="checkout-step">
                        Klarna är inte tillgänglig
                    </div>
                </template>

                <template v-else>
                    <klarna-checkout :validation="$v" :school-id="school.id" :addons="order.addons"
                                     :course-ids="[order.course.id]" :tutors="order.tutors"
                                     :students="order.students" :order-id="klarnaOrderId"
                                     :seats-available="order.course.seats" :gift-card-tokens="order.gift_card_tokens"
                                     ref="klarna_checkout" @validationUpdated="updateValidation"
                                     @courseUpdateOrder="updateOrder"/>
                </template>
            </div>
            <div class="page-pages" v-else>
                <div class="container text-xs-center">
                    <h3>Logga ut för att boka kurser</h3>
                    <a class="btn btn-primary logout-btn-coursepage" :href="routes.route('auth::logout')">Logga ut</a>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    import Api from 'vue-helpers/Api';
    import _ from 'lodash';
    import Checkout from 'vue-components/Checkout';
    import routes from 'build/routes.js';
    import Introduction from 'vue-pages/courses/Introduction';
    import RiskOne from 'vue-pages/courses/RiskOne';
    import Cart from 'vue-pages/courses/Cart';
    import Icon from 'vue-components/Icon';
    import Addon from 'vue-pages/courses/Addon';
    import moment from 'moment';

    import {
        required,
        minLength,
        between,
        email,
        numeric
    } from 'vuelidate/lib/validators';
    import {
        socialSecurityNumber
    } from 'components/CustomValidators.js';

    export default {
        components: {
            'klarna-checkout': Checkout,
            'introduction-participants': Introduction,
            'risk-one-participants': RiskOne,
            'cart': Cart,
            Icon,
            'addon': Addon
        },
        validations: {
            user: {
                email: {
                    required,
                    email
                },
                given_name: {
                    required
                },
                family_name: {
                    required
                },
                phone_number: {
                    required,
                    numeric
                }
            },
            loginUser: {
                email: {
                    required,
                    email
                },
                password: {
                    required
                }
            },
            order: {
                tutors: {
                    $each: {
                        given_name: {
                            required,
                            minLength: minLength(2)
                        },
                        family_name: {
                            required,
                            minLength: minLength(2)
                        },
                        social_security_number: {
                            socialSecurityNumber
                        },
                        email: {
                            required,
                            email
                        }
                    }
                },
                students: {
                    $each: {
                        given_name: {
                            required,
                            minLength: minLength(2)
                        },
                        family_name: {
                            required,
                            minLength: minLength(2)
                        },
                        social_security_number: {
                            socialSecurityNumber
                        },
                        transmission: {
                          requiredIf: function (val) {
                            return ((typeof this.order !== 'undefined' && this.order && this.order.course && this.order.course.vehicle_segment_id === 16)
                                && (val && val.length)) || (typeof this.order !== 'undefined' && this.order && this.order.course && this.order.course.vehicle_segment_id !== 16);
                          }
                        },
                        email: {
                            required,
                            email
                        }
                    }
                }
            }
        },
        props: {
            klarnaOrderId: {
                default: null
            },
            courseIds: {
                default: () => []
            }
        },
        data() {
            return {
                checkingGiftCard: false,
                pendingToken: '',
                state: 'neutral',
                routes: routes,
                start_time: '',
                school: {},
                order: {
                    addons: [],
                    courses: [],
                    course: {},
                    tutors: [],
                    students: [],
                    payment_method: 'KLARNA',
                    new_user: {},
                    gift_card_tokens: [],
                },
                giftCards: [],
                user: {
                    role_id: 1,
                    email: '',
                    given_name: '',
                    family_name: '',
                    phone_number: ''
                },
                loginUser: {
                    email: '',
                    password: ''
                },
                checkoutValid: false
            };
        },
        computed: {
            klarnaAvailable() {
                return true;
            },
            uniqueGiftCards() {
                return _.uniq(this.giftCards);
            },
            useGiftCards() {
                return this.school && this.school.accepts_gift_card;
            }
        },
        filters: {
            courseStartDate(rawDate) {
                let date = moment(rawDate);
                if (date) {
                    return date.format('dddd [den] D MMMM')
                } else {
                    return ''
                }
            },
            quantity(addon, order) {
                let orderAddon = order.addons.find(a => a.id === addon.id);
                return orderAddon ? orderAddon.quantity : 0;
            }
        },
        watch: {
            user: {
                handler() {
                    if (this.useGiftCards) {
                        this.giftCards.push(...this.user.gift_cards.filter(gc => !!parseInt(gc.remaining_balance)));
                    }
                },
                deep: true
            },
            giftCards: {
                handler() {
                    if (this.useGiftCards) {
                        this.order.gift_card_tokens = this.uniqueGiftCards.map(gc => gc.token);
                    }
                },
                deep: true
            }
        },
        methods: {
            removeGiftCard(id) {
                this.giftCards = this.giftCards.filter(gc => gc.id !== id);
            },
            updateValidation(valid) {
                this.checkoutValid = valid;
            },
            vehicleIconName(id) {
                switch (id) {
                    case 2:
                        return 'mc';
                    case 3:
                        return 'moped';
                    default:
                        return 'car';
                }
            },
            async checkGiftCard() {
                if (!this.checkoutValid) {
                    return //'adding gift cards disabled'
                }
                if (this.order.gift_card_tokens.includes(this.pendingToken)) {
                    return //'token already in order'
                }
                this.checkingGiftCard = true;
                let response = await Api.checkGiftCard(this.pendingToken);
                if (response.exists && !response.claimed) {
                    let giftCard = response.giftCard;
                    giftCard.token = this.pendingToken;
                    this.giftCards.push(giftCard)
                }
                this.checkingGiftCard = false;
                this.pendingToken = '';
            },
            updateOrder() {
                this.$refs.klarna_checkout.updateOrder();
            },
            async getData() {
                let id = JSON.parse(this.$localStorage.order).course.id;
                let [course, user] = await Promise.all([Api.findCourse(id), Api.getLoggedInUser()]);

                this.order = JSON.parse(this.$localStorage.order);

                if (parseInt(course.segment.vehicle_id) === 2) {
                    course.segment.label += ' MC';
                }

                this.$set(this.order, 'course', course);
                this.school = course.school;
            },
        },
        updated() {
        },
        created() {
            this.getData();
        }
    }
</script>
