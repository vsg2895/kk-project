<template>
    <div class="cart-global" :class="{'school-page' : schoolPage, 'has-klarna-page' : hasKlarna, 'payment-page' : paymentPage }">
      <div v-show="totalPrice">
        <button class="cart-visibility-toggle" @click="changeCollapsedCart">
          <span class="far fa-times-circle header-item header-item-right ico-menu-circle"></span>
<!--          <icon name="cross" size="lg"></icon>-->
        </button>
        <div class="card-block">
          <h3>Prisinformation</h3>
          <div class="cart-items">
            <div v-if="totalPrice">
              <div class="cart-item">
                <div>Bokningsavgift
                  <span class="text-numerical text-primary">&times; 1</span>
                </div>
                <div class="text-numerical">{{ bookingFee }} kr
                  <span class="small text-muted">({{ bookingFee }}/st)</span>
                </div>
              </div>
            </div>

            <template v-for="course in courses">
              <div class="cart-item-list">
                <div class="cart-item">
                  <p class="cart-item-label">{{ course.segment && course.segment.label }} </p>
                  <span class="cart-item-price">{{ course.price }} kr</span>
                </div>
              </div>
              <div class="cart-item participants-count" v-if="schoolPage">
                <span class="text-numerical">&times; {{ countParticipants(course) }}</span> deltagare
              </div>
              <div class="cart-item subtotal" v-if="schoolPage">
                <h4>Delsumma</h4>
                <div class="text-numerical">{{ subtotal(course) }} kr</div>
              </div>
            </template>

            <div v-if="giftCardTypes.length">
              <h4>Presentkort</h4>
              <div class="cart-item-list">
                <div class="cart-item" v-for="giftCardType in giftCardTypes">
                  <p class="cart-item-label">{{giftCardType.name}}</p>
                  <span class="cart-item-price">Värde: {{giftCardType.value}} kr</span>
                </div>
              </div>
            </div>

            <div class="cart-addons" v-if="addons.length">
              <h4>Tillval</h4>
              <div class="cart-item-list">
                <div class="cart-item" v-for="addon in addons">
                  <div>{{ addon.name }}
                    <span class="text-numerical text-primary">&times; {{ addon.quantity }}</span>
                  </div>
                  <div class="text-numerical">{{ addon.price * addon.quantity }} kr
                    <span class="small text-muted">({{ addon.price }}kr/st)</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="cart-gift-cards" v-if="giftCards.length">
              <div class="cart-item-list">
                <div class="cart-item" v-for="giftCard in giftCards">
                  <div>Presentkort</div>
                  <div class="text-numerical">-{{ giftCard.remaining_balance }} kr</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="cart-total">
          <div class="h4">Att betala
            <span class="hidden-md-up">: </span>
          </div>
          <div class="h2 text-numerical">{{ totalPrice }} kr</div>

          <div>
            <div class="display-block" v-if="schoolPage">
              <button class="btn btn-black" :class="{ 'disabled' : !selectionMade || disabled }" @click="toCheckout">Till betalning</button>
            </div>
            <div v-else>
              <div class="display-block">
                <button class="btn btn-black" :class="{ 'disabled' : !selectionMade || disabled }" @click="toCheckout">Till betalning</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-show="!totalPrice">
        <h3>Cart is empty</h3>
      </div>

    </div>
</template>

<script>
    import Icon from 'vue-components/Icon';
    import $ from 'jquery';
    import _ from 'lodash';
    import Api from "vue-helpers/Api";
    import { mapGetters, mapState } from 'vuex';

    export default {
        components: {
            Icon,
        },
        props: {
            giftCards: {
                default: () => []
            },
            bookingFee: {
                default: 39
            },
            participants: {
                default: 1
            },
            tutors: {
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
            paymentPage: {
                default: false
            }
        },
        data() {
            return {
                collapsed: this.mountCollapsed,
                windowWidth: window.innerWidth
            };
        },
        computed: {
            ...mapGetters('cart', ['qty', 'courseIds', 'addonIds', 'customIds']),
            ...mapState('cart', ['students', 'addons', 'courses']),
            attendees() {
              return [...this.students, ...this.tutors];
            },
            totalPrice() {
                let total = 0;
                if (this.courses && this.courses.length) {
                  this.courses.forEach(course => {
                    total += this.subtotal(course);
                  });
                }

                if (this.giftCardTypes.length) {
                    this.giftCardTypes.forEach(giftCardType => {
                        total += parseInt(giftCardType.price);
                    });
                }

                total = _.reduce(this.addons, (sum, addon) => {
                    return sum + addon.quantity * addon.price
                }, total)

                total = total - _.sum(this.giftCards.map(gc => parseInt(gc.remaining_balance)));

                if(total > 0) {
                    total += parseInt(this.bookingFee);
                }

                return Math.max(total, 0);
            },
            selectionMade() {
                return this.courses.length > 0 || this.giftCardTypes.length > 0 || this.addons.length > 0;
            }
        },
        watch: {
            giftCardTypes(oldVal, newVal) {
                this.collapsed = (newVal.length === 0);
            },
        },
        methods: {
            changeCollapsedCart() {
              this.$emit('changecollapsedcart');
            },
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
                event.stopPropagation()
                this.collapsed = !this.collapsed;
            },
            toCheckout() {
                if (this.totalPrice > 0 || this.qty > 0) {
                    window.onbeforeunload = null;
                    Api.toCheckout(this.$localStorage.school, this.courseIds, this.addonIds, this.customIds, this.students);
                    // this.$emit('toCheckout');
                }
            },
            handleWindowResize(event) { this.windowWidth = event.currentTarget.innerWidth; },
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
        }
    }
</script>
