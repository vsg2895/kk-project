<template>
  <vl-calendar
    @input="date => emitDate(date)"
    :is-selected="date => date === selectedDate"
    :is-disabled="calculateDisabled"
    :courses="courses"
    :custom-classes="customClasses"
    :show-weeks-number="showWeeksNumber"
    :default-date="defaultDate"
    :single-month=true
    :first-day-of-week="firstDayOfWeek"
    @moveForward="currentMonthMonth => $emit('moveForward', currentMonthMonth)"
    @moveBack="currentMonthMonth => $emit('moveBack', currentMonthMonth)"
    @close="$emit('close')"
    ref="calendar"
    :showClose="showClose"
  />
</template>

<script>
import VlCalendar from './VlCalendar'
import { DAYS_SHORTCUTS } from './constants/days'

export default {
  name: 'VlDaySelector',
  model: {
    prop: 'selectedDate'
  },
  components: {
    VlCalendar
  },
  props: ['courses', 'selectedDate', 'isDisabled', 'customClasses', 'showWeeksNumber', 'defaultDate', 'singleMonth', 'firstDayOfWeek', 'disabledDates', 'showClose'],

  methods: {
    calculateDisabled (date) {
      let prevDate = new Date();
      let checkDate = new Date(date);

      prevDate.setDate(prevDate.getDate() - 1);

      return checkDate <= prevDate;
    },
    emitDate (date) {
      this.emitDesc({description:this.courses[0].description,label:this.courses[0].label})
      this.$emit('input', date)
      this.$emit('focus')
    },
    emitDesc(desc)
    {
      this.$emit('changeDesc',desc)
    }
  }
}
</script>
