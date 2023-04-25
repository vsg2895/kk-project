<template>
  <div>
    <div v-if="!groupedCoursesCount(groupedCourses) && !noResult" class="search-result-header">
      <div class="search-results-none">
        <h4>Vänligen vänta, resultat laddas.</h4>
        <p>
          <icon name="smiley5"/>
        </p>
      </div>
    </div>
    <div class="search-result-header" v-else-if="!groupedCoursesCount(groupedCourses) && noResult">
      <div class="search-results-none">
        <h4>Hittade inga trafikskolor utifrån din filtrering.
          <icon name="smiley2"/>
        </h4>
        <p v-if="filter.vehicle_segments === 16" style="font-size: 1rem;" class="d-flex flex-column justify-content-between">
          <span>Dessvärre finns det inga enskilda körlektioner tillgängliga just nu, men det finns Körkortspaket med körlektioner som ni kan</span>
          <a href="/paketerbjudande" style="font-weight: 600; background-color: #23A78E; color: #FFFFFF; padding: 5px; border-radius: 6px;">BOKA HÄR</a>
        </p>
        <p v-else-if="filter.vehicle_segments === 7" >Tyvärr finns det inga fysiska Introduktionskurser bland våra trafikskolor i staden du sökt, men vi rekommenderar dig att gå samma kurs fast digitalt, klicka
          <a href="/kurser/digitalintroduktionskurs">här</a>
        </p>
        <p v-else >Tyvärr finns det inga tillgängliga kurser i din stad för tillfället, men kanske någon av dina närliggande städer har något att erbjuda.</p>
      </div>
    </div>
    <div :class="[groupedCourses.length ? 'search-result-list' : '']">
      <div class="search-result-group" v-for="(group, date, index) in groupedCourses" :key="index">
        <h2 class="search-result-date" v-if="filter.vehicle_segments !== ONLINE_LICENSE_THEORY">
          {{ date | formatDate('dddd DD MMMM') }}
        </h2>
        <div
          v-for="course in group"
          :key="course.id"
          @mouseover="mouseOverForSchool(true, course.school)"
          @mouseout="mouseOverForSchool(false, course.school)"
          class="search-result-object"
          :class="[`school-${course.school_id}`, course.school.top_partner ? 'top-partner-object' : '']">
          <div v-if="isYKB" class="school-logo-container">
            <img
              v-if="course.school.logo.path"
              :src="course.school.logo.path"
              class="school-logo"
              alt="Logo">
              <icon class="hidden-md-up mb-1" name="gift-red"></icon>
          </div>
          <div v-else class="school-item pl-0">
            <div v-if="filter.vehicle_segments !== ONLINE_LICENSE_THEORY" class="text-accent">{{ course.start_hour }} - {{ course.end_hour }}</div>
            <div v-if="YKB_SPECIALS.includes(filter.vehicle_segments)" class="text-accent">{{ course.segment.label }}</div>
            <div v-if="course.part && course.part.length" class="text-accent">{{ course.part }}</div>
          </div>
          <div class="school-name-container">
            <div v-if="isYKB" class="mb-1">
              <div class="text-accent">{{ course.start_hour }} - {{ course.end_hour }}</div>
              <div v-if="YKB_SPECIALS.includes(filter.vehicle_segments)" class="text-accent">{{ course.segment.label }}</div>
              <div v-if="course.part && course.part.length" class="text-accent">{{ course.part }}</div>
            </div>
            <h3 class="school-name">
              <a :href="routes.route('shared::schools.show', {citySlug: course.school.city.slug, schoolSlug: course.school.slug})">
                {{ course.school.name }}
              </a>
            </h3>
            <p>
              {{ course.school.address + ((course.school.address).includes(course.school.city.name) ? '' : ', ' + course.school.city.name) }}
            </p>
            <div v-if="avgRating(course.school)" class="mt-1">
              <stars :rating="avgRating(course.school)"></stars>
              <span class="d-flex align-items-center">
                <span class="search-avg">{{ avgRating(course.school) }}/5</span>
                <span class="search-qty">i betyg baserat på {{ course.school.ratings.length}} recensioner</span>
              </span>
            </div>
          </div>
          <div class="top-partner-container">
            <div class="top-partner-child">
              <div v-title="'Den här trafikskolan är kvalitetssäkrad som <br> samarbetspartner, vilket innebär att <br> trafikskolan rekommenderas starkt av både <br> elever och oss på Körkortsjakten. Trafikskolan gör allt för att ge sina elever en enastående <br> upplevelse tack vare dess utmärka service <br> och utbud.'">
                <i v-if="course.school.top_partner"  class="fa fa-medal fa-2x top-partner-medal"></i>
              </div>
              <p v-if="course.school.top_partner"> Top Partner</p>
            </div>
          </div>
          <div v-if="filter.vehicle_segments === ONLINE_LICENSE_THEORY || otherCourses(course).length" class="course-description-container">
            <div v-if="filter.vehicle_segments === ONLINE_LICENSE_THEORY">
              {{ course.description }}
            </div>
            <button
              v-if="otherCourses(course).length"
              class="btn btn-sm btn-outline-primary mt-1"
              @click="toggleCourseShowMore(course)">
              <span>{{ coursesShowMore[course.id] ? 'Färre' : 'Fler' }} tider&nbsp;</span>
              <icon :name="coursesShowMore[course.id] ? 'dropup' : 'dropdown'"></icon>
            </button>
            <div v-show="coursesShowMore[course.id] && otherCourses(course).length">
              <div v-for="otherCourse in otherCourses(course)" :key="otherCourse.id" class="pl-1 pt-1">
                <a :href="routes.route('shared::courses.show', { citySlug: course.school.city.slug, schoolSlug: course.school.slug, courseId: otherCourse.id })">
                  <span>Boka</span>
                  <icon name="arrow-right"></icon>
                </a>
                <h4>
                  {{ otherCourse.start_time | formatDate('dddd DD MMMM') }}
                  <span class="text-muted">
                    {{ otherCourse.start_hour }} - {{ otherCourse.end_hour }}
                  </span>
                </h4>
              </div>
            </div>
          </div>
