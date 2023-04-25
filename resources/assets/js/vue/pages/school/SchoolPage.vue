<template>
  <div v-if="school" class="school-page d-flex flex-column">
    <div v-if="this.iframe" class="row">
      <div class="container">
        <div class="col-12 d-flex flex-wrap iframe-menu-gap">
          <div class="col-lg-2 col-md-3 col-xs-3 iframe-menu-button text-center" v-for="currentSegmentGroup in showingTypes">
            <strong class="w-100" v-if="currentSegmentGroup.type === 'B'" @click="courseShowHandler(currentSegmentGroup.type)">
              Kurser - Bil
            </strong>
            <strong class="w-100" v-else-if="currentSegmentGroup.type === 'A'" @click="courseShowHandler(currentSegmentGroup.type)">
              Kurser - MC
            </strong>
            <strong v-else-if="currentSegmentGroup.type === 'AM'" @click="courseShowHandler(currentSegmentGroup.type)">
              Moped
            </strong>
            <strong class="w-100" v-else-if="currentSegmentGroup.type === 'YKB'" @click="courseShowHandler(currentSegmentGroup.type)">
              YKB
            </strong>
          </div>
          <div class="col-lg-2 col-md-3 col-xs-3 iframe-menu-button text-center paket-button" @click="paketShowHandler">
              Paket
          </div>
        </div>
        <hr>
        <div class="col-12 d-flex flex-wrap iframe-menu-gap mt-1 segment">
          <div class="col-lg-2 col-md-3 col-xs-3 iframe-menu-button text-center"
               v-for="currentSegment in showingSegments"
               @click="segmentCourseShowHandler(currentSegment)">
            {{ currentSegment.shortLabel === undefined ? currentSegment.label : currentSegment.shortLabel }}
          </div>
        </div>
      </div>
    </div>

    <div v-if="!iframe" class="container">
      <div class="col-md-12 px-0 py-5 school-header">
        <div class="basic-info-title">
          <span>{{school.name}}</span>
          <div class="top-partner-child">
            <div v-title="'Den här trafikskolan är med i vårt program för rekommenderade samarbetspartners. Trafikskolan gör allt för att ge sina gäster en bra upplevelse tack vare dess utmärkta service och bra priser. Skolan rekommenderas därför starkt av både elever och oss på Körkortsjakten.'">
              <i v-if="school.top_partner"  class="fa fa-medal top-partner-medal"></i>
            </div>
            <p v-if="school.top_partner"> Top Partner</p>
          </div>
          <icon class="icon base-line" name="gift"></icon>
        </div>
        <div class="mer-information font-weight-bold">
          <a v-scroll-to="{ el: '.school-description', offset: -40 }">+ Mer information</a>
          <a v-scroll-to="{ el: '.school-map', offset: -40 }">+ Hitta hit</a>
        </div>
        <div v-scroll-to="{ el: '.ratings-card', offset: -40 }" class="header-ratings">
          <stars :rating="avgRating"></stars>
          <span v-if="avgRating" class="d-flex align-items-center">
            <span class="search-avg">{{ avgRating }}/5</span>
            <span class="search-qty">i betyg baserat på {{ school.ratings.length}} recensioner</span>
          </span>
        </div>

      </div>
    </div>

    <school-service-list v-if="segments.length > 0"
        class="move-for-cart"
        :current-tab="currentTab"
        :tab-list="tabList"
        :selectedAddons="selectedAddons"
        :school="school"
        :vehicles="vehicles"
        :school-segment-sorting="schoolSegmentSorting"
        :vehicle-segments="segments"
        :courses="courses"
        :selectedCourses="selectedCourses"
        :students="students"
        :iframe="this.iframe"
        :courseShow="this.courseShow"
        :paketShow="this.paketShow"
        ref="schoolService"
        @switchTab="(tab) => { this.currentTab = tab }"
        @addAttendee="addAttendee"
        @removeAttendee="removeAttendee"
        @toggleAddon="toggleAddon"
        @toggleCourse="toggleCourse"
        @addCourse="addCourse"
        @segmentBlocks="segmentBlocksHandler">
    </school-service-list>
    <div class="container hidden-sm-down">
      <cart
          :bookingFee="bookingFee"
          :school-page="true"
          :students="students"
          @toCheckout="toCheckout"
          :courses="selectedCourses"
          :addons="selectedAddons"
          :disabled="!checkoutReady"
          :mountCollapsed="true"/>
    </div>
    <div class="hidden-sm-up">
      <cart-mobile
          :bookingFee="bookingFee"
          :school-page="true"
          :students="students"
          @toCheckout="toCheckout"
          :courses="selectedCourses"
          :addons="selectedAddons"
          :disabled="!checkoutReady"
          :mountCollapsed="true"/>
    </div>
    <div v-if="school.city" class="d-flex justify-content-center no-matching-times move-for-cart mt-3" :class="{'mb-2' : this.iframe}">
      <div v-if="tabList.length > 0 || this.courses.length > 0">
        <button class="btn btn-success" :class="{ 'disabled' : !checkoutReady }" @click="toCheckout">Till betalning</button>
      </div>
      <div class="d-flex flex-column justify-content-center align-items-center" v-else>
        <h3>Den här skolan har för tillfället inga kurser hos Körkortsjakten. Tryck här för att hitta skolor i din stad med tillgängliga kurser.</h3>
        <button @click="redirectCitySchoolsPage" class="btn btn-success">BOKA HÄR</button>
      </div>

    </div>
    <school-info v-if="!iframe"
        class="move-for-cart"
        :school="school"
        :user-rating="userRating"
        :user="user"
        :average-rating="avgRating"
        @rateSchool="rateSchool"
        @deleteRate="deleteRate"
        @claimSchool="claimSchool"
        :no-courses="!courses.length">
    </school-info>
    <cart-toast-notification @toCheckout="toCheckout" />
  </div>
