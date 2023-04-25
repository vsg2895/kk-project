<template>
  <div id="demo-app">

    <div class='demo-app-sidebar col-md-4 col-xl-3 pl-0 hide-for-small'>
      <div class="form-group">
        <semantic-dropdown :on-item-selected="schoolChanged" form-name="school_id" :search="true"
                           :readonly="readonly" :initial-item="selectedSchool" placeholder="Välj trafikskola"
                           :data="schools">
          <template slot="dropdown-item" slot-scope="props">
            <div class="item" :data-value="props.item.id">
              <div class="item-text">{{ props.item.name }} ({{ props.item.postal_city }})</div>
            </div>
          </template>
        </semantic-dropdown>
      </div>
      <div class='demo-app-sidebar-section'>
        <div v-if="selectedSchool" class="d-flex flex-column">
          <div v-for="segment in vehicleSegments" class='fc-event' :style='{"background-color": segment.color}' @click="filterEvents(segment.id)">
            <icon v-if="segment.id === curentSegmentId" name="plus"/> {{ segment.label }} ({{ segment.vehicle.label }})
          </div>
        </div>
      </div>

    </div>

    <div class='demo-app-main demo-app-calendar'>
      <div class="hidden-sm-up">
        <semantic-dropdown :on-item-selected="schoolChanged" form-name="school_id" :search="true"
                           :readonly="readonly" :initial-item="selectedSchool" placeholder="Välj trafikskola"
                           :data="schools">
          <template slot="dropdown-item" slot-scope="props">
            <div class="item" :data-value="props.item.id">
              <div class="item-text">{{ props.item.name }} ({{ props.item.postal_city }})</div>
            </div>
          </template>
        </semantic-dropdown>
      </div>

      <FullCalendar class='' :options='calendarOptions'> </FullCalendar>
    </div>

    <div v-show="categoryShow" id="category-modal-calendar" :style="calendarModalStyle" class='demo-app-main modal-calendar'>

      <button type="button" class="close close-categories" data-dismiss="alert" aria-label="Stäng" @click="closeCategorySelect()">
        <span aria-hidden="true">&times;</span>
      </button>

      <div class='demo-app-sidebar'>


        <div class="form-group">
          <semantic-dropdown :on-item-selected="schoolChanged" form-name="school_id" :search="true"
                             :readonly="readonly" :initial-item="selectedSchool" placeholder="Välj trafikskola"
                             :data="schools">
            <template slot="dropdown-item" slot-scope="props">
              <div class="item" :data-value="props.item.id">
                <div class="item-text">{{ props.item.name }} ({{ props.item.postal_city }})</div>
              </div>
            </template>
          </semantic-dropdown>
        </div>

        <template v-if="selectedSchool">
          <div class="d-flex flex-column justify-content-center align-items-center">
            <div v-for="segment in vehicleSegments" class='fc-event' :style='{"background-color": segment.color,"width":"93%"}'
                 @click="filterEvents(segment.id)">
              <icon v-if="segment.id === curentSegmentId" name="plus"/>
              {{ segment.label }} ({{ segment.vehicle.label }})
            </div>
          </div>


        </template>

      </div>

      <button :disabled='!selectedSchool || !curentSegmentId' class="btn btn-success mt-1" @click="segmentSelect()">Skapa</button>

    </div>

    <div v-show="createShow" id="create-modal-calendar" class='demo-app-main modal-calendar' :style="calendarModalStyle">
      <button type="button" class="close" data-dismiss="alert" aria-label="Stäng" @click="closeForm()">
        <span aria-hidden="true">&times;</span>
      </button>

      <form method="POST" action="/organization/courses/store">
        <input type="hidden" name="_token" :value="csrfToken">
        <course-form
            :old-data="[]"
            :course="courseData"
        ></course-form>
        <button class="btn btn-success mb-1" @click="submitForm()">Spara</button>
        <a class="btn btn-primary" :href="infoLink" target="_blank">Kursinformation</a>
        <a v-if="bookingsLink" class="btn btn-primary" :href="bookingsLink" target="_blank">Ladda ner deltagarlista</a>
      </form>
    </div>
  </div>
</template>

<script>
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
// import interactionPlugin, { Draggable } from '@fullcalendar/interaction'
import interactionPlugin from '@fullcalendar/interaction'
import routes from 'build/routes.js';
import Api from "vue-helpers/Api";
import _ from "lodash";
import $ from "jquery";
import moment from 'moment';
import SemanticDropdown from 'vue-components/SemanticDropdown';
import CourseFormSchedule from "vue-pages/courses/FormSchedule";

import Icon from 'vue-components/Icon';

