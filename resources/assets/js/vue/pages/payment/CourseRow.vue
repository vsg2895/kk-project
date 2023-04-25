<template>
    <div class="card course-card card-block text-left mx-0" :class="{'iframe-payment-page-block': this.iframe}">
      <button v-if="deletable" class="btn btn-sm float-xs-right btn-danger" :class="{'iframe-payment-page-block-del-button': this.iframe}" @click="remove">Ta bort</button>
      <div class="type tag tag-pill tag-default">{{ course.segment.label }} <span v-if="course.part && course.part.length">{{ course.part }}</span></div>
        <div v-if="course.vehicle_segment_id !== ONLINE_LICENSE_THEORY" class="date" >{{ courseStartDate(course.start_time) }}</div>
        <div v-if="course.vehicle_segment_id !== ONLINE_LICENSE_THEORY" class="time text-numerical">{{ course.start_hour }} - {{ course.end_hour }}</div>
        <div v-if="course.vehicle_segment_id !== ONLINE_LICENSE_THEORY" >Tillgängliga platser: {{ course.available_seats }}</div>
        <p v-if="course.description">{{ course.description }}</p>
        <div>
          <introduction v-if="isIntroduktionskurs"
            :transmissionShow="false"
            :students="students"
            :tutors="tutors"
            :onAddStudent="addAttendee"
            :onRemoveStudent="removeAttendee"
            :onAddTutor="addAttendee"
            :onRemoveTutor="removeAttendee"
            @attendeeUpdated="handleAttendeeUpdated(...arguments)" />
          <risk-one v-else
            :students="students"
            :showCategories="isRisktvaanMC"
            :transmissionShow="isKorlektioner"
            :transmission="course.transmission"
            :options="options"
            :onAddStudent="addAttendee"
            :onRemoveStudent="removeAttendee"
            @attendeeUpdated="handleAttendeeUpdated(...arguments)" />
        </div>
    </div>
</template>
<script>
    import moment from 'moment';
    import Introduction from 'vue-pages/courses/Introduction';
    import RiskOne from 'vue-pages/courses/RiskOne';

    export default {
        props: {
            course: Object,
            order: Object,
            deletable: Boolean,
            iframe:Boolean
        },
        data() {
          return {
            isEdited: false,
            options: ['A1', 'A2', 'A3']
          }
        },
        components: {
            Introduction,
            RiskOne
        },
        computed: {
          isIntroduktionskurs () {
            return this.course.segment && this.course.segment.id === 7;
          },
          isKorlektioner() {
            return this.course.segment && this.course.segment.id === 16;
          },
          isRisktvaanMC() {
            return this.course.segment && this.course.segment.id === 11;
          },
            students() {
                return this.order.students.filter(student => {
                  return student.courseId === this.course.id
                });
            },
            tutors() {
                return this.order.tutors.filter(tutor => {
                  return tutor.courseId === this.course.id
                });
            }
        },
        methods: {
            courseStartDate(rawDate) {
                let date = moment(rawDate);
                if (date) {
                    return date.format('dddd [den] D MMMM')
                } else {
                    return ''
                }
            },
            remove() {
                this.$emit('remove');
            },
            handleAttendeeUpdated(attendee, index, type) {
              this.$emit('attendeeUpdated', { ...attendee, courseId: this.course.id }, index, type)
            },
            addAttendee(target = false) {
                if (this.course.seats <= this.order[target].length) {
                  return;
                }

                this.order[target].push({
                    given_name: '',
                    family_name: '',
                    social_security_number: '',
                    email: '',
                    transmission: target ? '' : '',
                    courseId: this.course.id,
                })
              
              const courseStudents = this.order[target].filter(i => i.courseId === this.course.id);
              const params = new URLSearchParams(window.location.search);
              params.set(this.course.id, courseStudents.length);
              window.history.replaceState({}, '', decodeURIComponent(`${window.location.pathname}?${params}`));
            },
            removeAttendee(type, i) {
              this.edited = true;
              var arrayOfItems = type === 'students' ? this.order.students : this.order.tutors;

              if (!arrayOfItems.length) {
                return;
              }

              let courseStudents = this.order.students.filter(student => {
                return student.courseId === arrayOfItems[i].courseId
              })

              let courseTutors = this.order.tutors.filter(tutor => {
                return tutor.courseId === arrayOfItems[i].courseId
              })

              if ((courseStudents.length > 1 || courseTutors.length > 1) || (courseStudents.length === 1 && courseTutors.length === 1)) {
                type === 'students' ? this.order.students.splice(i, 1) : this.order.tutors.splice(i, 1);
              }

              if (courseStudents.length > 1) {
                const params = new URLSearchParams(window.location.search);
                params.set(this.course.id, courseStudents.length - 1);
                window.history.replaceState({}, '', decodeURIComponent(`${window.location.pathname}?${params}`));
              }
            },
            mounted() {
                this.addAttendee(true);
                this.addAttendee(false);
            }
        }
    }
</script>
