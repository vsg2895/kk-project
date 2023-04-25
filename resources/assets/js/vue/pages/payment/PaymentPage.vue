<template>
    <div id="course-page" class="container">
        <template v-if="school && order.courses">
            <template v-if="user.role_id == 1">
                <div class="row-flex">
                    <div class="col-xs-12 col-md-8 order-2 order-md-1 checkout-left-space">
                        <div v-if="hasCourses" class="checkout-step courses">
                            <course-row
                                v-for="(course,i) in order.courses"
                                :key="i"
                                :course="course"
                                :order.sync="order"
                                :deletable="deletable"
                                @remove="removeCourse(i)"
                                @attendeeUpdated="updateAttendee" />
                            <button
                                v-if="courseIds.length > 1"
                                :class="{ 'disabled' : !coursesRemoved }"
                                :disabled="!coursesRemoved"
                                @click="resetCourses"
                                class="btn btn-sm btn-success">Återställ borttagna kurser</button>
                        </div>

                        <div v-if="hasCustomAddons" class="checkout-step courses">
                            <package-row
                              v-for="(addon,i) in order.custom_addons"
                              :key="i"
                              :addon="addon"
                              :order.sync="order"
                              :deletable="deletable"
                              @attendeeUpdated="updateAddonAttendee" />
                        </div>

                        <div v-if="hasAddons" class="checkout-step courses">
                            <package-row
                              v-for="(addon,i) in order.addons"
                              :key="i"
                              :addon="addon"
                              :order.sync="order"
                              @attendeeUpdated="updateAddonAttendee" />
                        </div>

                        <template v-for="school in uniqueSchools">
                            <div v-if="school && school.addons.length" :key="school.id" class="checkout-step row">
                                <div class="col-sm-6 col-xl-4 mb-1" v-for="addon in school.addons" :key="addon.id">
                                    <special-addon :addon="addon" :onAdd="addAddon" :onRemove="removeAddon"
                                        :quantity="addon | quantity(order)"></special-addon>
                                </div>
                            </div>
                        </template>

                        <div v-if="useGiftCards" id="checkout-step-gift-card" class="checkout-step" :class="{ 'disabled' : !checkoutValid || checkingGiftCard }">
                            <div class="p-1">
                                <div v-if="checkingGiftCard" class="loader-indicator"></div>
                                <h3>Har du ett presentkort eller en rabattkod som du vill använda?</h3>
                                <div v-if="user.amount > 0" class="gift-card">
                                    <icon name="money-bag"></icon> Saldo
                                    <span class="value">{{ user.amount }}</span>
                                </div>
                                <div class="gift-card" v-for="giftCard in giftCards" :key="giftCard.gift_card_type_id">
                                    <icon style="width: 24px" name="gift-red"></icon>
                                    <span class="value">{{ giftCard.gift_card_type_id === 100000 ? giftCard.remaining_balance + '%' : giftCard.remaining_balance }}</span>
                                    <input type="button" value="Använd ej" class="btn btn-danger btn-sm" @click="removeGiftCard(giftCard.id)">
                                </div>
                                <h4>Aktivera nytt presentkort</h4>
                                <p class="gift-card-message gift-card-message-hidden">*Erbjudande gäller endast på köp över 600:-</p>
                                <div class="d-flex justif-content-between align-items-center">
                                    <div class="form-group mb-0 mr-1">
                                        <span class="fa fa-percent form-control-icon"></span>
                                        <input class="form-control form-control-lg" type="text" name="gift-card-token"
                                                placeholder="Presentkorts kod" v-model="pendingToken">
                                    </div>
                                    <input class="btn btn-primary" :class="{ 'disabled' : !checkoutValid }" type="button" value="Använd" @click="checkGiftCard">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-4 order-1 order-md-2 checkout-step cart-container">
                        <checkout-cart
                                :giftCardTypes="giftCardTypes"
                                :user="user"
                                :courses="order.courses"
                                :addons="orderAddons"
                                :students="order.students"
                                :tutors="order.tutors"
                                :bookingFee="bookingFee"
                                :gift-cards="uniqueGiftCards"
                                :onCourseDelete="onDeleteCourseCallbackGenerator">
                            <template slot="inner-addons">
                                <div>
                                    <div v-if="hasCustomAddons">
                                        <div v-for="customAddon in order.custom_addons" :key="customAddon.id">
                                            <addon
                                                :addon="customAddon"
                                                :onAdd="onAddAddonCallbackGenerator"
                                                :onRemove="onRemoveAddonCallbackGenerator"
                                                :onDelete="onDeleteAddonCallbackGenerator"
                                                :quantity="customAddon.id | addonQuantity(order.custom_addons)"
                                                :school-name="school.name"
                                                :custom="true">
                                            </addon>
                                        </div>
                                    </div>
                                    <div v-if="hasAddons">
                                        <div v-for="addon in order.addons" :key="addon.id">
                                            <addon
                                                :addon="addon"
                                                :onAdd="onAddAddonCallbackGenerator"
                                                :onRemove="onRemoveAddonCallbackGenerator"
                                                :onDelete="onDeleteAddonCallbackGenerator"
                                                :quantity="addon.id | addonQuantity(order.addons)"
                                                :school-name="school.name">
                                            </addon>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </checkout-cart>
                    </div>
                </div>
                <klarna-checkout
                  ref="klarna-checkout"
                  :giftCardTypes="giftCardTypes"
                  :school-id="school.id"
                  :validation="$v"
                  :course-ids="currentCourseIds"
                  :addons="order.addons"
                  :bookingFee="bookingFee"
                  :tutors="order.tutors"
                  :students="order.students"
                  :order-id="klarnaOrderId"
                  :seats-available="availableSeats"
                  :custom-addons="order.custom_addons"
                  :has-enough-seats-override="hasEnoughSeats"
                  :gift-card-tokens="order.gift_card_tokens"
                  @validationUpdated="updateValidation"/>
            </template>
            <div class="page-pages" v-else>
                <div class="container text-xs-center">
                    <h3>Logga ut för att boka kurser</h3>
                    <a class="btn btn-primary logout-btn-coursepage" :href="routes.route('auth::logout')">Logga ut</a>
                </div>
            </div>
        </template>
        <p class="text-xs text-muted">
          Vid bokning godkänner du våra
          <a target="_blank" href="/villkor">användarvillkor</a> och {{ school.name }}
          <a target="_blank" href="/kopvillkor">köpvillkor</a>
        </p>
    </div>