<!--          Endast 4 platser kvar      -->
          <div class="btn-container">
            <icon class="hidden-md-down" name="gift-red"></icon>
            <a v-if="course.old_price" :href="routes.route('shared::schools.show', { citySlug: course.school.city.slug, schoolSlug: course.school.slug }) + '?open=' + course.vehicle_segment_id"
               class="btn btn-rounded btn-success book-course-btn-courseresults" @click="clickedBook(course)">
              Boka nu &nbsp;<span class="price-with-new"> {{ Math.round(course.price) }} kr </span> &nbsp;<span class="price-with-old"> {{ Math.round(course.old_price) }} kr </span>
            </a>
            <a v-else :href="routes.route('shared::schools.show', { citySlug: course.school.city.slug, schoolSlug: course.school.slug }) + '?open=' + course.vehicle_segment_id"
               class="btn btn-rounded btn-success book-course-btn-courseresults" @click="clickedBook(course)">
              Boka nu {{ Math.round(course.price) }} kr
            </a>
            <span v-if="course.seats <= 5" id="left_seats" class="mt-2 font-weight-bold">Endast {{course.seats}} platser kvar</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script type="text/babel">
import moment from 'moment';
import _ from 'lodash';
import $ from 'jquery';
import Smileys from 'vue-components/Smileys';
import routes from 'build/routes';
import Icon from 'vue-components/Icon';
import DateRange from 'vue-components/DateRange';
import AnalyticsService from 'services/AnalyticsService';

import VlDaySelector from 'vue-components/VlDaySelector';
import Stars from 'vue-components/Stars';