export default {
  name: 'CoursesScheduler',
  components: {
    Icon,
    FullCalendar, // make the <FullCalendar> tag available
    'course-form': CourseFormSchedule,
    'semantic-dropdown': SemanticDropdown,
  },
  props: [
    'schools',
    'initialCourses',
    'csrfToken'
  ],
  data: function() {
    return {
      cellPosition: {},
      createShow: false,
      categoryShow: false,
      calendarOptions: {
        plugins: [
          dayGridPlugin,
          timeGridPlugin,
          interactionPlugin // needed for dateClick
        ],
        eventDidMount: function(info) {

        },
        firstDay: 1,
        slotMinTime: '09:00:00',
        slotMaxTime: '22:00:00',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        axisFormat: 'H(:mm)', //,'h(:mm)tt',
        timeFormat: {
          agenda: 'H(:mm)' //h:mm{ - h:mm}'
        },
        locale: 'en-GB',
        scrollTime: '00:00',
        slotLabelFormat:
            {
              hour: 'numeric',
              minute: '2-digit',
              omitZeroMinute: false,
            },
        initialView: 'timeGridWeek',
        events: [], // alternatively, use the `events` setting to fetch from a feed
        editable: false,
        droppable: false,
        disableDragging: true,
        initialDate: this.closestDate ? this.closestDate.format('YYYY-MM-DD') : moment().format('YYYY-MM-DD'),
        selectAllow: function(select) {
          return moment().diff(select.start) <= 0
        },
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        weekends: true,
        longPressDelay: 0,
        dayClick: this.handleDateSelect,
        select: this.handleDateSelect,
        eventClick: this.handleEventClick,
        eventsSet: this.handleEvents,
        allDaySlot: false,
        displayEventTime: false,
        // eventDragStop: this.eventDragStop,
        eventDrop: this.eventDrop,
        eventContent: this.eventRender,
        eventReceive: this.eventRecive,
        /* you can update a remote database when these fire:
        eventAdd:
        eventChange:
        eventRemove:
        */
      },
      currentEvents: [],
      route: routes.route('organization::courses.store'),
      courseData: this.initialCourses[0],
      selectedDate: null,
      infoLink: '',
      bookingsLink: null,
      selectedSchool: null,
      cities: [],

      vehicleSegments: [],

      curentSegmentId: null,

      events: []
    }
  },
  watch: {
    curentSegmentId(value) {
      this.initialEvents();
    },
    vehicleSegments(value) {
      // $(".fc-event").ready(function() {
      //   $('.fc-event').each(function () {
      //     new Draggable(this, {
      //       eventData: {
      //         title: 'Seats:  X'+ '\n' + ' Price: xxx.00',
      //         duration: '01:00'
      //       }
      //     });
      //   });
      // });
    }
  },
  computed: {
    calendarModalStyle() {
      return {
        top: `${this.cellPosition.top}px`,
        left: `${this.cellPosition.left}px`,
      }
    },
  },
  methods: {
    filterEvents(segmentId) {
      this.curentSegmentId = segmentId;
    },
    submitForm() {

      $("form").submit(function(e){
        e.preventDefault();
      });

      var that = this,
          data = $('form').serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
          }, {});


      _.forEach(this.initialCourses, function (course, key) {

        if (course.id !== that.courseData.id) {
          return;
        }

        data.id = that.courseData.id;
        that.initialCourses[key].available_seats = data.seats;
        that.initialCourses[key].price = data.price;
        that.initialCourses[key].start_time = moment(data.start_time).format('YYYY-MM-DD HH:mm:ss');


      });

      this.initialEvents();

      this.createShow = false;
      this.courseData = {};

      data.start_time = moment(data.start_time).format('YYYY-MM-DD HH:mm');
      Api.updateCourse(data);

    },
    closeForm() {
      this.createShow = false;
      this.courseData = {};
    },
    closeCategorySelect() {
      this.categoryShow = false;
    },
    handleWeekendsToggle() {
      this.calendarOptions.weekends = !this.calendarOptions.weekends // update a property
    },
    segmentSelect() {
      if (!this.curentSegmentId || !this.selectedSchool || !this.selectedDate) {
        return alert('Please choose school and course type first.');
      }

      window.location.href = '/organization/courses/create?courseType=' + this.curentSegmentId +
          '&school=' + this.selectedSchool.id+ '&start=' + this.selectedDate;

      this.categoryShow = false;
      this.createShow = false;
      this.courseData = {};
      this.selectedDate = null;

    },
    handleDateSelect(selectInfo) {
      this.categoryShow = true;
      this.createShow = false;
      this.courseData = {};
      this.selectedDate = moment(selectInfo.startStr).format('YYYY-MM-DD HH:mm');


      // Wait DOM updates before applying position updates
      this.$nextTick(() => {
        const parentEl = document.getElementById('demo-app');
        const parentElOffset = parentEl.getBoundingClientRect();
        const spacing = 120;
        const modalEl = document.getElementById('category-modal-calendar');
        const newLeftPosition = selectInfo.jsEvent.offsetX;
        this.cellPosition = {
          bottom: selectInfo.jsEvent.offsetY,
          left: newLeftPosition,
        };

        // If there is no room on the left side of clicked element, move to the right side of the element
        if (parentEl.offsetWidth < (newLeftPosition + spacing)) {
          this.cellPosition.left = newLeftPosition - parentElOffset.left - modalEl.offsetWidth - spacing;
        }
      });

      let calendarApi = selectInfo.view.calendar;
      calendarApi.unselect(); // clear date selection
    },
    eventDrop: function(event) {
      var data = {},
        that = this;
      _.forEach(this.initialCourses, function (course, key) {

        if (course.id !== parseInt(event.event.id)) {
          return;
        }

        data.id = event.event.id;
        data.seats = that.initialCourses[key].available_seats;
        data.price = that.initialCourses[key].price;
        data.start_time = moment(event.event.start).format('YYYY-MM-DD HH:mm');

        that.initialCourses[key].start_time = moment(event.event.start).format('YYYY-MM-DD HH:mm:ss');

        Api.updateCourse(data);

      });

      this.initialEvents();

      this.createShow = false;
      this.courseData = {};
    },
    eventRender: function(info) {
      let el = document.createElement('p');

      el.innerHTML = "<b>" + info.event.extendedProps.name + "</b><br>" +
          'Booked:' + info.event.extendedProps.booked + '<br> Seats:' + info.event.extendedProps.seats + '<br> Price:' + info.event.extendedProps.price;

      let arrayOfDomNodes = [ el ];
      return { domNodes: arrayOfDomNodes };
    },
    eventRecive(event, delta, revertFunc) {
      // this.createShow = true;
      // this.courseData = {
      //   city_id: this.selectedSchool.city_id,
      //   school: this.selectedSchool,
      //   start_time: event.event.start,
      //   seats: 5,
      //   price: 199.00,
      // }
    },
    handleEventClick(clickInfo) {

      this.createShow = false;
      this.courseData = {};

      // Wait DOM updates before applying position updates
      this.$nextTick(() => {
        const elOffset = clickInfo.el.getBoundingClientRect();
        const parentEl = document.getElementById('demo-app');
        const parentElOffset = parentEl.getBoundingClientRect();
        const spacing = 30;
        const modalEl = document.getElementById('create-modal-calendar');
        const newLeftPosition = elOffset.left - parentElOffset.left + clickInfo.el.clientWidth + spacing;
        this.cellPosition = {
          top: elOffset.top - parentElOffset.top - spacing,
          left: newLeftPosition,
        };

        // If there is no room on the left side of clicked element, move to the right side of the element
        if (parentEl.offsetWidth < (newLeftPosition + modalEl.offsetWidth)) {
          this.cellPosition.left = elOffset.left - parentElOffset.left - modalEl.offsetWidth - spacing;
        }
      });

      this.courseData = _.find(this.initialCourses, (course) => {
        return course.id === parseInt(clickInfo.event.id);
      });

      this.infoLink = this.courseData && this.courseData !== undefined ? '/organization/courses/' + this.courseData.id : '';
      this.bookingsLink = this.courseData && this.courseData !== undefined && this.courseData.bookings.length ? '/organization/courses/download/' + this.courseData.id : null;
      this.createShow = true;
    },
    initialEvents: function () {

      var eventsArray = [],
          that = this;

      var events = [];
      var currentDate = moment();
      var closestDate = this.initialCourses.length ? moment(this.initialCourses[0].start_time) : moment();

      _.forEach(this.initialCourses, function (course) {

        if (that.selectedSchool && course.school_id !== that.selectedSchool.id) {
          return;
        }

        var hours   = Math.floor(course.length_minutes / 60);
        var minutes = Math.floor(course.length_minutes - (hours * 60));

        if (hours   < 10) { hours   = "0"+hours; }
        if (minutes < 10) { minutes = "0"+minutes; }

        var startDate = moment(course.start_time);

        if (currentDate <= startDate && (startDate.diff(currentDate) < closestDate.diff(currentDate))  ) {
          closestDate = startDate
        }

        if (closestDate.diff(currentDate) < 0 && startDate.diff(currentDate) > 0) {
          closestDate = startDate
        }

        eventsArray.push(
            {
              id: course.id,
              title: course.name + '\n Booked:' + course.bookings.length + '\n Seats:' + course.available_seats + "\n Price:" + course.price,
              duration: hours+':'+minutes,
              start: course.start_time,
              end: moment(course.start_time).add(course.length_minutes, 'minutes').format('YYYY-MM-DD HH:mm'),
              allDay: false,
              color: course.segment.color,
              extendedProps: {
                name: course.name,
                booked: course.bookings.length ? course.bookings.filter(booking => {
                    return !booking.cancelled;
                  }).length : 0,
                bookedAll: course.bookings.length,
                seats: course.available_seats,
                price: course.price,
              }
            }
        );
      });

      this.calendarOptions.events = eventsArray;
      this.calendarOptions.initialDate = closestDate.format('YYYY-MM-DD');
    },
    async getCities() {
      this.cities = await Api.getCities();
    },
    async getSchools() {
      if (this.isAdmin) {
        this.schools = await Api.getSchools();
      }

      if (this.schools.length === 1) {
        this.selectedSchool = this.schools[0];
      }
    },
    async getVehicleSegments() {
      if (this.selectedSchool) {
        this.vehicleSegments = await Api.getVehicleSegmentsForSchool(this.selectedSchool.id);
      } else {
        this.vehicleSegments = await Api.getVehicleSegments(true);
      }

      if (!this.isAdmin) {
        var segments = [];
        this.vehicleSegments.forEach((vehicleSegment, i) => {
          if (!vehicleSegment.admin_only) {
            segments.push(vehicleSegment);
          }
        });
        this.vehicleSegments = segments;
      }

      this.selectedVehicleSegment = null;
    },
    schoolChanged(school) {
        this.selectedSchool = school;
        this.getVehicleSegments();
        this.initialEvents();
    },
  },
  created() {
    this.readonly = false;

    this.getCities();
    this.getSchools();
    this.getVehicleSegments();
    this.initialEvents();
  },
}
</script>

