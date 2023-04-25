<template>
    <div class="row package checkout-cart row-padding">
        <div class="col-xs-12">
            <div class="course-item" :class="{'iframe-payment-page-checkout-card':this.iframe}">
                <div class="course-item-background">
                    <div class="medium-text">Checkout</div>
                </div>
                <div class="course-item-content">
                    <slot name="inner-addons"></slot>
                    <template v-if="courses" v-for="course in courses">
                        <div class="course-box">
                            <h3 class="course-box__title">{{ course.segment && course.segment.label }} <span v-if="course.part > 0">Part {{ course.part }}</span></h3>
                            <div class="course-box__price">{{ course.price }} kr</div>
                            <div class="course-box__multiply" v-if="!schoolPage">
                                <span class="text-numerical">&times; {{ countParticipants(course) }}</span> deltagare
                            </div>
                            <div class="course-box__bill-wrap">
                                <div v-if="!schoolPage">
                                    <div class="course-box__total">Delsumma:</div>
                                    <div class="course-box__sum">{{ subtotal(course) }} kr</div>
                                </div>
                                <div class="course-box__delete" @click="handleDelete(course)">
                                    <div class="course-box__trash"></div>
                                    <div class="course-box__remove" >Ta bort</div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div v-show="parseFloat(user.amount) > 0 && giftCardsAmount < totalPricePure" class="addon-container">
                      <div class="addon" :class="{'flex-0-0-75':this.iframe}">
                        <div class="addon__controls">
                          <div>Bonus Saldo</div>
                          <div class="course-box__sum">- {{ (parseFloat(totalPricePure + bookingFee > user.amount ? user.amount : totalPricePure + bookingFee).toFixed(2)) + 'kr' }} </div>
                        </div>
                      </div>
                    </div>

                    <div v-if="giftCards" class="addon-container">
                    <div v-for="giftCard in giftCards" class="addon">
                      <div class="addon__controls">
<!--                        <div>{{ giftCard.title}}</div>-->
                        <div>Rabattkod: {{ giftCard.token }}</div>
                        <div class="course-box__sum">- {{ (giftCard.gift_card_type_id === 100000 ? parseFloat(totalPricePure * (giftCard.remaining_balance / 100)).toFixed(2) : giftCard.remaining_balance) + 'kr' }} </div>
                      </div>
                    </div>
                  </div>