export default {
  props: [
    'groupedCourses',
    'segmentChanged',
    'courseCount',
    'comparedSchools',
    'compareToggle',
    'availableVehicleSegments',
    'filter',
    'resultToggle',
    'noResult',
  ],
  components: {
    'date-range': DateRange,
    'smileys': Smileys,

    VlDaySelector,
    Icon,
    Stars,
  },
  data() {
    return {
      startDate: moment().format('YYYY-MM-DD'),
      date: null,
      routes: routes,
      coursesShowMore: {},
      isYKB: window.location.pathname.includes('/kurser/ykb'),
    };
  },
  filters: {
    formatDate: function (value, format) {
      let dt = moment(value).format(format);
      return dt.charAt(0).toUpperCase() + dt.slice(1);
    }
  },
  computed: {
    isYKB() {
      window.location.pathname.includes('/kurser/ykb');
    },
  },
  methods: {
    toggleCourseShowMore(course) {
      const isShown = this.coursesShowMore[course.id] || false;
      this.$set(this.coursesShowMore, course.id, !isShown)
    },
    avgRating(school) {
      let getAverage = arr => {
        let reducer = (total, currentValue) => total + currentValue;
        let sum = arr.reduce(reducer)
        return sum / arr.length;
      }
      let ratings = school.ratings.map(rating => rating.rating)

      if (!ratings.length) {
        return 0;
      }
      const avgAvg = parseFloat(getAverage(ratings).toFixed(1));

      return Number.isInteger(avgAvg) ? parseInt(avgAvg) : avgAvg;
    },
    groupedCoursesCount(groupedCourses) {
      if (!groupedCourses) {
        return 0;
      }

      let keysObject = Object.keys(groupedCourses);

      return keysObject.length;
    },
    groupedCoursesFiltered(coursesGrouped) {
      if (!coursesGrouped) {
        return coursesGrouped;
      }

      let keysObject = Object.keys(coursesGrouped);
      if (!keysObject.length) {
        return coursesGrouped;
      }

      if (this.date) {
        let obj = {};
        obj[this.date] = coursesGrouped[this.date];
        return coursesGrouped[this.date] ? obj : [];
      }

      return coursesGrouped;
    },
    calendarCourses(coursesGrouped) {
      if (!coursesGrouped) {
        return [];
      }

      let courses = [],
          keysObject = Object.keys(coursesGrouped),
          that = this;

      if (!keysObject.length) {
        return coursesGrouped;
      }

      this.startDate = keysObject[0];
      _.forEach(coursesGrouped, function(values, key) {
        _.forEach(values, function(courseObj, courseKey) {
          courseObj.course = courseObj;
          courses.push(courseObj);
        })
      });

      return courses;
    },
    mouseOverForSchool(isMouseOver, school) {
      var marker = $('.map-marker[data-marker_id=' + school.id + ']')
      if (isMouseOver) {
        marker.addClass('hover')
      } else {
        marker.removeClass('hover')
      }
    },
    otherCourses(course) {
      return _.filter(course.school.courses, ['vehicle_segment_id', course.vehicle_segment_id])
    },
    isSelectedVehicleSegment(segment) {
      return this.filter.vehicle_segments == segment.id || this.filter.vehicle_segments == segment.name
    },
    notInComparison(id) {
      return !_.includes(this.comparedSchools.map((s) => s.id), id);
    },
    getSegmentName() {
      if (this.availableVehicleSegments.length > 0) {
        let item = _.find(this.availableVehicleSegments, {id: this.filter.vehicle_segments});
        return this.alternateNames(item.label);
      }
    },
    alternateNames(segmentName) {
      switch (segmentName.toLowerCase()) {
        case 'riskettan':
          return 'riskutbildningar';
        case 'introduktionskurs':
          return 'handledarutbildning';
        case 'teorilektion':
          return 'teoriutbildning';
        case 'mopedkurs am':
          return 'moppekurs';
        default:
          return segmentName;
      }
    },
    clickedBook(course) {
      AnalyticsService.eventCourse('clicked', course);
    },
  },
}
</script>

<style lang="scss" type="text/scss">

</style>