</template>

<script>
import _ from 'lodash';
import Addon from 'vue-pages/courses/Addon';
import Api from 'vue-helpers/Api';
import Cart from 'vue-pages/courses/Cart';
import CartAddon from 'vue-pages/courses/CartAddon'
import CheckoutCart from 'vue-pages/courses/CheckoutCart';
import Checkout from 'vue-components/Checkout';
import CourseRow from './CourseRow';
import { email, minLength, numeric, required, requiredIf } from 'vuelidate/lib/validators';
import Icon from 'vue-components/Icon';
import Introduction from 'vue-pages/courses/Introduction';
import Map from 'vue-components/Map';
import { mapActions, mapState } from 'vuex';
import moment from 'moment';
import PackageRow from './PackageRow';
import Participant from "../courses/Participant";
import RiskOne from 'vue-pages/courses/RiskOne';
import routes from 'build/routes.js';
import { socialSecurityNumber } from 'components/CustomValidators.js';

const KORLEKTIONER_ID = 16;
const RISKTVAAN_MC_ID = 11;

export default {
  components: {
      'klarna-checkout': Checkout,
      'map-component': Map,
      'introduction-participants': Introduction,
      'risk-one-participants': RiskOne,
      'participant': Participant,
      'cart': Cart,
      Icon,
      'addon': CartAddon,
      CourseRow,
      PackageRow,
      'special-addon':Addon,
      'checkout-cart': CheckoutCart
  },
  validations: {
    user: {
      email: { required, email },
      given_name: { required },
      family_name: { required },
      phone_number: { required, numeric }
    },
    loginUser: {
      email: { required, email },
      password: { required }
    },
    order: {
      tutors: {
        $each: {
          given_name: { required, minLength: minLength(2) },
          family_name: { required, minLength: minLength(2) },
          social_security_number: { socialSecurityNumber },
          email: { required, email }
        }
      },
      students: {
        $each: {
          given_name: { required, minLength: minLength(2) },
          family_name: { required, minLength: minLength(2) },
          social_security_number: { socialSecurityNumber },
          transmission: {
            required: requiredIf(function(student) {
              const course = this.order.courses.find((course) => course.id === student.courseId);
              return !_.isEmpty(course) && course.segment.id === KORLEKTIONER_ID;
            }),
          },
          category: {
            required: requiredIf(function(student) {
              const course = this.order.courses.find((course) => course.id === student.courseId);
              return !_.isEmpty(course) && course.segment.id === RISKTVAAN_MC_ID;
            }),
          },
          email: { required, email }
        }
      }
    }
  },
  props: {
    klarnaOrderId: { default: null },
    bookingFee: { required: true },
    schoolId: { required: true },
    courseIds: { type: Array, default: () => ([]) },
    addonIds: { type: Array, default: () => ([]) },
    customIds: { type: Array, default: () => ([]) },
    giftCardTypes: { type: Array, default: () => ([]) }
  },
  filters: {
    addonQuantity(addonId, orderAddons) {
      let item = orderAddons.find(addon => addon.id === addonId)
      return item ? item.quantity : 0;
    },
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
  data() {
    return {
      checkingGiftCard: false,
      pendingToken: '',
      selectedPaymentMethod: 1,
      state: 'neutral',
      routes: routes,
      start_time: '',
      showMap: true,
      map: null,
      mapInited: false,
      markerData: [],
      school: {},
      coursesRemoved: false,
      schools: [],
      order: {
        addons: [],
        custom_addons: [],
        courses: [],
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
      checkoutValid: false,
      giftCardUsed: false,
      minAmountForCode: 600
    };
  },
  computed: {
    ...mapState('cart', ['students']),
    currentCourseIds() {
      let courseIds = [];
      if (this.order && this.order.courses.length) {
          courseIds = this.order.courses.map(c => c.id);
      }

      return courseIds;
    },
    availableSeats() {
      if (this.order && this.order.courses.length)
        return Math.min(...this.order.courses.map(course => {
            return course && course.available_seats || 0;
        }));
      else return 0;
    },
    orderHasIntroCourse() {
      return this.order.courses.filter(course => {
          return course.segment.name === 'INTRODUCTION_CAR';
      }).length > 0;
    },
    orderHasMopedAmCourse() {
      return this.order.courses.filter(course => {
        console.log(course.segment.name);
        return course.segment.name === 'MOPED_AM' || course.segment.name === 'MOPED_PACKAGE';
      }).length > 0;
    },
    mapReady() {
      return !!this.markerData && !!this.school.longitude && !!this.school.latitude;
    },
    schoolLink() {
      if (this.school && this.school.slug && this.school.city.slug) {
        return routes.route('shared::schools.show', { schoolSlug: this.school.slug, citySlug: this.school.city.slug })
      }
    },
    isUserFormActive() {
      return !!this.loginUser.password || !!this.loginUser.email
    },
    isNewUserFormActive() {
      return !!this.user.given_name || !!this.user.family_name || !!this.user.phone_number || !!this.user.email
    },
    orderValid() {
      return !this.$v.order.$invalid && (this.user.id || !this.$v.user.$invalid) && this.hasEnoughSeats;
    },
    klarnaAvailable() {
      return true;
      //Gift card orders always use kkj klarna so it always "has klarna"
      // return this.klarnaOrderId && ((this.school && this.school.has_klarna) || this.hasGiftCards)
    },
    mapCenter() {
      if (this.school.longitude && this.school.latitude) {
        return {
          lat: parseFloat(this.school.latitude),
          lng: parseFloat(this.school.longitude)
        };
      }
      return {
        lat: 0,
        lng: 0
      };
    },
    cartEnabled() {
      return this.hasCourses || this.hasAddons || this.hasCustomAddons || this.hasGiftCards;
    },
    hasCourses() {
      return this.order.courses && this.order.courses.length > 0;
    },
    hasGiftCards() {
      return this.giftCardTypes && this.giftCardTypes.length > 0;
    },
    deletable() {
      return (this.order.addons.length + this.order.courses.length + this.order.custom_addons) > 1;
    },
    hasAddons() {
      return this.order.addons && this.order.addons.length > 0;
    },
    hasCustomAddons() {
      return this.order.custom_addons && this.order.custom_addons.length > 0;
    },
    customAddons() {
      return this.school && this.school.custom_addons && this.school.custom_addons.length ?
            this.school.custom_addons.filter(addon => addon.active) : [];
    },
    orderAddons() {
      return [...this.order.addons, ...this.order.custom_addons];
    },
    uniqueGiftCards() {
      return _.uniq(this.giftCards);
    },
    hasEnoughSeats() {
      let participants = [...this.order.students, ...this.order.tutors];
      let hasEnoughSeatsArr = this.order.courses.map(c => {
        let courseParticipantsCount = participants.filter(p => p.courseId === c.id).length;
        return c.available_seats >= courseParticipantsCount;
      });
      return _.reduce(hasEnoughSeatsArr, (prev, next) => {
        return prev && next;
      });
    },
    useGiftCards() {
      return true;
      // return this.school && this.school.accepts_gift_card;
    },
    uniqueSchools() {
      var vm = this;
      var include = false;

      this.order.courses.forEach(function (course) {
        include = false;
        vm.schools.forEach(function (schoolItem) {
          if (schoolItem.id === course.school.id) {
            include = true;
          }
        });
        if (!include) {
          vm.schools.push(course.school);
        }
      });

      return vm.schools;
    },
    totalPrice() {
      let total = 0,
          saldo = 0;
      this.order.courses.forEach(course => {
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

      total = _.reduce(this.orderAddons, (sum, addon) => {
        return sum + addon.quantity * addon.price
      }, total)

      total = total - _.sum(this.giftCards.map(gc => gc.gift_card_type_id === 100000 ? total * (parseInt(gc.remaining_balance) / 100) : parseInt(gc.remaining_balance)));

      if(total > 0) {
        total += parseInt(this.bookingFee);
      }
      saldo = this.user ? parseFloat(this.user.amount) : 0;
      if (saldo > 0 && total > 0) {
        total -= saldo;
      }
      return Math.max(Math.round(total * 100) / 100, 0);
    },
    attendees(){
      return [...this.order.students, ...this.order.tutors];
    },
  },
  watch: {
    user: {
      handler() {
        if (this.useGiftCards && this.user.gift_cards) {
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
    ...mapActions('cart', {
        storeRemoveCourse: 'removeCourse',
        storeRemoveAddon: 'removeAddon',
    }),
    updateAttendee(attendee, index, attendeeType) {
      const courseAttendees = this.order[attendeeType].filter((it) => it.courseId === attendee.courseId);
      courseAttendees.splice(index, 1, attendee);
      this.$set(this.order, attendeeType, [...this.order[attendeeType].filter((it) => it.courseId !== attendee.courseId), ...courseAttendees]);
    },
    updateAddonAttendee(attendee, index, attendeeType) {
      const attendees = this.order[attendeeType].filter((it) => it.addonId === attendee.addonId);
      attendees.splice(index, 1, attendee);
      this.$set(this.order, attendeeType, [...this.order[attendeeType].filter((it) => it.addonId !== attendee.addonId), ...attendees]);
    },
    addAddon() {
      return addon => {
        let addons = this.order.addons;
        let existingIndex = _.findIndex(addons, (existingAddon) => {
          return addon.name === existingAddon.name;
        });

        existingIndex !== -1 ?
            addons[existingIndex].quantity++ :
            addons.push({
              ...addon,
              price: addon.pivot.price,
              quantity: 1
            });

        this.$set(this.order, 'addons', addons);

        this.order.students.push({
          given_name: '',
          family_name: '',
          social_security_number: '',
          email: '',
          transmission: '',
          category: '',
          addonId: addon.id,
        })
      }
    },
    removeAddon() {
      return (addon) => {
        this.storeRemoveAddon(addon);
        let addons = this.order.addons;
        let existingIndex = _.findIndex(addons, (existingAddon) => {
          return addon.name === existingAddon.name;
        });

        if (existingIndex !== -1 &&  addons[existingIndex].quantity > 1) {
          addons[existingIndex].quantity--;
          this.order.students.forEach((attendee, index) => {
            if(attendee.addonId === addon.id) {
              this.order.students.splice(index, 1);
            }
          });
        } else {
          addons.splice(existingIndex, 1);
          this.order.students = this.order.students.filter((attendee, index) => {
            return attendee.addonId !== addon.id;
          });
        }

        this.$set(this.order, 'addons', addons);
      }
    },
    removeGiftCard(id) {
      this.giftCards = this.giftCards.filter(gc => gc.id !== id);
      this.giftCardUsed = false;
    },
    updateValidation(valid) {
      this.checkoutValid = valid;
    },
    async checkGiftCard() {
      if (!this.checkoutValid) {
        return //'adding gift cards disabled'
      }
      if (this.order.gift_card_tokens.includes(this.pendingToken)) {
        return //'token already in order'
      }
      if (this.giftCardUsed) {
        return //don't let using > 1 gift cards in 1 order
      }
      if (Math.round(this.user.amount) > 0) {
        return; //don't let using gift cards, if balance is used
      }
      this.checkingGiftCard = true;
      let response = await Api.checkGiftCard(
          this.pendingToken,
          this.courseIds.length ? this.courseIds[0] : null,
          this.orderHasMopedAmCourse
      );
      if (response.exists && !response.claimed && parseInt(response.giftCard.remaining_balance)) {
        let giftCard = response.giftCard;
        giftCard.token = this.pendingToken;

        if (this.totalPrice >= this.minAmountForCode) {
          this.giftCards.push(giftCard)
          this.giftCardUsed = true;
          $('.gift-card-message').addClass('gift-card-message-hidden');
        } else {
          $('.gift-card-message').removeClass('gift-card-message-hidden');
        }
      }
      this.checkingGiftCard = false;
      this.pendingToken = '';
    },
    updateOrder() {
      this.$refs['klarna-checkout'].updateOrder();
    },
    handlePaymentMethodChanged(paymentMethod) {
      this.selectedPaymentMethod = paymentMethod;
    },
    courseStartDate(rawDate) {
      let date = moment(rawDate)
      if (date) {
        return date.format('dddd [den] D MMMM')
      } else {
        return ''
      }
    },
    removeCourse(index) {
      this.order.students = this.order.students.filter(student => {
        return student.courseId !== this.order.courses[index].id;
      });
      this.order.tutors = this.order.tutors.filter(tutor => {
        return tutor.courseId !== this.order.courses[index].id;
      });
      this.order.courses.splice(index, 1);
      this.coursesRemoved = true;
    },
    onAddAddonCallbackGenerator(custom) {
      return (addon) => {
        let addons = custom ? this.order.custom_addons : this.order.addons;
        let existingIndex = _.findIndex(addons, (existingAddon) => {
            return addon.name === existingAddon.name;
        });

        if (existingIndex !== -1) {
          addons[existingIndex].quantity++;
        } else {
          let price = custom ? addon.price : addon.pivot.price;
          addons.push({ ...addon, price, quantity: 1 });
        }
        let target = custom ? 'custom_addons' : 'addons';
        this.$set(this.order, target, addons);
      }
    },
    onRemoveAddonCallbackGenerator(custom) {
      return (addon) => {
        let addons = custom ? this.order.custom_addons : this.order.addons;
        let existingIndex = _.findIndex(addons, (existingAddon) => {
          return addon.name === existingAddon.name;
        });

        if (existingIndex !== -1) {
          let foundAddon = addons[existingIndex];
          if (foundAddon.quantity > 1) {
            addons[existingIndex].quantity--;
          }
        }
        let target = custom ? 'custom_addons' : 'addons';
        this.$set(this.order, target, addons);
        if (this.totalPrice < this.minAmountForCode) {
          this.giftCards.map(giftCard => {
            this.removeGiftCard(giftCard.id);
          })
        }
      }
    },
    onDeleteAddonCallbackGenerator(custom) {
      return (addon) => {
        let addons = custom ? this.order.custom_addons : this.order.addons;
        let existingIndex = _.findIndex(addons, (existingAddon) => {
          return addon.name === existingAddon.name;
        });

        if (existingIndex !== -1) {
          addons[existingIndex].quantity = 1;
          addons.splice(existingIndex, 1);
        }
        let target = custom ? 'custom_addons' : 'addons';
        this.$set(this.order, target, addons);

        this.order.students = this.order.students.filter((attendee) => {
          return attendee.addonId !== addon.id;
        });

        this.storeRemoveAddon(addon);
        const params = new URLSearchParams(window.location.search);
        if (addons.length < 1) {
          params.delete('paket');
        } else {
          params.set('paket', addons.map(a => a.id).join());
        }

        if (this.totalPrice < this.minAmountForCode) {
          this.giftCards.map(giftCard => {
            this.removeGiftCard(giftCard.id);
          })
        }

        window.history.replaceState({}, '', decodeURIComponent(`${window.location.pathname}?${params}`));
      }
    },
    onDeleteCourseCallbackGenerator() {
      return (course) => {
        let courses = this.order.courses;
        let students = [...this.order.students];
        let tutors = [...this.order.tutors];
        let existingIndex = _.findIndex(courses, (existingCourse) => {
          return course.id === existingCourse.id;
        });
        if (existingIndex !== -1) {
          if (students.length) {
            students = students.filter((student) => {
              return student.courseId !== course.id
            });
          }
          if (tutors.length) {
            tutors = tutors.filter((tutor) => {
              return tutor.courseId !== course.id
            });
          }
          courses.splice(existingIndex, 1);
        }
        this.$set(this.order, 'students', students);
        this.$set(this.order, 'tutors', tutors);
        this.$set(this.order, 'courses', courses);
        this.storeRemoveCourse(course);

        const params = new URLSearchParams(window.location.search);
        if (courses.length < 1) {
          params.delete('kurser');
        } else {
          params.set('kurser', courses.map(c => c.id).join());
        }
        params.delete(course.id);
        if (this.totalPrice < this.minAmountForCode) {
          this.giftCards.map(giftCard => {
            this.removeGiftCard(giftCard.id);
          })
        }
        window.history.replaceState({}, '', decodeURIComponent(`${window.location.pathname}?${params}`));
      }
    },
    async book() {
      if (this.state !== 'processing') {
        this.state = 'processing';
        if (this.orderValid) {
          if (this.user) {
            this.order.new_user = this.user
          }
          let order = await Api.storeOrder(this.order);
          if (order) {
            var transactionProducts = [];
            order.items.forEach((item) => {
                transactionProducts.push({
                    'sku': String(item.id),
                    'name': String(item.name),
                    'price': parseInt(item.amount), // DOES THIS FIX GA PRICE?
                    'quantity': item.quantity
                });
            });
            dataLayer.push({
                'transactionId': String(order.id),
                'transactionTotal': order.order_value,
                'transactionProducts': transactionProducts
            });

            window.location = routes.route('shared::payment.confirmed', {
                schoolSlug: this.school.slug,
            });
          } else {
            this.state = 'neutral';
          }
        }
        this.state = 'neutral';
      }
    },
    async logIn() {
      let response = await Api.logIn(this.loginUser);
      if (response) {
          this.user = response;
          this.authenticated = true;
      } else {
        this.loginUser.password = "";
      }
    },
    async getData() {
      let [courses, user, school] = await Promise.all([this.getCoursesData(), this.getUserData(), Api.findSchool(this.schoolId)]);
      this.school = school;
      this.$set(this.order, 'courses', courses);

      //TODO: Zlatan: Need to verify school handling and change it. There is no school for gift cards
      if (courses && courses[0]) {
        this.markerData = [{
          id: courses[0].id,
          latitude: courses[0].latitude,
          longitude: courses[0].longitude,
        }]
      }

      this.$set(this.order, 'payment_method', 'KLARNA');
      if (user && user.role_id) {
        this.$set(this, 'user', _.assign(this.user, user));
      }
    },
    getCoursesData() {
      return Promise.all(this.courseIds.map(id => Api.findCourse(id)));
    },
    getUserData() {
      return Api.getLoggedInUser();
    },
    async resetCourses() {
      let courses = await this.getCoursesData();
      this.$set(this.order, 'courses', courses);
      this.coursesRemoved = false;
    },
    initCartAddons() {
      let id;
      for (id of this.addonIds) {
        let addon = this.school.addons.find(addon => addon.id === id);
        if (addon) {
          this.order.addons.push({ ...addon, price: addon.pivot && addon.pivot.price || undefined, quantity: 1 });
        }
      }
      for (id of this.customIds) {
        let custom = this.school.custom_addons.find(custom => custom.id === id);
        if (custom) {
          this.order.custom_addons.push({ ...custom, price: custom.price, quantity: 1 });
        }
      }
    },
    initAttendeeInfo() {
      this.order.courses.forEach(course => {
        let urlParams = new URLSearchParams(window.location.search);
        let qty = parseInt(urlParams.get(course.id));

        if (qty < 1 || isNaN(qty)) {
          qty = 1;
        }

        for (let i = 0; i < qty; i++) {
          this.order.students.push({
            given_name: this.user ? this.user.given_name : '',
            family_name: this.user ? this.user.family_name : '',
            social_security_number: '',
            email: this.user ? this.user.email : '',
            courseId: course.id,
            transmission: '',
            category: '',
          });
        }
      });

      this.order.addons.forEach(addon => {
        this.order.students.push({
          given_name: this.user ? this.user.given_name : '',
          family_name: this.user ? this.user.family_name : '',
          social_security_number: '',
          email: this.user ? this.user.email : '',
          transmission: '',
          category: '',
          addonId: addon.id,
        });
      });

      this.order.custom_addons.forEach(custom_addons => {
        this.order.students.push({
          given_name: this.user ? this.user.given_name : '',
          family_name: this.user ? this.user.family_name : '',
          social_security_number: '',
          email: this.user ? this.user.email : '',
          transmission: '',
          addonId: custom_addons.id,
        });
      });
    },
    subtotal(course) {
      let price = parseInt(course.price)
      return (this.attendees.length ? this.countParticipants(course) : this.participants) * price;
    },
    countParticipants(course) {
      return this.attendees.length ?
          this.attendees.filter(attendee => {
            return attendee.courseId === course.id;
          }).length : this.participants;
    }
  },
  updated() {
    if (!this.mapInited && this.order.courses.length && this.user.role_id == 1 && window.initMap) {
      window.initMap();
      this.mapInited = true;
    }
  },
  async created() {
    await this.getData();
    this.initCartAddons();
    // Commented, due to https://korkortsjakten.atlassian.net/browse/KKJ-126
    this.initAttendeeInfo();
  },
}
</script>
