<template>
  <div>
    <h4>Elev / Handledare</h4>
    <div class="course-participants">
      <template v-for="(student, index) in students">
        <participant
          :key="index"
          :index="index"
          :transmissionShow="transmissionShow"
          :showCategories="showCategories"
          type="students"
          :onRemove="onRemoveStudent"
          :transmission="transmission"
          :number-of-participants="students.length"
          @updated="$emit('attendeeUpdated', $event, index, 'students')" />
      </template>
      <button class="btn btn-sm btn-outline-primary add-student-btn-coursepage" @click="onAddStudent('students')">
        Lägg till fler deltagare
        <icon name="plus"></icon>
      </button>
    </div>
  </div>
</template>

<script>
import Participant from './Participant';
import Icon from 'vue-components/Icon';

export default {
  components: { Participant, Icon },
  props: ['students', 'onRemoveStudent', 'onAddStudent', 'courseId', 'transmissionShow', 'transmission', 'showCategories'],
  methods: {
    onRemove() {
      this.$emit('removeAttendee', 'student')
    }
  },
}
</script>
