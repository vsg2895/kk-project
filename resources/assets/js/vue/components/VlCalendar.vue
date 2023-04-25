<template>
  <div class="vl-calendar">
    <div v-if="showClose" class="d-flex">
      <button class="calendar-back-btn mb-2" @click="$emit('close')">
        <i class="fa fa-arrow-left"></i>
        <span>Tillbaka</span>
      </button>
    </div>

    <vl-calendar-month
      :month="currentMonthMonth"
      :year="currentMonthYear"
      :is-selected="isSelected"
      :is-disabled="isDisabled"
      :courses="courses"
      :custom-classes="customClasses"
      :show-weeks-number="showWeeksNumber"
      :first-day-of-week="firstDayOfWeek"
      @input="date => $emit('input', date)"
      @backward="moveBack"
      @forward="moveForward"
    />


  </div>
</template>

<script>
import VlCalendarMonth from './VlCalendarMonth'
import * as DatesUtils from './utils/DatesUtils'
import { DAYS_SHORTCUTS } from './constants/days'

export default {
  name: 'VlCalendar',
  components: {
    VlCalendarMonth
  },

  props: {
    isSelected: Function,
    isDisabled: Function,
    customClasses: Object,
    courses: Array,
    showWeeksNumber: Boolean,
    defaultDate: String,
    firstDayOfWeek: {
      type: String,
      validator: v =>  DAYS_SHORTCUTS.includes(v),
      default: 'mon'
    },
    showClose: { type: Boolean, defautl: false },
  },

  data () {
    const defaultDate = this.defaultDate ? DatesUtils.parseDate(this.defaultDate) : DatesUtils.getToday()
    return {
      currentMonthMonth: defaultDate.getMonth(),
      currentMonthYear: defaultDate.getFullYear()
    }
  },

  computed: {
    nextMonthMonth () {
      return this.currentMonthMonth === 11 ? 0 : this.currentMonthMonth + 1
    },

    nextMonthYear () {
      return this.currentMonthMonth === 11 ? this.currentMonthYear + 1 : this.currentMonthYear
    }
  },

  methods: {
    moveBack () {
      if (this.currentMonthMonth === 0) {
        this.currentMonthMonth = 11
        this.currentMonthYear--
      } else {
        this.currentMonthMonth--
      }

      this.$emit('moveBack', this.currentMonthMonth)
    },

    moveForward () {
      if (this.currentMonthMonth === 11) {
        this.currentMonthMonth = 0
        this.currentMonthYear++
      } else {
        this.currentMonthMonth++
      }

      this.$emit('moveForward', this.currentMonthMonth)
    }
  }
}
</script>
