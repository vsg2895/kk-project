<template>
    <div v-show="totalPrice"
    :class="{ 'school-page' : schoolPage, 'has-klarna-page' : hasKlarna, 'payment-page' : paymentPage }"
    class="card-mobile">
        <h3 class="card-mobile-title">Varukorg</h3>
        <div v-if="totalPrice">
            <div class="d-flex justify-content-between mb-1">
                <span class="cart-item-label">Bokningsavgift </span>
                <span class="cart-item-sum"> 1 &times; {{ bookingFee }} kr</span>
            </div>
        </div>

        <div class="cart-item-list" v-for="course in courses" :key="course.id">
            <div class="d-flex justify-content-between mb-1">
                <span class="cart-item-label">{{ course.segment && course.label }} </span>
                <span class="cart-item-sum">{{ countParticipants(course) }} &times; {{ subtotal(course) }} kr</span>
            </div>
        </div>

        <div v-if="giftCardTypes.length">
            <h4>Presentkort</h4>
            <div class="cart-item-list">
                <div class="d-flex justify-content-between mb-1" v-for="giftCardType in giftCardTypes" :key="giftCardType.id">
                    <span class="cart-item-label">{{ giftCardType.name }}</span>
                    <span class="cart-item-sum">Värde: {{ giftCardType.value }} kr</span>
                </div>
            </div>
        </div>

        <div class="cart-addons" v-if="addons.length">
            <div class="cart-item-list">
                <div class="d-flex justify-content-between mb-1" v-for="addon in addons" :key="addon.id">
                    <span class="cart-item-label">{{ addon.name }}</span>
                    <span class="cart-item-sum">{{ addon.quantity }} &times; {{ addon.price }} kr</span>
                </div>
            </div>
        </div>

        <div class="cart-gift-cards" v-if="giftCards.length">
            <div class="cart-item-list">
                <div class="d-flex justify-content-between mb-1" v-for="giftCard in giftCards" :key="giftCard.id">
                    <span class="cart-item-label">Presentkort</span>
                    <span class="cart-item-sum">-{{ giftCard.remaining_balance }} kr</span>
                </div>
            </div>
        </div>

        <div class="cart-total d-flex justify-content-between mb-1">
          <span>Att betala</span>
          <span class="cart-item-sum">{{ totalPrice }} kr</span>
        </div>

        <div class="d-flex justify-content-end">
            <button class="btn btn-success" :class="{ 'disabled' : !selectionMade || disabled }" @click="toCheckout">Till kassan</button>
        </div>
    </div>

</template>

<script>
    import Icon from 'vue-components/Icon';
    import _ from 'lodash';

    export default {
        components: {
            Icon,
        },
        props: {
            giftCards: {
                default: () => []
            },
            bookingFee: {
                required: true
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
            disabled: {
                default: false
            },
            enableCheckout: {
                default: false
            },
            paymentPage: {
                default: false
            }
        },
        data() {
            return {
                windowWidth: window.innerWidth
            };
        },
        computed: {
            attendees(){
              return [...this.students, ...this.tutors];
            },
            totalPrice() {
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

                total = total - _.sum(this.giftCards.map(gc => parseInt(gc.remaining_balance)));

                if(total > 0) {
                    total += parseInt(this.bookingFee);
                }

                return Math.max(total, 0);
            },
            selectionMade() {
                return this.courses.length > 0 || this.giftCardTypes.length > 0 || this.addons.length > 0;
            },
            isDesktop() {
                return this.windowWidth >= 992;
            }
        },
        methods: {
            countParticipants(course) {
                return this.attendees.length 
                    ? this.attendees.filter(attendee => attendee.courseId === course.id).length 
                    : this.participants;
            },
            subtotal(course) {
                let price = parseInt(course.price)
                return (this.attendees.length? this.countParticipants(course) : this.participants) * price;
            },
            toCheckout() {
                if (this.selectionMade) {
                    this.$emit('toCheckout');
                }
            },
            handleWindowResize(event) { this.windowWidth = event.currentTarget.innerWidth; },
        },
        beforeDestroy: function () {
            window.removeEventListener('resize', this. handleWindowResize)
        },
        mounted() {
            window.addEventListener('resize', this.handleWindowResize);
        }
    }
</script>

<style lang="scss" scoped>
.card-mobile {
    min-height: 198px;
    width: calc(100% - 1rem);
    height: 100%;
    padding: 1rem 1rem 1rem 2rem;
    margin-left: -1px;
    border-top-right-radius: 5px;
    border-bottom-right-radius: 5px;
    background-color: #fff;
    font-family: "Open Sans", serif;
    font-size: 14px;
    letter-spacing: 0;
    line-height: 19px;
    color: #0F0F0F;

    .card-mobile-title {
        color: #0F0F0F;
        font-family: Roboto, sans-serif;
        font-size: 16px;
        font-weight: bold;
        letter-spacing: 0;
        line-height: 19px;
        padding-top: 15px;
    }

    .cart-total {
        font-size: 16px;
        font-weight: 600;
        letter-spacing: 0;
        line-height: 22px;
        font-family: "Open Sans";
    }
}
</style>