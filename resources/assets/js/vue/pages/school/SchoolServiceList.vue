<template>
  <div class="row">
    <div class="container no-padding-on-mobile">
      <div v-for="(block, index) in segmentBlocks" :key="index + Math.floor(Math.random() * 99999)">
        <template v-if="block.hasLenght">
          <div v-if="!iframe" class="accordion-container segment-name col-xs-12" @click="coursesData[block.type] = !coursesData[block.type]">
            <h2 class="accordion-title">{{ block.title }}</h2>
            <span class="accordion-arrow fas fa-chevron-down" :class="{ 'accordion-rotate': coursesData[block.type] }" />
          </div>
          <template v-if="(coursesData[block.type]) || (tabList.length > 0 && iframe && courseShow)">
            <h3 class="segment-name-product col-xs-11">Kurser</h3>
            <div
              v-for="segment in block.segments" :key="segment.id"
              class="segment-result-object col-xs-12" :id="'scroll-'+segment.id"
              :class="{'iframe-segment-result-object' : iframe}"
              v-if="!iframe || iframe && !segment.hide">
              <h4 class="school-card-type-name">{{segment.label}}</h4>
              <p v-if="segment.description && (calendarSegmentId !== ONLINE_LICENSE_THEORY)"  class="school-info-description">
                <strong v-if="changeDescription.description !== '' && segment.label === changeDescription.label">{{ changeDescription.description }}</strong>
                <strong v-else>{{ segment.description }}</strong>
              </p>
              <div v-if="calendarSegmentId !== segment.id" class="btn-school-checkout">
                <button @click="openCalendar(segment)" class="btn btn-success btn-sm">
                  Boka från {{ priceCoursesById(segment.id) }}kr
                </button>
              </div>
              <template v-if="showCalendar && calendarSegmentId === segment.id">
                <vl-day-selector
                  v-if="calendarSegmentId !== ONLINE_LICENSE_THEORY"
                  v-model="date"
                  @changeDesc="setDescription"
                  :courses="calendarSegmentCourses"
                  :default-date="startDate"
                  :single-month="true"
                  showClose
                  @close="closeCalendar" />
                <school-courses-list
                  :courses="segmentCourses"
                  :school="school"
                  :show-date="date"
                  :students="students"
                  :selectedCourses="selectedCourses"
                  :iframe="iframe"
                  @toggleCourse="toggleCourse"
                  @addCourse="addCourse"
                  @addAttendee="addAttendee"
                  @removeAttendee="removeAttendee"
                  class="mt-1" />
              </template>
            </div>
          </template>
        </template>
      </div>

      <template v-if="(tabList.length > 0 && !iframe) || (tabList.length > 0 && iframe && paketShow)">
        <h3 class="segment-name-product col-xs-12 pl-1">Paket</h3>
        <div
          v-for="item in tabList"
          :key="item.id"
          :class="{ 'course-item-list active': item.top_deal, 'iframe-segment-result-object' : iframe}"
          class="segment-result-object col-xs-12">
          <h3 class="school-card-type-name">{{ item.name }}</h3>
          <div v-if="item.top_deal" class="top-deal">
            <i class="fa fa-bookmark"></i>
            <i class="fa fa-star"></i>
            <span class="top-deal-text">Top deal</span>
          </div>

          <template v-if="item.description">
            <p class="school-info-description">
              {{ item.description }}
            </p>
            <span v-if="item.show_left_seats" class="attention">Endast {{ item.left_seats }} kvar!</span>
          </template>

          <div class="btn-school-checkout">
            <button
              @click="toggleAddon(item)"
              :class="buttonClass(item)"
              class="btn btn-sm">
                {{ buttonText(item) }}
            </button>
          </div>
        </div>
      </template>
    </div>

    <div v-if="showCalendar" class="container hidden">
      <div class="row">
        <div v-if="calendarCourseType.id !== ONLINE_LICENSE_THEORY" class="segment-result-object-calendar col-xs-12 col-lg-5 clearfix">
          <vl-day-selector
            v-model="date"
            :courses="calendarSegmentCourses"
            :default-date="startDate"
            :disabled-dates="{ to: '2021-01-03' }"
            :single-month="true"
            showClose
            @close="closeCalendar" />
          <span v-html="calendarCourseType && (calendarCourseType.calendar_description && (!date || calendarCourseType.id !== ONLINE_LICENSE_THEORY)) ? calendarCourseType.calendar_description : segmentCourses && segmentCourses[0] ? segmentCourses[0].description : ''">
          </span>
        </div>
        <div
          :class="calendarCourseType.id === ONLINE_LICENSE_THEORY ? 'col-lg-12 col-xl-12' : 'col-lg-7 col-xl-7'"
          class="col-xs-12 text-md-right match-height-item result-object-buttons p-0">
          <span class="school-calendar-title">{{ calendarCourseType.label }}</span>
          <school-courses-list
            :courses="segmentCourses"
            :school="school"
            :show-date="date"
            :students="this.students"
            :selectedCourses="selectedCourses"
            :iframe="iframe"
            @toggleCourse="toggleCourse"
            @addAttendee="addAttendee"
            @removeAttendee="removeAttendee" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
    import { get, min, sortBy } from 'lodash';
    import Icon from 'vue-components/Icon';
    import moment from "moment";
    import routes from 'build/routes';
    import SchoolCoursesList from './SchoolCoursesList';
    import SchoolPriceTable from './SchoolPriceTable';
    import SemanticDropdown from 'vue-components/SemanticDropdown';
    import VlDaySelector from 'vue-components/VlDaySelector';

    const DEFAULT_TIME_FORMAT = 'YYYY-MM-DD';
    const TYPE_B = 'B';
    const TYPE_A = 'A';
    const TYPE_AM = 'AM';
    const TYPE_YKB = 'YKB';
    const ONLINE_INTRIDUKTIONSUTBILDING_ADMIN_ID = 25;
    const ONLINE_INTRIDUKTIONSUTBILDING_ID = 19;
    const ONLINE_COURSE_IDS = [ONLINE_INTRIDUKTIONSUTBILDING_ID, ONLINE_INTRIDUKTIONSUTBILDING_ADMIN_ID];

    export default {
        props: [
            'currentTab',
            'tabList',
            'selectedAddons',
            'school',
            'vehicles',
            'vehicleSegments',
            'schoolSegmentSorting',
            'courses',
            'school',
            'selectedCourses',
            'students',
            'iframe',
            'paketShow',
            'courseShow'
        ],
        data() {
            return {
                date: null,
                showCalendar: false,
                calendarSegmentId: null,
                calendarCourseType: null,
                changeDescription: {
                  description:'',
                  label:'',
                },
                coursesData: {
                  [TYPE_B]: true,
                  [TYPE_A]: true,
                  [TYPE_AM]: true,
                  [TYPE_YKB]: true,
                },
                selectedShowDescription: [],
                routes
            }
        },
        components: {
          Icon,
          SchoolCoursesList,
          SchoolPriceTable,
          SemanticDropdown,
          VlDaySelector,
        },
        computed: {
          segmentBlocks() {
            let segmentBlocks = [{
                type: TYPE_B,
                title: 'Bil / B Körkort',
                hasLenght: !!this.bCourses.length,
                segments: this.bCourses,
              }, {
                type: TYPE_A,
                title: 'Motorcykel / A Körkort',
                hasLenght: !!this.aCourses.length,
                segments: this.aCourses,
              }, {
                type: TYPE_AM,
                title: 'Motorcykel / AM Körkort',
                hasLenght: !!this.amCourses.length,
                segments: this.amCourses,
              }, {
                type: TYPE_YKB,
                title: 'YKB Förarutbildning',
                hasLenght: !!this.ykbCourses.length,
                segments: this.ykbCourses,
              }]
            const currentSegmentBlocks = segmentBlocks.filter(segmentBlock => {
              return segmentBlock.hasLenght;
            });

            if (this.iframe) {
              this.$emit('segmentBlocks', currentSegmentBlocks)
            }
            return segmentBlocks;
           },
          bCourses() {
            const segments = this.vehicleSegments.filter(vehicleSegment => {
              const segmentHasCourses = this.courses.some(course => {
                if (course.segment === ONLINE_INTRIDUKTIONSUTBILDING_ADMIN_ID) {
                  return vehicleSegment.id === ONLINE_INTRIDUKTIONSUTBILDING_ID;
                }
                return course.segment === vehicleSegment.id;
              });
              return vehicleSegment.vehicle.identifier == TYPE_B && vehicleSegment.bookable && segmentHasCourses;
            });
            var sorted = [];
            if (this.schoolSegmentSorting[1] && this.schoolSegmentSorting[1] !== undefined && this.schoolSegmentSorting[1][0].sort_order) {
              var items = segments,
                  segmentSorting = this.schoolSegmentSorting[1],
                  asArray = Object.keys(this.schoolSegmentSorting[1]);
                  var array = sortBy(asArray, function(asArray) {
                    return asArray.sort_order;
                  });
              array.forEach(function (key) {
                var found = false;
                items = items.filter(function(item) {
                  if(!found && item.id === segmentSorting[key].vehicle_segment_id) {
                    sorted.push(item);
                    found = true;
                    return false;
                  } else
                    return true;
                })
              });
              return sorted;
            }
            //Digital Introduktionskurs - 19, Digital Introduction Course English - 21, Introduktionskurs - 7,
            //Introduktionskurs English- 30,
            //Körlektioner - 16, Riskettan MC - 10, RISK_TWO_MC - 11, Körkortsteori och Testprov - 32
            const sortingOrder = [19, 21, 7, 30, 16, 10, 11];
            const sortedSegments = segments.slice().sort((a, b) => {
              if(sortingOrder.includes(b.id)) return 1;
              if(sortingOrder.includes(a.id)) return -1;
              return 0;
            });

            return sorted.concat(sortedSegments);
          },
          aCourses() {
            const segments = this.vehicleSegments.filter(vehicleSegment => {
              const segmentHasCourses = this.courses.some(course => course.segment === vehicleSegment.id);
              return vehicleSegment.vehicle.identifier == TYPE_A && vehicleSegment.bookable && segmentHasCourses;
            });
            var sorted = [];
            if (this.schoolSegmentSorting[2] && this.schoolSegmentSorting[2] !== undefined && this.schoolSegmentSorting[2][0].sort_order) {
              var items = segments,
                  segmentSorting = this.schoolSegmentSorting[2],
                  asArray = Object.keys(this.schoolSegmentSorting[2]);
              var array = sortBy(asArray, function(asArray) {
                return asArray.sort_order;
              });
              array.forEach(function (key) {
                var found = false;
                items = items.filter(function(item) {
                  if(!found && item.id === segmentSorting[key].vehicle_segment_id) {
                    sorted.push(item);
                    found = true;
                    return false;
                  } else
                    return true;
                })
              });
            }
            return sorted.length ? sorted : segments;
          },
          amCourses() {
            const segments = this.vehicleSegments.filter(vehicleSegment => {
              const segmentHasCourses = this.courses.some(course => course.segment === vehicleSegment.id);
              return vehicleSegment.vehicle.identifier == TYPE_AM && vehicleSegment.bookable && segmentHasCourses;
            });
            var sorted = [];
            if (this.schoolSegmentSorting[3] && this.schoolSegmentSorting[3] !== undefined && this.schoolSegmentSorting[3][0].sort_order) {
              var items = segments,
                  segmentSorting = this.schoolSegmentSorting[3],
                  asArray = Object.keys(this.schoolSegmentSorting[3]);
              var array = sortBy(asArray, function(asArray) {
                return asArray.sort_order;
              });
              array.forEach(function (key) {
                var found = false;
                items = items.filter(function(item) {
                  if(!found && item.id === segmentSorting[key].vehicle_segment_id) {
                    sorted.push(item);
                    found = true;
                    return false;
                  } else
                    return true;
                })
              });
            }
            return sorted.length ? sorted : segments;
          },
          ykbCourses() {
            const segments = this.vehicleSegments.filter(vehicleSegment => {
              const segmentHasCourses = this.courses.some(course => course.segment === vehicleSegment.id);
              return vehicleSegment.vehicle.identifier == TYPE_YKB && vehicleSegment.bookable && segmentHasCourses;
            });
            var sorted = [];

            if (this.schoolSegmentSorting[4] && this.schoolSegmentSorting[4] !== undefined && this.schoolSegmentSorting[4][0].sort_order) {
              var items = segments,
                  segmentSorting = this.schoolSegmentSorting[4],
                  asArray = Object.keys(this.schoolSegmentSorting[4]);
              var array = _.sortBy(asArray, function(asArray) {
                return asArray.sort_order;
              });

              array.forEach(function (key) {
                var found = false;
                items = items.filter(function(item) {
                  if(!found && item.id === segmentSorting[key].vehicle_segment_id) {
                    sorted.push(item);
                    found = true;
                    return false;
                  } else
                    return true;
                })
              });
            }

            return sorted.length ? sorted : segments;
          },
          calendarSegmentCourses() {
            return this.courses.filter(course => {
              if (this.calendarSegmentId === ONLINE_INTRIDUKTIONSUTBILDING_ID) {
                return course.segment === this.calendarSegmentId || course.segment === ONLINE_INTRIDUKTIONSUTBILDING_ADMIN_ID;
              }
              return course.segment === this.calendarSegmentId;
            });
          },
          segmentCourses() {
            if (!this.date) return this.calendarSegmentCourses;
            return this.calendarSegmentCourses.filter(course => course.course.start_time.indexOf(this.date) + 1);
          },
          startDate() {
            return this.calendarSegmentCourses.length ?
              moment(this.calendarSegmentCourses[0].course.start_time).format(DEFAULT_TIME_FORMAT) : 
              moment().format(DEFAULT_TIME_FORMAT);
          }
        },
        methods: {
          setDescription(value){
            this.changeDescription.description = value.description;
            this.changeDescription.label = value.label;
          },
          priceCoursesById(id) {
            const prices = this.courses.reduce((acc, course) => {
              if (ONLINE_COURSE_IDS.includes(id) || ONLINE_COURSE_IDS.includes(course.segment) || course.segment === id) {
                acc.push(course.price);
              }
              return acc;
            }, []);
            return min(prices);
          },
          toggleAddon(addon) {
              if (!addon.left_seats && addon.show_left_seats) {
                  return
              }
              this.$emit('toggleAddon', addon);
          },
          toggleShowDescription(item) {
              if (this.selectedShowDescription.includes(item)) {
                let index = this.selectedShowDescription.indexOf(item);
                if (index > -1) {
                  this.selectedShowDescription.splice(index, 1);
                }
              } else {
                this.selectedShowDescription.push(item);
              }
              this.$emit('toggleShowDescription', item);
          },
          showDescription(item) {
            return this.selectedShowDescription.includes(item);
          },
          addAttendee(course) {
              this.$emit('addAttendee', course)
          },
          removeAttendee(course) {
              this.$emit('removeAttendee', course)
          },
          addCourse(course) {
            this.$emit('addCourse', course);
          },
          toggleCourse(course) {
              this.$emit('toggleCourse', course);
          },
          openCalendar(segment) {
            this.showCalendar = true;
            this.calendarSegmentId = get(segment, 'id');
            this.calendarCourseType = segment;
          },
          closeCalendar() {
            this.showCalendar = false;
            this.calendarSegmentId = null;
            this.calendarCourseType = null;
          },
          isAddonSelected(addon) {
            return this.selectedAddons.some(it => it.id === addon.id);
          },
          buttonText(addon) {
            return this.isAddonSelected(addon) ? 'Ta bort' : `Boka ${addon.price}kr`;
          },
          buttonClass(addon) {
            let result = this.isAddonSelected(addon) ? 'btn-danger' : 'btn-success';
            result += !addon.left_seats && addon.show_left_seats ? ' disabled' : '';
            return result;
          }
        },
      mounted() {
          //open segment automatically
          let urlParams = new URLSearchParams(window.location.search);
          let selectedSegmentId = urlParams.get('open');
          let segmentToOpen = this.vehicleSegments.filter(vehicleSegment => {
            if (vehicleSegment.id) {
              return vehicleSegment.id == selectedSegmentId;
            }
            return false;
          });
          if (segmentToOpen[0]) {
            this.openCalendar(segmentToOpen[0]);
          }
          //scroll to segment
          document.getElementById("scroll-" + selectedSegmentId).scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }
</script>

<style lang="scss" type="text/scss">
.accordion-container {
  font-family: "Roboto";
  position: relative;
  display: block;
  padding: 1.25rem 1.5rem;
  margin-bottom: 2px;
  color: #fff;
  font-size: 28px;
  text-decoration: none;
  background-color: black;
  border-radius: 5px;
  -webkit-transition: background-color 0.2s;
  transition: background-color 0.2s;
  cursor: pointer;

  &:hover {
    background-color: black;
    transition: all 0.5s ease-out;
  }

  @media (max-width: 767px) { // sm and down
    width: calc(100% - 1rem);
    padding: 1rem 1rem 1rem 2rem;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;

    .accordion-arrow {
      top: 1rem;
      right: 1rem;
    }    
  }
}
.accordion-title {
  font-family: "Roboto";
  font-weight: 700;
}

.accordion-arrow {
  position: absolute;
  top: 1.25rem;
  right: 1.5rem;
  text-align: center;
  color: #fff;
  line-height: 1rem;
  font-size: 1rem;
  -webkit-transition: all 0.2s ease-out;
  transition: all 0.2s ease-out;

  &.accordion-rotate {
    transform: rotate(180deg);
  }
}

.accordion_content {
  padding: 30px;
  margin-bottom: 2px;
  font-size: 18px;
  display: none;
  background-color: black;
}
.accordion_content {
  word-wrap: break-word;
  color: white;
  border-radius: 5px;
  margin-top: -15px;
}
</style>
