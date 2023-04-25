<template>
    <div>
        <div class="search-result-header">
            <div class="search-result-sort d-flex align-items-center justify-content-end">
              <semantic-dropdown
                size="sm"
                value-field="name"
                align-dropdown="right"
                :floating="true"
                :initial-item="sort" placeholder="Sortera efter" :on-item-selected="sortChanged"
                :data="availableVehicleSegments"
                class="d-flex align-items-center">
                  <template slot="custom-dropdown-items">
                      <div class="item" data-value="COMPARISON">
                          <div class="item-text">Jämförpris</div>
                      </div>
                      <div class="item" data-value="DRIVING_LESSON">
                          <div class="item-text">Timpris körlektion</div>
                      </div>
                  </template>
                  <template slot="dropdown-item" slot-scope="props">
                      <div class="item" :data-value="props.item.name">
                          <div class="item-text">Pris <span v-text="props.item.label"></span></div>
                      </div>
                  </template>
              </semantic-dropdown>
            </div>

            <div class="hide-for-small">
                <div v-if="schoolCount">
                    <h4>Hittade <span v-text="schoolCount"></span> trafikskolor<span
                            v-if="selectedCity"> i <span v-text="selectedCity.name"></span></span><i
                            class="text-warning"
                            v-show="isMemberFilterEnabled"> med
                        bokningsbara kurser</i><span> utifrån valda filter</span></h4>
                </div>
                <div class="search-results-none" v-else-if="!schoolCount && noResult">
                    <h4>Hittade inga trafikskolor utifrån din filtrering.
                        <icon name="smiley2"/>
                    </h4>
                    <p>Justera filter eller flytta på kartan</p>
                </div>
                <div class="search-results-none" v-else>
                    <h4>Vänligen vänta, resultat laddas.</h4>
                    <p>
                        <icon name="smiley5"/>
                    </p>
                </div>
            </div>
        </div>

        <div class="search-result-list">

          <div @mouseover="mouseOverForSchool(true, school)" @mouseout="mouseOverForSchool(false, school)"
            class="search-result-object"
            v-for="(school, index) in schools"
            :key="index"
            :class="[`school-${school.id}`, school.top_partner ? 'top-partner-object' : '']">
            <div class="school-logo-container">
              <img
                v-if="school.logo"
                :src="school.logo"
                class="school-logo"
                alt="Logo">
                <icon class="hidden-md-up mb-1" name="gift-red"></icon>
            </div>
            <div class="school-name-container">
              <h3 class="school-name">
                <a :href="getSchoolRoute(school)">{{ school.name }}</a>
              </h3>
              <div>{{ school.address + ((school.address).includes(school.city.name) ? '' : ', ' + school.city.name) }}</div>

              <div v-if="avgRating(school)" class="mt-1">
                <stars :rating="avgRating(school)"></stars>
                <span class="d-flex align-items-center">
                  <span class="search-avg">{{ avgRating(school) }}/5</span>
                  <span class="search-qty">i betyg baserat på {{ school.ratings.length}} recensioner</span>
                </span>
              </div>
            </div>

            <div class="top-partner-container">
              <div class="top-partner-child">
                <div v-title="'Den här trafikskolan är kvalitetssäkrad som <br> samarbetspartner, vilket innebär att <br> trafikskolan rekommenderas starkt av både <br> elever och oss på Körkortsjakten. Trafikskolan gör allt för att ge sina elever en enastående <br> upplevelse tack vare dess utmärka service <br> och utbud.'">
                  <i v-if="school.top_partner"  class="fa fa-medal fa-2x top-partner-medal"></i>
                </div>
                <p v-if="school.top_partner"> Top Partner</p>
              </div>
            </div>

            <div class="btn-container">
              <icon class="hidden-md-down" name="gift-red"></icon>
              <a v-if="inPaketPage" class="btn btn-success btn-rounded btn-100" :href="getSchoolRoute(school)">
                Boka Körkortspaket
              </a>
              <a v-else class="btn btn-success btn-rounded btn-100" :href="getSchoolRoute(school)">
                Boka&nbsp;<span v-if="Math.trunc(school.course_min_price) > 0">kurser från {{ Math.trunc(school.course_min_price) }} kr</span>
              </a>

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
    import SemanticDropdown from 'vue-components/SemanticDropdown';
    import routes from 'build/routes.js';
    import Pagination from 'vue-helpers/Pagination';
    import Icon from 'vue-components/Icon';
    import {mapActions, mapGetters} from "vuex";
    import RecoComparisonWidget from "vue-components/RecoComparisonWidget";
    import Stars from 'vue-components/Stars';

    export default {
        props: [
            'schools',
            'schoolCount',
            'schoolAll',
            'comparedSchools',
            'availableVehicleSegments',
            'sort',
            'sortChanged',
            'compareToggle',
            'schoolForSegment',
            'isMemberToggle',
            'isMemberFilterEnabled',
            'selectedCity',
            'selectedVehicle',
            'schoolBestDeals',
            'noResult',
            'inPaketPage',
        ],
        components: {
            'smileys': Smileys,
            'semantic-dropdown': SemanticDropdown,
            Icon,
            'reco-comparison': RecoComparisonWidget,
            Stars,
        },
        data() {
            return {
                routes: routes,
                schoolVehicleSegment: {
                    segment: null,
                    school: null
                },
                best: 0
            };
        },
        filters: {
            formatDate: function (value, format) {
                return moment(value).format(format);
            }
        },
        methods: {
            ...mapActions('config', ['setLessonsCount']),
            mouseOverForSchool(isMouseOver, school) {
                var marker = $('.map-marker[data-marker_id=' + school.id + ']')
                if (isMouseOver) {
                    marker.addClass('hover')
                } else {
                    marker.removeClass('hover')
                }
            },
            getCourseMinPrice(school){
              if (!school.upcoming_courses.length) {
                return false;
              }

              var minPrice = 0;

              school.upcoming_courses.forEach(function (element) {
                if ((element.price < minPrice || !minPrice) && element.seats > 0) {
                  minPrice = element.price;
                }
              });

              return minPrice;

            },
            isBestDeal(school) {
              if (!this.schoolBestDeals) {
                return false;
              }

              var bestDeal = false;

              this.schoolBestDeals.forEach(function (item) {
                  if (item.id === school.id ) {
                    bestDeal = true;
                  }
              });

              return bestDeal;
            },
            avgRating(school) {
              const { ratings } = school;
              if (!ratings) {
                return 0;
              }

              const sum = ratings.map(({ rating }) => rating).reduce((total, currentValue) => total += currentValue, 0)
              const average = parseFloat((sum / ratings.length).toFixed(1));

              return Number.isInteger(average) ? parseInt(average) : average;
            },
            forPage(page, data) {
              return Pagination.dataForPage(page, data);
            },
            toSchoolPage: function (school, page) {
                this.$set(school, 'coursePage', page);
            },
            getSchoolRoute: function (school) {
                return routes.route('shared::schools.show', {citySlug: school.city.slug, schoolSlug: school.slug});
            },
            notInComparison(id) {
                return !_.includes(this.comparedSchools.map((s) => s.id), id);
            },
            schoolHasIncompletePrices(school) {
                let vm = this;
                let missing = _.filter(school.formatted_prices, (price) => {
                    if (price.label === "Teorilektion" && price.vehicle_id === vm.selectedVehicle && price.price == null) {
                        return false;
                    }
                    return price.vehicle_id === vm.selectedVehicle && price.price == null;
                });

                return missing.length;

            },
            rotateBestDeals() {
              var vm = this;
                  if (!vm.schoolBestDeals) {
                    return;
                  }
                  vm.schoolBestDeals.length - 1 === vm.best ? vm.best = 0 : vm.best++;
            },
            nextBest() {
              var vm = this;
              if (!vm.schoolBestDeals) {
                return;
              }
              vm.schoolBestDeals.length - 1 === vm.best ? vm.best = 0 : vm.best++;
            },
            prevBest() {
              var vm = this;
              if (!vm.schoolBestDeals) {
                return;
              }
              vm.best - 1 >= 0 ? vm.best-- : vm.best = vm.schoolBestDeals.length - 1;
            }
        },
        computed: {
            ...mapGetters('config', ['getLessonsCount'])
        },
        mounted() {
            var vm = this;
            this.setLessonsCount();
            setInterval(function() {
              vm.rotateBestDeals();
            },10000);
        },
        destroyed() {
        }
    }
</script>

<style lang="scss" type="text/scss">

</style>