<style lang='css'>
h2 {
  margin: 0;
  font-size: 16px;
}
ul {
  margin: 0;
  padding: 0 0 0 1.5em;
}
li {
  margin: 1.5em 0;
  padding: 0;
}
b { /* used for event dates/times */
  margin-right: 3px;
}
#demo-app {
  display: flex;
  position: relative;
  min-height: 100%;
  font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
  font-size: 14px;
}
.demo-app-sidebar-section {
  padding: 2em;
}

@media (min-width: 767px) {
  .demo-app-calendar {
    width: 100%;
    flex-grow: 1;
  }
}

@media (min-width: 200px) and (max-width: 767px) {
  .fc .fc-toolbar-title {
    font-size: 0.75em;
    margin: 0;
    padding-left: 5px;
  }

  .fc-direction-ltr .fc-toolbar > * > :not(:first-child) {
    margin-left: .75em;
    display: none;
    height: 0;
  }

  .fc .fc-toolbar.fc-header-toolbar {
    margin-top: 1.5em;
  }

  .fc-view-harness .fc-view-harness-active {
    height: 1100px!important;
  }

}

.fc-event {
  position: relative;
  display: inline-block;
  padding: 4px;
  font-size: .85em;
  line-height: 1.3;
  border-radius: 3px;
  font-weight: 400;
  margin: 10px 0;
  cursor: pointer;
  color: #fff;
  text-align: center;
  word-break: break-word;
}

