<template>
  <div class="courses-calendar-segment-block">
    <div class="no-courses" v-if="!courses.length">
      <p v-text="`Trafikskolan har ingen schema för bokningar för detta datum.`"></p>
      <a v-if="school.city && !this.iframe" class="btn btn-outline-success"
          :href="routes.route('shared::search.schools', {citySlug: school.city.slug})"
          v-text="`Sök efter kurser bland andra trafikskolor`">
      </a>
    </div>
    <template v-else>
      <!-- course appointment -->
      <school-course
        v-for="course in paginatedCourses"
        :key="course.id"
        :course="course"
        :students="students"
        :is-selected="isCourseSelected(course)"
        @toggleCourse="toggleCourse"
        @addCourse="addCourse"
        class="mt-1" />
      <div class="paginator-container" v-if="courses.length > coursesPerPage">
        <paginator
          :total-pages="totalPages"
          :currentPage="currentPage"
          @prev="prev"
          @page="page"
          @next="next" />
      </div>
    </template>
  </div>
</template>

<script>
import routes from 'build/routes';
import Icon from 'vue-components/Icon';
import Paginator from 'vue-components/Paginator';
import SchoolCourse from './SchoolCourse';

export default {
  props: ['courses', 'school', 'selectedCourses', 'showDate', 'students', 'iframe'],
  components: { Icon, Paginator, SchoolCourse },
  data() {
    return {
      routes,
      currentPage: 1,
      coursesPerPage: 5,
    }
  },
  computed: {
    paginatedCourses() {
      let paginatedCourses = [];
      if (this.courses.length <= this.coursesPerPage) {
        paginatedCourses = this.courses;
      } else {
        let start = (this.currentPage - 1) * this.coursesPerPage;
        let end = start + this.coursesPerPage;
        paginatedCourses = this.courses.slice(start, end);
      }
      return paginatedCourses;
    },
    totalPages() {
      return this.courses.length ? Math.ceil(this.courses.length / this.coursesPerPage) : 0;
    },
  },
  methods: {
    isCourseSelected(course) {
      return this.selectedCourses.some(it => it.id === course.id);
    },
    buttonClass(course) {
      return this.isCourseSelected(course) ? 'btn-danger' : 'btn-success'
    },
    buttonText(course) {
      return this.isCourseSelected(course) ? 'Ta bort' : `${course.course.price_with_currency} Boka`;
    },
    addCourse(course) {
      this.$emit('addCourse', course);
    },
    toggleCourse(course) {
      this.$emit('toggleCourse', course);
    },
    countParticipants(course) {
      return this.students && this.students.length ?
          this.students.filter(attendee => {
            return attendee.courseId === course.id;
          }).length : 0;
    },
    addAttendee(course) {
      if (this.countParticipants(course) === 0 ) {
        this.toggleCourse(course);
      } else {
        this.$emit('addAttendee', course);
      }
    },
    removeAttendee(course) {
      if (this.countParticipants(course) === 1 ) {
        this.toggleCourse(course);
      }

      this.$emit('removeAttendee', course)
    },
    maybePlural(integer, one, many) {
      return integer > 1 ?
          many : one;
    },
    next() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
      }
    },
    page(nr) {
      this.currentPage = nr;
    },
    prev() {
      if (this.currentPage > 1) {
        this.currentPage--
      }
    },
  }
}
</script>
