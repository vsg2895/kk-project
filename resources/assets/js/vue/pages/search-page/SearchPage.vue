<template>
    <div id="search-page" class="d-flex mt-3">
        <div id="search-sidebar">
          <div v-if="resultType == 'COURSE'" class="container-fluid">
            <div class="row">
              <div class="col-lg-12">
                <h1 class="decrease-font mt-2" v-text="getHeader1() ? getHeader1() : (selectedCourse ? selectedCourse.label : '')"></h1>
              </div>
            </div>
            <div class="row hide-for-small">
              <div class="col-lg-12">
                <h6 v-html="head_desc ? head_desc : (selectedCourse ? selectedCourse.description : '') "></h6>
              </div>
            </div>
          </div>
          <button v-if="resultType !== 'SCHOOL' && selectedCourse && selectedCourse.id !== ONLINE_LICENSE_THEORY" class="close-map hidden-md-up" @click="showCalendar = !showCalendar" v-show="!showMap">
            <icon v-show="!showCalendar" name="calendar"/>
            <icon v-show="showCalendar" name="cross"/>
          </button>

            <div
                v-if="showCalendar && resultType !== 'SCHOOL' && selectedCourse && selectedCourse.id !== ONLINE_LICENSE_THEORY"
                class="search-calendar">
                <vl-day-selector
                    v-model="date"
                    v-if="showCalendar && startDate"
                    :courses="calendarCourses(calendardCourses)"
                    :default-date="startDate"
                    disabled-dates="finishDate"
                    single-month="true"
                    :show-close="false"
                    @moveBack="moveBack"
                    @moveForward="moveForward"
                    class="p-2" />
            </div>
            <div class="search-filters container mb-1">
                <search-filters :city-changed="cityChanged" :cities="cities"
                                :schools-changed="schoolsChanged" :selectable-schools="selectableSchools"
                                :vehicles="vehicles" :selected-city="selectedCity"
                                :selected-schools="filter.schools" :filter="filter"
                                :show-filters="showFilters" :show-filters-toggle="showFiltersToggle"
                                :result-type="resultType" :selected-vehicle="selectedVehicle"
                                :selected-course="selectedCourse">
                </search-filters>
            </div>

            <div id="search-results" class="content-loader container"
                 :class="{ 'loading': searchingSchools || searchingCourses }">
                <div class="loader-indicator"></div>

                <div v-if="resultType === 'SCHOOL'">
                    <school-results :schools="filteredSchools" :school-count="schoolCount" :school-best-deals="selectedCityBestDeal"
                                    :compared-schools="schoolsToCompare" :compare-toggle="compareToggle"
                                    :available-vehicle-segments="availableVehicleSegments"
                                    :sort-changed="sortChanged" :sort="filter.sort"
                                    :school-for-segment="schoolForSegment" :is-member-toggle="isMemberToggle"
                                    :is-member-filter-enabled="filter.is_member"
                                    :selected-city="selectedCity" :selected-vehicle="filter.vehicle_id"
                                    :no-result="noResultSchool" :in-paket-page="initialInPaket">
                    </school-results>
                </div>
                <div v-else>
                    <course-results :available-vehicle-segments="availableVehicleSegments"
                                    :filter="filter" :grouped-courses="groupedCoursesFiltered(groupedCourses)"
                                    :compared-schools="schoolsToCompare" :compare-toggle="compareToggle"
                                    :course-count="courseCount" :segment-changed="segmentChanged"
                                    :result-toggle="toggleResultType" :no-result="noResultCourse"></course-results>
                </div>

                <nav v-if="lastPage > 1">
                    <ul class="pagination">
                        <li class="page-item" :class="{'disabled': filter.page <= 1}"
                            :tabindex="filter.page <= 1 ? '-1' : ''">
                            <a class="page-link" rel="prev" href="#" aria-label="Previous"
                               @click="previousPage">
                                <span aria-hidden="true"><icon name="angle-left"></icon></span>
                                <span class="sr-only">Previous</span>
                            </a>
                        </li>
                        <li class="page-item" v-for="pageNumber in lastPage"
                            :class="{'active': pageNumber === filter.page}"
                            @click="toPage(pageNumber)">
                            <a class="page-link" href="#" v-text="pageNumber"></a>
                        </li>
                        <li class="page-item" :class="{'disabled': filter.page >= lastPage}"
                            :tabindex="filter.page >= lastPage ? '-1' : ''">
                            <a class="page-link" rel="next" href="#" aria-label="Next" @click="nextPage">
                                <span aria-hidden="true"><icon name="angle-right"></icon></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div id="search-map" class="hidden-md-down" :class="{ 'full-screen': showMap }">

          <div v-if="resultInfo" class="map-message" v-text="resultInfo"></div>
          <map-component :showMap="showMap" :center="mapCenter" :onBoundsChanged="boundsChanged"
                           :onInteract="onMapInteract" :markerData="markerData"
                           :marker-click="onMarkerClick"></map-component>
            <button class="close-map" @click="showMap = !showMap" v-show="showMap">
                <icon name="cross"/>
            </button>
        </div>

        <div class="search-comparison">
            <div class="search-comparison-backdrop preview"></div>
            <div class="search-comparison-backdrop full" @click="hideComparison()"></div>

            <div class="search-comparison-outer" :class="{ 'search-comparison-customize-mode': inCustomizeMode }">
                <div class="container">
                    <h3 class="search-comparison-heading" v-text="'Jämför trafikskolor'"></h3>
                    <button class="search-comparison-customize customize-mode-off"
                            :class="{ 'customized': activeSegments.length > activeVehicleSegments.length }"
                            @click="inCustomizeMode = !inCustomizeMode">
                        <icon name="angle-right"/>
                        <span v-text="'Anpassa prisval'"></span>
                        <icon class="asterisk" name="Asterisk"/>
                    </button>

                    <button class="search-comparison-customize customize-mode-on"
                            :class="{ 'customized': activeSegments.length > activeVehicleSegments.length }"
                            @click="inCustomizeMode = !inCustomizeMode">
                        <icon name="angle-left"/>
                        <span v-text="'Stäng'"></span>
                        <icon class="asterisk" name="Asterisk"/>
                    </button>

                    <div class="search-comparison-inner container-fluid">

                        <button class="btn btn-primary comparison-show-full show-comparison-btn-searchpage"
                                @click="showComparison()" v-text="'Jämför'"></button>
                        <button class="comparison-hide-full" @click="hideComparison()">
                            <icon name="cross" size="lg"></icon>
                        </button>

                        <div class="search-comparison-preview container-fluid">
                            <div class="row">
                                <div class="col-lg-1 hidden-md-down"></div>
                                <div v-for="school in schoolsToCompare" class="col-md-4 col-lg-3 text-xs-center">
                                    <label class="comparison-remove">
                                        <input class="custom-control-input" :id="'include-' + school.id" type="checkbox"
                                               :value="school.id" v-model="compare.schools">
                                        <icon name="cross"></icon>
                                    </label>
                                    <h3 class="school-name h4"><a
                                            :href="routes.route('shared::schools.show', {citySlug: school.city.slug, schoolSlug: school.slug})">{{
                                        school.name
                                        }}</a></h3>
                                    <template v-if="school.average_rating">
                                        <span class="school-rating"><smileys :value="school.average_rating"></smileys>
                                        <span v-text="school.average_rating"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <div class="link comparison-clear" @click="clearComparison()" v-text="'Rensa'"></div>
                        </div>

                        <div class="comparison-table">
                            <table class="table">
                                <thead>
                                <tr class="hidden-md-up">
                                    <th v-for="school in schoolsToCompare" class="text-xs-center">
                                        <a :href="routes.route('shared::schools.show', {citySlug: school.city.slug, schoolSlug: school.slug})">
                                            <h3 class="h4" v-text="school.name"></h3>
                                            <icon name="link-open"></icon>
                                        </a>
                                    </th>
                                </tr>
                                <tr>
                                    <th class="text-muted">Bedömning</th>
                                    <th v-for="school in schoolsToCompare" class="text-xs-center">
                                        <h3 class="school-name h4 hidden-sm-down"><a
                                                :href="routes.route('shared::schools.show', {citySlug: school.city.slug, schoolSlug: school.slug})">{{
                                            school.name }}</a></h3>
                                        <span class="school-rating" v-if="school.average_rating"><smileys
                                                v-bind:value="school.average_rating"></smileys>
                                            <span v-text="school.average_rating"></span>
                                        </span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="segment in activeSegments" class="comparison-segment"
                                    :class="{ 'unactive-segment': !isActiveSegment(segment) }">
                                    <th class="description">
                                        <label class="custom-control custom-checkbox">
                                            <input class="custom-control-input" :id="segment.name" :value="segment.name"
                                                   v-model="activeVehicleSegments" type="checkbox">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">
                                            <h4 class="label">{{ segment.label }}
                                                <popup v-if="segment.description"
                                                       :content="segment.description"></popup>
                                            </h4>
                                            <div class="comment small" v-text="segment.default_comment"></div>
                                        </span>
                                        </label>
                                    </th>
                                    <td v-for="school in schoolsToCompare" class="price">
                                        <span class="h3 text-numerical"
                                              v-text="segment.default_price ? segment.default_price + ' kr' : school.formatted_prices[segment.name].price_suffix">
                                        </span>
                                        <div v-show="school.formatted_prices[segment.name].comment"
                                             class="comment small"
                                             v-text="school.formatted_prices[segment.name].comment">
                                        </div>
                                        <div v-show="!school.formatted_prices[segment.name].comment && school.formatted_prices[segment.name].price_per_lesson"
                                             class="comment small"
                                             v-text="`${school.formatted_prices[segment.name].price_per_lesson} kr/ ${segment.default_comment}`">
                                        </div>
                                    </td>
                                </tr>
                                <tr class="spacer">
                                    <th></th>
                                    <td v-for="school in schoolsToCompare"></td>
                                </tr>
                                <tr class="comparison-table-sum">
                                    <th class="comparison-title">
                                        <h4 v-text="'Jämförpris B-körkort'"></h4>
                                    </th>
                                    <td v-for="school in schoolsToCompare" class="text-xs-center">
                                        <span class="h2 text-numerical"
                                              :class="{'lowest-price': hasLowestPrice(school), 'customized': hasLowestPrice(school) && activeSegments.length > activeVehicleSegments.length }">
                                            <span v-text="getTotalPriceFormatted(school)"></span>
                                            <icon class="asterisk" name="asterisk"/>
                                        </span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <div class="clearfix">
                                <a target="_blank"
                                   :href="routes.route('shared::pages.contact', {subject: 'rapportera'})"
                                   class="invalid-info small text-muted float-xs-right">
                                    <span v-text="'Rapportera felaktig prisinformation'"></span>
                                    <icon size="sm" name="arrow-right"></icon>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Api from 'vue-helpers/Api';