</template>

<script>
import $ from 'jquery'
import { cloneDeep, sortBy } from 'lodash';
import moment from 'moment';
import routes from 'build/routes';
import Api from 'vue-helpers/Api';
import Icon from 'vue-components/Icon';
import Cart from 'vue-pages/courses/Cart'
import CartMobile from 'vue-pages/courses/CartMobile'
import CartToastNotification from 'vue-components/CartToastNotification.vue';
import SchoolServiceList from './SchoolServiceList';
import SchoolInfo from './SchoolInfo';
import Stars from 'vue-components/Stars';
import Slick from "vue-slick";
import { mapActions, mapGetters, mapState } from 'vuex';

const TOAST_OPTIONS = {
  width: 340,
  title: 'Tillagd i din varukorg',
  duration: 5000,
};

const getToastMessage = (course, count) => `
  <div class="d-flex justify-content-between">
    <span>${course.name}</span>
    <span class="price">${count} x ${course.price} kr</span>
  </div>`;

export default {
  props: ['id', 'user', 'bookingFee', 'iframe'],
  components: {
    Cart,
    CartMobile,
    CartToastNotification,
    Icon,
    SchoolServiceList,
    SchoolInfo,
    Stars,
    Slick,
  },
  data() {
    return {
      routes,
      loading: true,
      currentTab: 1,
      vehicles: [],
      vehiclesSort: [],
      segments: [],
      schoolSegmentSorting: [],
      school: {},
      userRating: 0,
      markerData: {},
      courseFilter: 0,
      christmasCampaign: true,
      showingTypes: {},
      showingSegments: [],
      courseShow: true,
      paketShow: true,
      vehicle_segements_short_names : [
        { name: 'Körkortsteori och Testprov', short_name: 'Teori' },
        { name: 'Introduction Course English', short_name: 'Intro Course En' },
        { name: 'YKB Fortutbildning 35 h', short_name: 'YKB Fortut 35h' },
        { name: 'YKB Grundkurs 140 h', short_name: 'YKB Grund 140h' },
        { name: 'YKB Delkurs 1 Sparsam Körning', short_name: 'YKB Del 1 S-K' },
        { name: 'YKB Delkurs 2 Godstransporter', short_name: 'YKB Del 2 Gods' },
        { name: 'YKB Delkurs 3 Lagar och Regler', short_name: 'YKB Del 3 L och R' },
        { name: 'YKB Delkurs 4 Ergonomi och Hälsa', short_name: 'YKB Del 4 E och H' },
        { name: 'YKB Delkurs 5  Trafiksäkerhet och Kundfokus', short_name: 'YKB Del 5 T och K' },
        { name: 'Risk 1&2 combo English', short_name: 'Risk 1&2 combo E' },
      ],
    }
  },
  computed: {
    ...mapGetters('cart', ['qty', 'courseIds', 'addonIds', 'customIds']),
    ...mapState('cart', {
      selectedAddons: 'addons',
      selectedCourses: 'courses',
      students: 'students',
    }),
    avgRating() {
      const { ratings } = this.school;
      if (!ratings) {
        return 0;
      }
      const sum = ratings.map(({ rating }) => rating).reduce((total, currentValue) => total += currentValue, 0)
      const average = parseFloat((sum / ratings.length).toFixed(1));

      return Number.isInteger(average) ? parseInt(average) : average;
    },
    customAddons() {
      return this.school && this.school.custom_addons ?
          this.school.custom_addons
              .filter(custom => custom.active)
              .map(custom => {
                return {
                  id: custom.id,
                  type: 'custom_addon',
                  name: custom.name,
                  price: custom.price,
                  description: custom.description,
                  quantity: 1,
                  top_deal: custom.top_deal,
                  show_left_seats: custom.show_left_seats,
                  sort_order: custom.sort_order,
                  left_seats: custom.left_seats
                }
              }) : [];
    },
    addons() {
      return this.school && this.school.addons ?
          this.school.addons.map(addon => {
            return {
              id: addon.id,
              type: 'addon',
              name: addon.name,
              price: parseInt(addon.pivot.price),
              description: addon.pivot.description,
              quantity: 1,
              top_deal: addon.pivot.top_deal,
              show_left_seats: addon.pivot.show_left_seats,
              sort_order: addon.pivot.sort_order,
              left_seats: addon.pivot.left_seats
            }
          }) : [];
    },
    prices() {
      return this.school && this.school.formatted_prices && this.segments ?
          this.segments.filter(segment => {
            return segment.bookable;
          }).map(segment => {
            let priceObj = this.school.formatted_prices[segment.name];
            return {
              type: 'price',
              name: segment.label,
              price: priceObj && priceObj.price,
              description: segment.description,
              vehicleId: segment.vehicle_id,
              id: segment.id
            }
          }).filter(priceObj => {
            return priceObj.price;
          }) : [];
    },
    tabList() {
      let concat = this.customAddons.concat(this.addons);

      if (!concat.length) {
        return [];
      }

      return sortBy(this.customAddons.concat(this.addons), function(item) {
        return item.sort_order;
      });
    },
    courses() {
      return this.school && this.school.courses && this.school.courses.length ?
          this.school.courses.filter(course => {
            let hasAvailableSeats = course.available_seats > 0;
            let inFuture = moment(course.start_time).isAfter(moment());
            return hasAvailableSeats && inFuture;
          }).sort((a, b) => {
            return moment(a.start_time).format('YYYYMMDD') - moment(b.start_time).format('YYYYMMDD');
          }).map(course => {
            let vehicle = this.vehicles.filter(vehicle => {
              if (vehicle.id == course.segment.vehicle_id) {
                return vehicle;
              }
            });
            return {
              id: course.id,
              name: course.name,
              label: course.segment.label,
              date: moment(course.start_time).format('dddd Do MMMM'),
              time: `${course.start_hour} - ${course.end_hour}`,
              price: parseInt(course.price),
              availableSeats: course.available_seats,
              description: course.description,
              segment: course.vehicle_segment_id,
              vehicle,
              course
            }
          }) : [];

    },
    checkoutReady() {
      return this.school.id && (this.selectedCourses.length || this.selectedAddons.length);
    },
    isDesktop() {
      return this.windowWidth >= 992;
    }
  },
  watch: {
    selectedCourses() {
      this.warnBeforeNavigationCheck();
    },
    selectedAddons() {
      this.warnBeforeNavigationCheck();
      },
  },
  created() {
    this.getData();
    this.warnBeforeNavigationCheck();
    //Only mount collapsed if we are forcing it or if width is to small
    this.collapsed = !this.mountCollapsed ?
        !this.isDesktop :
        true;
  },
  methods: {
    ...mapActions('cart', ['clear', 'upsertCourse', 'removeCourse', 'addAddon', 'removeAddon']),
    warnBeforeNavigationCheck() {
      window.onbeforeunload = this.selectedAddons.length || this.selectedCourses.length ?
          () => {
            return 'OBS: Kundkorgen kommer inte sparas om du navigerar ifrån denna sida!'
          } : null;
    },
    redirectCitySchoolsPage(){
     let route = routes.route('shared::search.schools', { citySlug: this.school.city.slug })
      location.href = route;
    },
    async getData() {
      let [vehicles, segments, school, userRating, schoolSegmentSorting] = await Promise.all([Api.getVehicleTypesForSchool(this.id), Api.getVehicleSegments(), Api.findSchool(this.id), Api.getUserRatingForSchool(this.id), Api.getVehicleSegmentsOrder(this.id)]);
      this.vehicles = vehicles;
      this.segments = segments;
      this.school = school;
      this.schoolSegmentSorting = schoolSegmentSorting;

      if (userRating && userRating.rating) {
        this.userRating = userRating;
      }
      this.markerData = [this.school];
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
    async rateSchool(rating) {
      let userRating = await Api.rateSchool(this.school, rating);
      if (userRating && userRating.rating) {
        this.$set(this, 'userRating', userRating);
      }
      let school = await Api.findSchool(this.school.id);
      this.$set(this, 'school', school);
    },
    async deleteRate() {
      let success = await Api.deleteRateForSchool(this.school);
      let school = await Api.findSchool(this.school.id);
      if (success) this.$set(this, 'userRating', null);
      this.$set(this, 'school', school);
    },
    async claimSchool() {
      if (this.user) {
        let response = await Api.claimSchool(this.school);
        this.claimSent = true;
      } else {
        window.location = routes.route('auth::register.organization', {'school_id': this.school.id});
      }
    },
    checkLocalStorage() {
      if ((this.$localStorage.school !== 'undefined') && (parseInt(this.$localStorage.school) !== this.school.id) && this.qty > 0) {
        const message = 'Vill du ta bort dina tidigare valda produkter?\nDu har redan valt något från en annan trafikskola. Om du fortsätter kommer din varukorg att tömmas.';
        if(confirm(message)) this.clear();
        else throw Error('Confirmation dialog declined');
      }
      this.$localStorage.set('school', this.school.id);
    },
    // TODO: Refactor addCourse and toggleCourse into single function
    addCourse(course) {
      try {
        this.checkLocalStorage();
      } catch (err) {
        return;
      }

      // TODO: check why this event fail the page
      // AnalyticsService.eventCourse('added', course);

      const { availableSeats, selectedSeats } = course;
      const takenSeats = this.students.filter((s) => s.courseId === course.id).length;

      if (availableSeats < (takenSeats + selectedSeats)) {
        return;
      }

      const clonedCourse = cloneDeep(course);
      const upsertedCourse = this.isCourseInCart(course) ? { ...clonedCourse, availableSeats: availableSeats - selectedSeats } : clonedCourse;

      this.upsertCourse(upsertedCourse);
      this.$notify({ ...TOAST_OPTIONS, text: getToastMessage(course, selectedSeats) });
    },
    toggleCourse(course) {
      try {
        this.checkLocalStorage();
      } catch (err) {
        return;
      }

      if (this.isCourseInCart(course)) {
        this.removeCourse(course);
        // AnalyticsService.eventCourse('removed', course);
        return;
      }
      // TODO: check why this event fail the page
      // AnalyticsService.eventCourse('added', course);
      
      this.upsertCourse(course);
      this.$notify({ ...TOAST_OPTIONS, text: getToastMessage(course, 1) });
    },
    isCourseInCart(course) {
      return this.selectedCourses.some(it => it.id === course.id)
    },
    toggleAddon(addon) {
      try {
        this.checkLocalStorage();
      } catch (err) {
        return;
      }
      addon.school = this.school.id;

      if (this.addonInCart(addon)) {
        this.removeAddon(addon);
        // TODO: Check why this event fails on production
        // AnalyticsService.eventAddon('removed', addon);

        return;
      }

      this.addAddon(addon);
      // TODO: Check why this event fails on production
      // AnalyticsService.eventAddon('added', addon);
      this.$notify({ ...TOAST_OPTIONS, text: getToastMessage(addon, 1) });
    },
    addonInCart(addon) {
      return this.selectedAddons.some(it => it.id === addon.id);
    },
    toCheckout() {
      if (this.checkoutReady) {
        window.onbeforeunload = null;
        Api.toCheckout(this.school.id, this.courseIds, this.addonIds, this.customIds, this.students, this.iframe);
      }
    },
    onFilterSelect(item) {
      this.courseFilter = item.id;
    },
    addAttendee(course) {
      if (course.availableSeats <= this.students.filter((obj) => obj.courseId === course.id).length) {
        return;
      }

      this.students.push({
        given_name: '',
        family_name: '',
        social_security_number: '',
        email: '',
        transmission: '',
        courseId: course.id,
      });
    },
    removeAttendee(course) {
      if (!this.students || !this.students.filter((obj) => obj.courseId === course.id).length) {
        return;
      }

      var itemId = 0;
      $.each(this.students, function(i, student) {
        if (student.courseId === course.id) {
          itemId = i;
        }
      });

      this.students.splice(itemId, 1);

    },
    getFormattedData(date) {
      return moment(date).format('DD MMM, YYYY');
    },
    hasRating() {
      return this.school && this.school.ratings && this.school.ratings.length > 0;
    },

    paketShowHandler(){
      this.courseShow = false;
      this.paketShow = true;
      // Close any opened calendar view
      this.$refs.schoolService.calendarSegmentId = null;
    },

    courseShowHandler(value){
      this.courseShow = true;
      this.paketShow = false;
      this.$refs.schoolService.segmentBlocks.filter(segmentBlock => {
        segmentBlock.hasLenght = false;
        segmentBlock.hasLenght = segmentBlock.type === value;
        segmentBlock.segments.filter(segment => {
          segment.hide = false;
        })
        return segmentBlock;
      })
      // Close any opened calendar view
      this.$refs.schoolService.calendarSegmentId = null;
    },

    segmentCourseShowHandler(currentSegment){
      this.courseShow = true;
      this.paketShow = false;
      this.$refs.schoolService.segmentBlocks.filter(segmentBlock => {
        segmentBlock.hasLenght = false;
        segmentBlock.hasLenght = segmentBlock.type === currentSegment.type;
        segmentBlock.segments.filter(segment => {
          segment.hide = segment.id !== currentSegment.id;
        })
        return segmentBlock;
      });
      // Open corresponding segment calendar view
      this.$refs.schoolService.openCalendar(currentSegment)
    },

    segmentBlocksHandler(value) {
      this.showingTypes = value;
      this.showingTypes.filter(segmentType => {
        segmentType.segments.filter(segment => {
          segment.type = segmentType.type
          for (let i = 0; i < this.vehicle_segements_short_names.length; i++) {
            if (this.vehicle_segements_short_names[i].name === segment.label) {
              segment.shortLabel = this.vehicle_segements_short_names[i].short_name
            }
          }
          this.showingSegments.push(segment)
        })
      });
    },
  }
}
</script>
