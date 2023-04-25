<template>
    <div>
        <p class="text-danger text-center" v-if="!isGiftCardOrder && !hasEnoughSeats">
            Du försöker boka fler platser än vad som finns tillgängligt. Just nu finns det {{ seatsAvailable }} platser.
        </p>
    </div>
</template>

<script type="text/babel">
import { mapActions } from 'vuex';
        import Klarna from 'vue-helpers/Klarna';
        export default {
            props: {
                validation: {
                    required: true
                },
                orderId: {
                    required: true
                },
                schoolId: {
                    required: false
                },
                courseIds: {
                    required: false
                },
                giftCardType: {
                    required: false
                },
                addons: {
                    type: Array,
                    default: () => []
                },
                bookingFee: {
                    default: 39
                },
                customAddons: {
                    type: Array,
                    default: () => []
                },
                tutors: {
                    type: Array,
                    default: () => []
                },
                students: {
                    type: Array,
                    default: () => []
                },
                seatsAvailable: {
                    default: 0
                },
                giftCardTokens: {
                    type: Array,
                    default: () => []
                },
                hasEnoughSeatsOverride: {
                    type: Boolean,
                    default: false
                }
            },
            data() {
                return {
                    valid: false
                };
            },
            watch: {
                validation: {
                    handler() {
                        this.validate();
                    },
                    deep: true
                },
                courseIds: {
                    handler() {
                        this.updateOrder()
                    },
                    deep: true
                },
                addons: {
                    handler() {
                        this.updateOrder()
                    },
                    deep: true
                },
                customAddons: {
                    handler() {
                        this.updateOrder();
                    },
                    deep: true
                },
                tutors: {
                    handler() {
                        this.updateOrder()
                    },
                    deep: true
                },
                students: {
                    handler() {
                        this.updateOrder()
                    },
                    deep: true
                },
                giftCardType: {
                    handler() {
                        this.updateOrder()
                    },
                    deep: true
                },
                giftCardTokens: {
                    handler() {
                        this.updateOrder()
                    },
                    deep: true
                }
            },
            computed: {
                hasEnoughSeats () {
                    return (this.courseIds.length ? (this.students.length + this.tutors.length) <= this.seatsAvailable : true) || this.hasEnoughSeatsOverride;
                },
                isGiftCardOrder () {
                    //abstract equality operator is intended
                    // return false;
                    return this.schoolId == null;
                }
            },
            methods: {
                ...mapActions('cart', {
                    clearCart: 'clear',
                }),
                async updateOrder() {
                    // Suspending the checkout
                    if (this.valid && ((this.students.length || this.tutors.length) || this.isGiftCardOrder)) {
                        const self = this;
                        window._klarnaCheckout(function (api) {
                            api.suspend();
                            api.on({
                                'redirect_initiated': function() {
                                    self.clearCart();
                                }
                            });
                        });

                        let requestData = {
                            schoolId: this.schoolId,
                            courseIds: this.courseIds,
                            students: this.students,
                            tutors: this.tutors,
                            addons: this.addons,
                            customAddons: this.customAddons,
                            giftCardType: this.giftCardType,
                            giftCardTokens: this.giftCardTokens
                        };
                        let response = await Klarna.updateOrder(this.orderId, requestData);

                        if (response.hasOwnProperty('error') || !response.success) {
                            window._klarnaCheckout(function (api) {
                                api.suspend();
                            });
                            $('.checkout-container, .checkout-left-space .row').html('<div class="checkout-step couclassName><div class=" text-center">' +
                                    '<div classNames=" card course-card classNameblock text-left"><!----> ' +
                                        '<div class="type tag-pillclassNamedefault" style="text-align: center; color: red;">' +
                                            'Unexpected error occurred. Please try to reload page. If it is does not helped please contact us.' +
                                        '</div>' +
                                    '</div>' +
                                '</div>');
                        } else {
                            // Resuming the checkout
                            window._klarnaCheckout(function (api) {
                                api.resume();
                            });
                        }
                    }
                },
                validate() {
                    let enableCheckout;
                    if (this.isGiftCardOrder) {
                        enableCheckout = !!this.giftCardType;
                    } else {
                        enableCheckout = !this.validation.order.$invalid && this.hasEnoughSeats;
                    }

                    if (enableCheckout) {
                        $('.checkout-container').removeClass('disabled');
                    } else {
                        $('.checkout-container').addClass('disabled');
                    }

                    this.valid = enableCheckout;
                    this.$emit('validationUpdated', this.valid);
                },
            },
            mounted () {
                var vm = this;
                vm.updateOrder();
                vm.validate();
                $('.checkout-container').appendTo('.checkout-left-space');

                this.$emit('courseUpdateOrder');
            },
        }
    </script>