@media (min-width: 200px) and (max-width: 767px) {
  .modal-calendar {
    position: absolute;
    z-index: 1; /* Sit on top */
    overflow: auto; /* Enable scroll if needed */
    background-color: white; /* Black w/ opacity */
    box-shadow: 0 24px 38px 3px rgb(0 0 0 / 14%), 0 9px 46px 8px rgb(0 0 0 / 12%), 0 11px 15px -7px rgb(0 0 0 / 20%);
    width: 268px;
    padding-top: 36px;
    padding-left: 22px;
    padding-bottom: 10px;
  }
  .demo-app-sidebar {
    width: 218px!important;
  }
  #price {
    width: 222px;
  }
  #seats {
    width: 222px;
  }

  .close-categories {
    margin-top: -29px;
    margin-right: 5px;
  }
}

@media (min-width: 768px) {
  .modal-calendar {
    position: absolute;
    z-index: 1; /* Sit on top */
    max-width: 640px;
    min-width: 300px;
    overflow: auto; /* Enable scroll if needed */
    background-color: white; /* Black w/ opacity */
    box-shadow: 0 24px 38px 3px rgb(0 0 0 / 14%), 0 9px 46px 8px rgb(0 0 0 / 12%), 0 11px 15px -7px rgb(0 0 0 / 20%);
    padding: 2.5em;
  }

  .close-categories {
    margin: -27px !important;
  }
}
.fc-event-title {
  top: 0;
  bottom: 0;
  max-height: 100%;
  overflow: hidden;
  font-size: 8px;
}

#page-content > div > div > div > div.demo-app-sidebar.hide-for-small > div.form-group > div > div.menu.transition.visible,
#page-content > div > div > div > div:nth-child(3) > div,
#page-content > div > div > div > div:nth-child(3) > div > div > div > div.menu.transition.visible {
  overflow-x: scroll !important;
  width: 100%;
  height: 200px;
}

div.fc-event-main > p  {
  font-size: 13px!important;
}

</style>