<!--                  theory_online_discount-->
                  <div v-if="courses" class="addon-container">
                    <div v-for="course in courses">
                      <div v-if="user.theory_online_discount && course.segment.id === 32">
                        <div class="addon" :class="{'flex-0-0-75':this.iframe}">
                          <div class="addon__controls">
                            <div>Körkortsteori och Testprov</div>
                            <div class="course-box__sum">- {{user.theory_online_discount.amount}} %</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                    <div class="addon-container">
                      <div class="addon" :class="{'flex-0-0-75':this.iframe}">
                        <div class="addon__controls">
                          <div>Bokningsavgift</div>
                          <div class="course-box__sum">{{ bookingFee }} kr</div>
                        </div>
                      </div>
                    </div>

                    <div class="school-price">
                        <span class="total">Total:</span>
                        <span class="price">{{totalPrice}} kr</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Icon from 'vue-components/Icon';
    import $ from 'jquery';
    import _ from 'lodash';

    export default {
        components: {
            Icon,
        },
        props: {
            bookingFee: {
              default: 39
            },
            user: {
              default: () => []
            },
            giftCards: {
                default: () => []
            },
            courses: {
                default: () => []
            },
            participants: {
                default: 1
            },
            students: {
                default: () => []
            },
            tutors: {
                default: () => []
            },
            addons: {
                default: () => []
            },
            giftCardTypes: {
                default: () => []
            },
            schoolPage: {
                default: false
            },
            hasKlarna: {
                default: false
            },
            mountCollapsed: {
                default: false
            },
            disabled: {
                default: false
            },
            enableCheckout: {
                default: false
            },
            paymentPage: {
                default: false
            },
            schoolName: {
                default: ''
            },
            onCourseDelete: {
                default: () => []
            },
            iframe:Boolean
        },
        data() {
            return {
                collapsed: this.mountCollapsed,
                windowWidth: window.innerWidth,
                onDeleteCourseCallback: () => {}
            };
        },
        computed: {
            attendees(){
                return [...this.students, ...this.tutors];
            },
            giftCardsAmount() {
                let total = 0;

                if (this.giftCards.length) {
                    this.giftCards.forEach(giftCardType => {
                        total += parseFloat(giftCardType.remaining_balance);
                    });
                }

                return Math.max(total, 0);
            },
            totalPricePure() {
                let total = 0;
                this.courses.forEach(course => {
                    total += this.subtotal(course);
                });

                if (this.giftCardTypes.length) {
                    this.giftCardTypes.forEach(giftCardType => {
                        total += parseInt(giftCardType.price);
                    });
                }

                total = _.reduce(this.addons, (sum, addon) => {
                    return sum + addon.quantity * addon.price
                }, total)

                return Math.max(total, 0);
            },
            totalPrice() {
                let total = 0,
                  saldo = 0;
                this.courses.forEach(course => {
                  if (this.user.theory_online_discount && course.segment.id === 32) {
                    total = total + (this.subtotal(course) - this.subtotal(course) * this.user.theory_online_discount.amount / 100);
                  } else {
                    total += this.subtotal(course);
                  }
                });

                if (this.giftCardTypes.length) {
                    this.giftCardTypes.forEach(giftCardType => {
                        total += parseInt(giftCardType.price);
                    });
                }

                total = _.reduce(this.addons, (sum, addon) => {
                    return sum + addon.quantity * addon.price
                }, total)

                total = total - _.sum(this.giftCards.map(gc => gc.gift_card_type_id === 100000 ? total * (parseInt(gc.remaining_balance)/100) : parseInt(gc.remaining_balance)));

                if(total > 0) {
                    total += parseInt(this.bookingFee);
                }

                saldo = this.user ? parseFloat(this.user.amount) : 0;
                if (saldo > 0 && total > 0) {
                    total -= saldo;
                }

                return Math.max(Math.round(total * 100) / 100, 0);
            },
            selectionMade() {
                return this.courses.length > 0 || this.giftCardTypes.length > 0 || this.addons.length > 0;
            },
            isDesktop() {
                return this.windowWidth >= 992;
            }
        },
        watch: {
            giftCardTypes(oldVal, newVal) {
                this.collapsed = (newVal.length === 0);
            },
        },
        methods: {
            countParticipants(course) {
                return this.attendees.length ?
                    this.attendees.filter(attendee => {
                        return attendee.courseId === course.id;
                    }).length : this.participants;
            },
            subtotal(course) {
                let price = parseInt(course.price)
                return (this.attendees.length? this.countParticipants(course) : this.participants) * price;
            },
            toggleCollapse(event) {
                event.stopPropagation();
                this.collapsed = !this.collapsed;
            },
            toCheckout() {
                if (this.selectionMade) {
                    this.$emit('toCheckout');
                }
            },
            handleWindowResize(event) {
                this.windowWidth = event.currentTarget.innerWidth;
            },
            handleDelete(course) {
                this.onDeleteCourseCallback(course);
            },
        },
        created() {
            //Only mount collapsed if we are forcing it or if width is to small
            this.collapsed = !this.mountCollapsed ?
                !this.isDesktop :
                true;
        },
        beforeDestroy: function () {
            window.removeEventListener('resize', this. handleWindowResize)
        },
        mounted() {
            window.addEventListener('resize', this.handleWindowResize);
            this.onDeleteCourseCallback = this.onCourseDelete();
        }
    }
</script>
