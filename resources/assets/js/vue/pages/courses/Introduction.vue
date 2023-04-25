<template>
  <div>
    <h4>Elev / Handledare </h4>
    <div class="course-participants">
      <participant
        v-for="(tutor, index) in tutors"
        :key="index"
        :index="index"
        type="tutors"
        :data="tutor"
        :onRemove="onRemoveTutor"
        :number-of-participants="students.length + tutors.length"
        @updated="$emit('attendeeUpdated', $event, index, 'tutors')" />
      <button class="btn btn-sm btn-outline-primary add-tutor-btn-coursepage" @click="onAddTutor('tutors')">
        Lägg till
        <template v-if="tutors.length">fler</template>
        Elev / Handledare
        <icon name="plus"></icon>
      </button>
    </div>
    <hr>
    <h4>Elev / Handledare</h4>
    <div class="course-participants">
      <participant
        v-for="(student, index) in students"
        :key="index"
        :index="index"
        type="students"
        :transmissionShow="transmissionShow"
        :data="student"
        :onRemove="onRemoveStudent"
        :number-of-participants="students.length + tutors.length"
        @updated="$emit('attendeeUpdated', $event, index, 'students')" />
      <button class="btn btn-sm btn-outline-primary add-student-btn-coursepage" @click="onAddStudent('students')">
        Lägg till fler
        <template v-if="students.length">/</template>
        <icon name="plus"></icon>
      </button>
    </div>
  </div>
</template>

<script>
import Icon from 'vue-components/Icon';
import Participant from './Participant';

export default {
  components: { Participant, Icon },
  props: ['students', 'tutors', 'onRemoveStudent', 'onAddStudent', 'onRemoveTutor', 'onAddTutor', 'courseId', 'transmissionShow'],
}
</script>