import moment from 'moment';
import _ from 'lodash';
import $ from 'jquery';
import routes from 'build/routes.js';
import CustomMarker from 'components/CustomMarker.js';

import Map from 'vue-components/Map';
import Icon from 'vue-components/Icon';
import VueHead from 'vue-head';
import Popup from 'vue-components/Popup';
import Smileys from 'vue-components/Smileys';
import CourseResults from './CourseResults';
import SchoolResults from './SchoolResults';
import SearchFilters from './SearchFilters';

import VlDaySelector from 'vue-components/VlDaySelector';

export default {
  props: [
    'initialCity',
    'initialLongitude',
    'initialLatitude',
    'initialResultType',
    'initialCourseType',
    'initialVehicle',
    'initialTitle',
    'initialInPaket',
  ],
  components: {
    SearchFilters,
    CourseResults,
    SchoolResults,
    Smileys,
    'map-component': Map,
    Popup,
    Icon,
    VueHead,
    VlDaySelector,
  },
  data() {
    return {
      title: '',
      metaDescription: '',
      modalScroll: false,
      scrollPosition: 0,
      showCalendar: true,
      showMap: false,
      showFilters: false,
      isComparisonVisible: false,
      inCustomizeMode: false,
      routes: routes,
      searchingSchools: false,
      searchingCourses: false,
      mapCenter: {lat: 59.329323, lng: 18.068581},
      resultType: this.initialResultType,
      resultInfo: null,
      cities: [],
      groupedCourses: [],
      filteredSchools: [],
      schools: [],
      bookableVehicleSegments: [],
      vehicles: [],
      vehicleSegments: [],
      lastPageSchools: 0,
      lastPageCourses: 0,
      lastPage: 0,
      schoolCount: 0,
      courseCount: 0,
      calendardCourses: [],

      schoolCoursePage: 1,
      filter: {
        is_member: 0,
        city_id: window.location.pathname.slice(-"/all".length) === "/all" ? '' : 143,
        vehicle_id: 1,
        vehicle_segments: 0,
        schools: '',
        from: moment().format('YYYY-MM-DD'),
        to: moment().endOf('month').format('YYYY-MM-DD'),
        page: 1,
        sort: 'PRICE',
        bounds: '',
        all_courses: window.location.pathname.slice(-"/all".length) === "/all" ? 1 : 0,
        calendar: 0,
        all: true
      },
      lastFilter: {},
      compare: {
        schools: [],
      },
      activeVehicleSegments: [],
      schoolsToCompare: [],
      map: null,
      markerData: [],
      noResultSchool: false,
      noResultCourse: false,
      selectedCityBestDeal: [],
      startDate: null,
      finishDate: {to: moment().add(12, 'month').format('YYYY-MM-DD')},

      date: null,
      getCoursesQtyRequests: 0,

      header1: '',
      head_desc: '',
      headers1: {
        'SCHOOL-1': 'Trafikskolor körkort',
        'SCHOOL-2': 'Trafikskolor körkort',
        'SCHOOL-3': 'Trafikskolor körkort',
        'SCHOOL-1-city': 'Trafikskolor i $city',
        'SCHOOL-2-city': 'Trafikskolor i $city',
        'SCHOOL-3-city': 'Trafikskolor i $city',
        'COURSE-1': 'Boka kurser för Ditt körkort',
        'COURSE-1-6': 'Riskettan för Bil',
        'COURSE-1-6-city': 'Riskettan för Bil i $city',
        'COURSE-1-7': 'Introduktionskurs körkort',
        'COURSE-1-7-city': 'Introduktionskurs $city',
        'COURSE-1-13': 'Risktvåan körkort',
        'COURSE-1-13-city': 'Risktvåan $city',
        'COURSE-1-16': 'Teoriutbildning Bil',
        'COURSE-1-16-city': 'Teoriutbildning Bil i $city',
        'COURSE-2-10': 'Riskettan MC',
        'COURSE-2-10-city': 'Riskettan MC $city',
        'COURSE-3-15': 'Mopedkurs AM',
        'COURSE-3-15-city': 'Mopedkurs AM $city'
      },
      header_descs: {
        'SCHOOL-1': 'Jämför trafikskolor. Vi har information om kurser och priser för alla tarfikskolor. Hitta lediga tider, boka & betala online med Klarna. Snart har du ditt körkort!',
        'SCHOOL-2': 'Jämför trafikskolor. Vi har information om kurser och priser för alla tarfikskolor. Hitta lediga tider, boka & betala online med Klarna. Snart har du ditt körkort!',
        'SCHOOL-3': 'Jämför trafikskolor. Vi har information om kurser och priser för alla tarfikskolor. Hitta lediga tider, boka & betala online med Klarna. Snart har du ditt körkort!',
        'SCHOOL-1-city': 'Jämför trafikskolor i $city. Vi har information om kurser och priser för alla trafikskolor i $city. Hitta lediga tider, boka & betala online med Klarna. Snart har du ditt körkort!',
        'SCHOOL-2-city': 'Jämför trafikskolor i $city. Vi har information om kurser och priser för alla trafikskolor i $city. Hitta lediga tider, boka & betala online med Klarna. Snart har du ditt körkort!',
        'SCHOOL-3-city': 'Jämför trafikskolor i $city. Vi har information om kurser och priser för alla trafikskolor i $city. Hitta lediga tider, boka & betala online med Klarna. Snart har du ditt körkort!',
        'COURSE-1': ' Vi har information om kurser och priser för alla trafikskolor i Sverige. Hitta lediga tider, boka & betala online med Klarna. Snart har du ditt körkort!',
        'COURSE-1-6': 'Hitta tider, boka & betala online! Riskettan är del 1 i den obligatoriska riskutbildningen för B-körkort (del 2 är halkbanan).',
        'COURSE-1-6-city': 'Hitta tider i $city boka & betala online! Riskettan är del 1 i den obligatoriska riskutbildningen för B-körkort (del 2 är halkbanan).',
        'COURSE-1-7': 'Introduktionsutbildning (tidigare kallad handledarutbildning) är ett krav för att få övningsköra privat. Både du och den du ska köra med måste gå kursen.',
        'COURSE-1-7-city': 'Boka och betala online! Handledarutbildning (Introduktionsutbildning) i $city är ett krav för att få övningsköra privat. Både du och den du ska köra med måste gå kursen.',
        'COURSE-1-13': 'Boka och betala online! Risktvåan (halkan) är en obligatorisk del av din körkortsutbildning.',
        'COURSE-1-13-city': 'Boka och betala online! Risktvåan (halkan) i $city är en obligatorisk del av din körkortsutbildning.',
        'COURSE-1-16': 'Förbered dig på teoriprovet med klassrumsledd undervisning. Många tycker att det är enklare än att läsa i boken.',
        'COURSE-1-16-city': 'Tycker du att det är svårt att att läsa körkortsboken hemma? Låt en lärare undervisa och ta ut det viktigaste! Hitta kurstillfällen i $city',
        'COURSE-2-10': 'Hitta tid, boka & betala online!Riskettan är del 1 i den obligatoriska riskutbildningen för B-körkort (del 2 är halkbanan).',
        'COURSE-2-10-city': 'Boka och betala online! Riskettan för MC i $city. Ta Ditt MC-körkort nu.',
        'COURSE-3-15': 'Boka och betala online! Mopedkurs. Ta Ditt moppe-körkort nu.',
        'COURSE-3-15-city': 'Boka och betala online! Mopedkurs i $city. Ta Ditt moppe-körkort nu.'
      },
      courses: {
        'introduktionskurser': 7,
        'riskettan': 6,
        'teorilektion': 16,
        'risktvaan': 13,
        'mopedkurs': 15,
        'riskettanmc': 10
      },
      segments: window.location.pathname.split('/')
    };
  },
  head: {
    title: function () {
      return {
        complement: 'Körkortsjakten',
        inner: this.title,
      }
    },
    meta: function () {
      return [
        {
          name: 'description', content: this.metaDescription, id: 'htde',
        },
        {
          name: 'og:description', content: this.metaDescription, id: 'ogde',
        }
      ]
    },
  },
  watch: {
    date() {
      if (!this.date) {
        return;
      }
      this.filter.from = this.filter.to = this.date;
      this.searchCourses();
    },
    lastPage() {
      if (this.filter.page > this.lastPage) {
        this.toPage(this.lastPage);
      }
    },
    modalScroll() {
      var vm = this
      if (this.modalScroll && vm.scrollPosition) {
        setTimeout(function () {
          $(window).scrollTop(vm.scrollPosition)
        }, 0);
      }
    },
    resultType() {
      if (this.resultType === 'COURSE') {
        this.lastPage = this.lastPageCourses;
        if (!this.filter.vehicle_segments) {
          let segments = this.availableVehicleSegments;
          let segment = segments[0];
          if (segment) {
            this.$set(this.filter, 'vehicle_segments', segment.id);
          }
        }
      } else {
        this.lastPage = this.lastPageSchools;
      }
      //this.setUrl();
      this.toPage(1);
    },
    filter: {
      handler(oldVal, newVal) {
        var vm = this;

        if (vm.filterHasChanged()) {
          if (!_.isEmpty(this.lastFilter) && this.lastFilter.vehicle_id != newVal.vehicle_id) {
            this.filter.sort = "RATING";
            let segments = vm.availableVehicleSegments;
            let segment = segments[0];
            if (segment) {
              vm.$set(this.filter, 'vehicle_segments', segment.id);
            }
          }

          var city = _.find(vm.cities, function (city) {
            return city.id == vm.filter.city_id;
          });

          // if (vm.filter.city_id != vm.lastFilter.city_id && city) {
          if (((vm.filter.city_id != vm.lastFilter.city_id) && (vm.filter.city_id !== 143 && vm.lastFilter.city_id !== undefined)) && city) {//second condition added to fix Stockholm filter issue on mobile
            vm.mapCenter = {lat: parseFloat(city.latitude), lng: parseFloat(city.longitude)};
            vm.lastFilter = _.clone(vm.filter);
          } else {
            vm.search();
            vm.setActiveVehicleSegments(vm.filter.vehicle_id);
          }
        }
        //vm.setUrl();
      },
      deep: true
    },
    compare: {
      handler(oldVal, newVal) {
        var vm = this;

        _.forEach(vm.markers, function (marker) {
          if (marker && marker.id) {
            if (_.includes(newVal.schools, marker.id)) {
              marker.setActive();
            } else {
              marker.setInactive();
            }
          }
        });

        var schools = _.unionBy(this.courseSchools(this.groupedCoursesFiltered(this.groupedCourses)), vm.filteredSchools, vm.schoolsToCompare, function (school) {
          return school.id;
        });

        schools = _.filter(schools, function (school) {
          return _.includes(vm.compare.schools, school.id);
        });

        vm.schoolsToCompare = schools;
        vm.setActiveVehicleSegments(vm.filter.vehicle_id);

        if (newVal.schools.length > 0) {
          $('.search-comparison').addClass('visible');
        } else {
          $('.search-comparison').removeClass('visible');
        }
      },
      deep: true
    }
  },
  computed: {
    selectedCity: function () {
      var vm = this;
      return _.find(vm.cities, function (city) {
        let selectedCity = (city.id == vm.filter.city_id);

        if (selectedCity && city.best_deal.length) {
          (async () => {
            var bestSchools = [];
            _.each(city.best_deal, (value) => bestSchools.push(value.id));
            vm.selectedCityBestDeal = city.best_deal.length === 1 ? [await Api.findSchool(bestSchools.join(','))] : await Api.findSchool(bestSchools.join(','));
          })();
        }

        return selectedCity;
      })
    },
    selectedCourse: function () {
      let vm = this;
      return _.find(vm.availableVehicleSegments, function (segment) {
        return segment.id == vm.filter.vehicle_segments;
      })
    },
    selectedVehicle: function () {
      let vm = this;
      return _.find(vm.vehicles, function (vehicle) {
        return vehicle.id == vm.filter.vehicle_id;
      })
    },
    isInModal() {
      let inModal = this.showFilters || (!this.isFiltersActionBarVisible || (this.isFiltersActionBarVisible && this.showMap));

      if (inModal) {
        this.scrollPosition = $(window).scrollTop();
        $(window).scrollTop(0)
      }
      this.modalScroll = !inModal;
      return inModal
    },
    isFiltersActionBarVisible() {
      return !(this.showFilters || this.isComparisonVisible)
    },
    selectableSchools() {
      var vm = this;

      var schools = _.filter(vm.schools, function (school) {
        return _.includes(school.available_vehicles_ids, vm.filter.vehicle_id);
      });

      if (schools && schools.length) {
        if (vm.filter.bounds !== '' && vm.filter.bounds) {
          let [latLow, lngLow, latHigh, lngHigh] = _.words(vm.filter.bounds, /[^, ]+/g);

          return _.filter(schools, function (school) {
            return school.latitude >= latLow && school.latitude <= latHigh && school.longitude >= lngLow && school.longitude <= lngHigh;
          });
        } else if (vm.filter.city_id !== '' && vm.filter.city_id) {
          return _.filter(schools, function (school) {
            return school.city_id == vm.filter.city_id
          });
        } else {
          return schools;
        }
      } else {
        return [];
      }
    },
    availableVehicleSegments() {
      var vm = this;
      return _.filter(vm.bookableVehicleSegments, function (vehicleSegment) {
        return !vm.filter.vehicle_id || vehicleSegment.vehicle_id == vm.filter.vehicle_id;
      });
    },
    courseSchools() {
      return _.flatten(_.map(this.groupedCoursesFiltered(this.groupedCourses), function (group) {
        return _.map(group, function (course) {
          return course.school;
        })
      }))
    },
    flattenedCourses() {
      return _.flatten(_.map(this.groupedCoursesFiltered(this.groupedCourses), function (group) {
        return _.map(group, function (course) {
          return course;
        })
      }))
    },
    activeSegments() {
      var vm = this;
      return _.filter(this.vehicleSegments, function (segment) {
        return segment.vehicle_id == vm.filter.vehicle_id && segment.comparable;
      })
    }
  },
  filters: {
    formatDate(value, format) {
      return moment(value).format(format);
    }
  },
  methods: {
    setHeaders(slug) {
      this.header1 = this.setVariables(this.headers1[slug]);

      if (this.selectedCity && this.selectedCity.info && this.selectedCity.info[this.segments[1]]) {
        this.head_desc = this.selectedCity.info[this.segments[1]];
      } else {
        this.head_desc = this.setVariables(this.header_descs[slug]);
      }
      return this.header1;
    },
    getHeader1() {
      let slug = this.resultType;
      if (typeof this.selectedVehicle !== "undefined") {
        slug += '-' + this.selectedVehicle.id;
      }

      if (this.resultType === 'COURSE') {
        slug += '-' + this.courses[this.segments[1]];
      } else {
        this.collapsed = false;
      }

      if (typeof this.selectedCity !== "undefined") {
        slug += '-' + 'city';
      }
      return this.setHeaders(slug);
    },
    setVariables(text) {
      if (typeof this.selectedCity !== "undefined" && text) {
        return text.replace(/\$city/g, this.selectedCity.name);
      }
      return text;
    },
    moveForward() {
      this.date  = null;
      this.filter.from = moment(this.filter.from).add(1, 'month').startOf('month').format('YYYY-MM-DD');
      this.filter.to = moment(this.filter.to).add(1, 'month').endOf('month').format('YYYY-MM-DD');
      this.searchCourses();
    },
    moveBack() {
      this.date  = null;
      this.filter.from = moment(this.filter.from).subtract(1, 'month').startOf('month').format('YYYY-MM-DD');
      this.filter.to = moment(this.filter.to).subtract(1, 'month').endOf('month').format('YYYY-MM-DD');
      this.searchCourses();
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

      this.startDate = moment(keysObject[0]).format('YYYY-MM-DD');

      _.forEach(coursesGrouped, function(values, key) {
        _.forEach(values, function(courseObj, courseKey) {
          courseObj.course = courseObj;
          courses.push(courseObj);
        })
      });

      return courses;
    },
    /****************
     * API
     ***************/
    async searchCoursesPrices() {
      this.positionLoader();
      let lastFilter = _.clone(this.filter);
      lastFilter.calendar = 1;
      lastFilter.to = moment(this.filter.to).add(12, 'month').format('YYYY-MM-DD');

      let {booted, data} = await Api.searchCourses(lastFilter);

      let {courses} = data;
      this.calendardCourses = courses;
    },

    async searchCourses() {
      this.searchingCourses = true;
      this.noResultCourse = false;
      this.positionLoader();
      let {booted, data} = await Api.searchCourses(this.filter);
      let {courses, total, last_page} = data;
      this.searchingCourses = false;
      this.groupedCourses = courses;
      this.courseCount = total;
      this.lastPageCourses = last_page;
      if (this.resultType === 'COURSE') {
        this.markerData = this.flattenedCourses;
        this.lastPage = this.lastPageCourses;
        let keysObject = this.coursesGrouped ? Object.keys(this.coursesGrouped) : {};

        if (!this.markerData.length && !keysObject.length && !this.date && this.getCoursesQtyRequests < 5) {
          this.getCoursesQtyRequests++;
          this.moveForward();
          return;
        }

        if (this.markerData && this.markerData[0]) {
          this.getCoursesQtyRequests = 5;
          this.startDate = moment(this.markerData[0]['start_time']).format('YYYY-MM-DD');
        }
      }

      this.updateVisibleResultsInfo();
      if (booted == 0) {
        this.noResultCourse = true;
      }
    },
    async searchSchools() {
      this.searchingSchools = true;
      this.noResultSchool = false;
      this.positionLoader();
      this.filter.in_paket = this.initialInPaket;
      let {booted, data} = await Api.searchSchools(this.filter);
      let {schools, total, last_page} = data;
      this.searchingSchools = false;
      this.filteredSchools = schools;
      this.schoolCount = total;
      this.lastPageSchools = last_page;
      if (this.resultType === 'SCHOOL') {
        this.markerData = this.filteredSchools;
        this.lastPage = this.lastPageSchools;
      }

      this.updateVisibleResultsInfo();
      if (booted == 0) {
        this.noResultSchool = true;
      }
    },
    updateVisibleResultsInfo: function () {
      var type = 'trafikskolor';
      var total = this.schoolCount;
      if (this.resultType == 'COURSE') {
        type = 'kurser';
        total = this.courseCount;
      }

      if (!this.markerData.length) {
        this.resultInfo = 'Inga matchande ' + type + ' i områden';
        return;
      }

      if (total > this.markerData.length) {
        this.resultInfo = 'Visar ' + this.markerData.length + ' av ' + total + ' bäst matchade ' + type;
      } else {
        this.resultInfo = 'Visar ' + this.markerData.length + ' bäst matchade ' + type;
      }
    },
    async getCities() {
      this.cities = await Api.getCities();
    },
    async getSchools() {
      this.schools = await Api.getSchools();
    },
    async getVehicleTypes() {
      this.vehicles = await Api.getVehicleTypes();
    },
    async getVehicleSegments() {
      this.vehicleSegments = await Api.getVehicleSegments();
      this.bookableVehicleSegments = _.filter(this.vehicleSegments, function (segment) {
        return segment.bookable;
      });
      this.setActiveVehicleSegments(this.filter.vehicle_id);
    },

    /*******************
     * FILTER
     ******************/
    filterHasChanged() {
      if (!this.lastFilter) {
        return true;
      }

      return !(_.isEqual(this.filter, this.lastFilter));
    },

    /*******************
     * ACTIONS
     ******************/
    isMemberToggle() {
      var is_member = 1 - this.filter.is_member
      this.$set(this.filter, 'is_member', is_member);
    },
    schoolForSegment(schoolId, segmentId) {
      this.resultType = 'COURSE'
      if (segmentId) this.$set(this.filter, 'vehicle_segments', segmentId);
      this.$set(this.filter, 'schools', [schoolId]);
    },
    toggleResultType() {
      if (this.resultType === 'SCHOOL') {
        this.resultType = 'COURSE';
      } else {
        this.resultType = 'SCHOOL';
      }

      this.updateVisibleResultsInfo();
    },
    setResultType(type) {
      this.resultType = type;
      this.updateVisibleResultsInfo();
    },
    search() {
      this.lastFilter = _.clone(this.filter);
      let promiseArray = [];
      if (this.resultType === 'COURSE') {
        promiseArray =[this.searchCourses()]
      } else {
        promiseArray =[this.searchSchools()]
      }
      return Promise.all(promiseArray);
    },
    showFiltersToggle() {
      if (this.showMap) this.showMap = false;
      this.showFilters = !this.showFilters;
    },
    applyFilters() {
      this.showFilters = false
    },
    /*******************
     * MAP
     * ****************/
    boundsChanged(bounds) {
      if (!bounds) {
        this.search();
      }

      this.$set(this.filter, 'bounds', bounds);

      if (this.resultType === 'COURSE') {
        // this.date = null;
        // this.getCoursesQtyRequests = 5;
        // this.filter.from = moment().format('YYYY-MM-DD');
        // this.filter.to = moment().endOf('month').format('YYYY-MM-DD');
        (async () => {
          this.searchCoursesPrices();
        })()
      }
    },
    onMapInteract() {
      this.$set(this.filter, 'city_id', '');
    },

    /*******************
     * COMPARISON
     ******************/
    showComparison() {
      if (this.isComparisonVisible) {
        this.hideComparison()
      } else {
        $('.search-comparison-preview').fadeOut(750);
        $('.search-comparison').addClass('visible-full');
        $('.search-comparison-heading').css('visibility', 'visible').hide().fadeIn(750);
        $('.comparison-show-full').fadeOut(500);
        $('.comparison-hide-full').fadeIn(500);
        this.isComparisonVisible = true;
      }
    },
    hideComparison() {
      $('.search-comparison-preview').fadeIn(750);
      $('.search-comparison').removeClass('visible-full');
      $('.search-comparison-heading').fadeOut(750, function () {
        $(this).show().css('visibility', 'hidden');
      });
      $('.comparison-show-full').fadeIn(500);
      $('.comparison-hide-full').fadeOut(500);
      this.isComparisonVisible = false
    },
    clearComparison() {
      this.$set(this.compare, 'schools', []);
      this.compareSchools = [];
    },
    setActiveVehicleSegments(vehicleId) {
      let vm = this;

      let vehicleSegments = _.filter(this.vehicleSegments, function (segment) {
        let correctVehicle = segment.vehicle_id == vehicleId;
        let gotPrice = true;
        _.each(vm.schoolsToCompare, (school) => {
          if (school.formatted_prices[segment.name]) {
            if (school.formatted_prices[segment.name].price == null) {
              gotPrice = false;
            }
          }
        });

        return correctVehicle && gotPrice && segment.comparable;
      }).map(function (segment) {
        return segment.name;
      });

      vm.activeVehicleSegments = vehicleSegments;

    },
    isActiveSegment(segment) {
      return _.includes(this.activeVehicleSegments, segment.name)
    },
    getTotalPrice(school) {
      var vm = this;

      return _.reduce(this.activeVehicleSegments, function (sum, name) {
        let price = 0;
        if (school.formatted_prices[name].price) {
          price = parseInt(school.formatted_prices[name].price);
        }

        if (!price) {
          var filterName = name;
          let currentSegment = _.filter(vm.vehicleSegments, segment => { return segment.name === filterName; });
          price = parseInt(currentSegment[0].default_price ? currentSegment[0].default_price : 0);
        }

        return sum + price;
      }, 0);
    },
    getTotalPriceFormatted(school) {
      return this.getTotalPrice(school) + " kr";
    },
    hasLowestPrice(school) {
      var vm = this;
      var price = this.getTotalPrice(school);

      var prices = _.map(vm.schoolsToCompare, function (school) {
        return vm.getTotalPrice(school);
      });

      return _.min(prices) == price;
    },

    /*******************
     * URL
     ******************/
    buildCurrentUrl() {
      var data = {};
      var vm = this;

      var courseType = _.find(vm.bookableVehicleSegments, function (segment) {
        return vm.filter.vehicle_segments == segment.id
      });

      if (courseType && this.resultType === 'COURSE') {
        data.courseType = courseType.label.toLowerCase();
      }

      let vehicleString = ''; //'?vehicle_id=' + vm.filter.vehicle_id;

      if (vm.filter.city_id) {

        var city = _.find(vm.cities, function (city) {
          return vm.filter.city_id == city.id;
        });

        if (city) data.citySlug = city.slug;

        if (this.resultType === 'COURSE') {
          //return routes.route('shared::search.courses', data) + vehicleString;
        } else {
          return routes.route('shared::search.schools', data) + vehicleString;
        }
      } else {
        if (this.resultType === 'COURSE') {
          return routes.route('shared::courses.index', data) + vehicleString;
        } else {
          return routes.route('shared::schools.index', data) + vehicleString;
        }
      }
    },
    setUrl() {
      let url = this.buildCurrentUrl();
      window.history.pushState(null, null, url);
      this.setMeta();
    },
    setMeta() {
      let vm = this;
      let city;
      if (vm.filter.city_id) {
        city = _.find(vm.cities, (city) => {
          return vm.filter.city_id === city.id;
        });
      }
      let courseType = _.find(vm.bookableVehicleSegments, (segment) => {
        return vm.filter.vehicle_segments === segment.id
      });
      this.metaDescription = 'K&ouml;rkortsjakten &auml;r en oberoende prisj&auml;mf&ouml;relse av trafikskolor. Vi vill ge all information blivande f&ouml;rare beh&ouml;ver fram till den dagen de har k&ouml;rkortet i sin hand - allt från handledarkursen, valet av trafikskola till uppkörningen';
      if (courseType && this.resultType === 'COURSE') {
        this.metaDescription = 'Sök, jämför och boka ' + courseType.label + ' direkt hos Körkortsjakten. Sveriges enda jämföreslsesite för körkort. Betala enkelt med Klarna.';
        if (city) {
          this.metaDescription = 'Sök, jämför och boka ' + courseType.label + ' i ' + city.name + ' direkt hos körkortsjakten. Hitta billigaste och bästa körkortsutbildningen i ' + courseType.label;
        }
      }
      //let title = this.metaTitle();
      //if (title) {
      //    this.title = title;
      //}
      this.$emit('updateHead');
    },
    metaTitle() {
      let title1 = '';
      let title2 = '|';
      var vm = this;
      switch (this.resultType) {
        case 'SCHOOL':
          title1 += 'Körskolor ';
          title2 += ' Trafikskolor';
          break;
        case 'COURSE':
          let segment = _.find(vm.bookableVehicleSegments, function (segment) {
            return vm.filter.vehicle_segments === segment.id
          });
          if (segment && segment.label) {
            title1 += this.alternateCourseNames(segment.label) + ' ';
            title2 += ' Boka ' + segment.label;
          } else {
            title1 += 'Körlektion ';
            title2 += ' Boka kurs';
          }
          break;
        default:
          return false;
      }
      if (this.filter.vehicle_id) {
        let vehicle = _.find(vm.vehicles, v => {
          return v.id === vm.filter.vehicle_id
        });
        if (!vehicle) {
          return false
        }
        title1 += vehicle && vehicle.label ? `${vehicle.label} ` : '';
      }
      if (this.filter.city_id) {
        let city = _.find(vm.cities, c => {
          return c.id === vm.filter.city_id
        });
        title2 += city && city.name ? ` i ${city.name}` : '';
      }
      return title1 + title2;
    },
    alternateCourseNames(segmentName) {
      switch (segmentName.toLowerCase()) {
        case 'riskettan':
          return 'Riskutbildning';
        case 'introduktionskurs':
          return 'Handledarutbildning';
        case 'teorilektion':
          return 'Teoriutbildning';
        case 'mopedkurs am':
          return 'Moppekurs';
        default:
          return segmentName.charAt(0).toUpperCase() + segmentName.slice(1);
      }
    },
    /****************
     * PAGINATION
     ***************/
    getSchoolRoute(school) {
      return routes.route('shared::schools.show', {citySlug: school.city.slug, schoolSlug: school.slug});
    },
    async toPage(page) {
      if (this.filter.page != page) {
        this.$set(this.filter, 'page', page);
        await this.search();
      } else {
        this.markerData = this.resultType == 'COURSE' ? this.courseSchools(this.groupedCoursesFiltered(this.groupedCourses)) : this.filteredSchools;
      }
      this.scrollUp();
    },
    async previousPage() {
      this.$set(this.filter, 'page', this.filter.page - 1);
      await this.search();
      this.scrollUp();
    },
    async nextPage() {
      this.$set(this.filter, 'page', this.filter.page + 1);
      await this.search();
      this.scrollUp();
    },
    scrollUp() {
      $('#search-sidebar').scrollTop(0);
    },
    compareToggle(school) {
      if (!_.includes(this.schoolsToCompare.map((s) => s.id), school.id)) {
        this.schoolsToCompare.push(school)
      } else {
        _.remove(this.schoolsToCompare, {
          id: school.id
        })
      }
      this.compareChanged(this.schoolsToCompare.map((s) => s.id))
    },
    compareChanged(data) {
      this.$set(this.compare, 'schools', data);
    },
    sortChanged(sort) {
      if (sort.name !== undefined) {
        sort = sort.name;
      }
      this.$set(this.filter, 'sort', sort);
    },
    cityChanged(city) {
      this.$set(this.filter, 'city_id', city.id);
      this.schoolsChanged([]);
      this.setUrl();
    },
    schoolsChanged(ids) {
      if (typeof ids.id != 'undefined') {
        ids = ids.id;
      }

      this.$set(this.filter, 'schools', ids);
    }
    ,
    segmentChanged(id) {
      this.$set(this.filter, 'vehicle_segments', id);
    },
    /****************
     * LOADER
     ***************/
    positionLoader() {
      var $el = $('#search-results');
      var elH = $el.outerHeight(),
          H = $(window).height(),
          r = $el[0].getBoundingClientRect(), t = r.top, b = r.bottom;
      var topOffset = (Math.max(0, t > 0 ? Math.min(elH, H - t) : (b < H ? b : H))) / 2;
      $('.loader-indicator').css("top", (topOffset.toFixed() + 'px'));
    },
    onMarkerClick(object) {
      let selector = '.school-' + (object.school_id ? object.school_id : object.id);

      $('.search-result-object').removeClass('school-active');
      $('.search-result-object').removeClass('school-hover-selected');

      $(selector).addClass('school-active');
      $(selector).addClass('school-hover-selected');

      setTimeout(function(){
        $(selector).removeClass('school-active');
        $(selector).removeClass('school-hover-selected');
      },6000);
      this.$scrollTo(selector, 500, { container: '.search-result-list', offset: -120 });
    }
  },
  created() {
    this.title = this.initialTitle ? this.initialTitle : 'Körskolor | Trafikskolor i Stockholm ';
    this.filter.city_id = this.initialCity ? (window.location.pathname.slice(-"/all".length) === "/all" ? '' : this.initialCity) : (window.location.pathname.slice(-"/all".length) === "/all" ? '' : 143);
    this.filter.vehicle_id = this.initialVehicle ? this.initialVehicle : 1;
    this.filter.vehicle_segments = this.initialCourseType ? this.initialCourseType : 0;
    this.mapCenter.lat = this.initialLatitude ? this.initialLatitude : 59.329323;
    this.mapCenter.lng = this.initialLongitude ? this.initialLongitude : 18.068581;
    (async () => {
      await this.getCities();
      await this.getVehicleTypes();
      await this.getVehicleSegments();
      await this.getSchools();
      this.setMeta();
      this.searchCoursesPrices(true);
    })()
  }
}

</script>
